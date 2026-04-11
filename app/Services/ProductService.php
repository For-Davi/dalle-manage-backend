<?php

namespace App\Services;

use App\DTO\Image\CreateImageDTO;
use App\DTO\Product\CreateProductDTO;
use App\DTO\Product\FilterProductDTO;
use App\DTO\Product\ProductAdvanced\CreateProductAdvancedDTO;
use App\DTO\Product\ProductAdvanced\UpdateProductAdvancedDTO;
use App\DTO\Product\ProductImage\CreateProductImageDTO;
use App\DTO\Product\ProductTag\CreateProductTagDTO;
use App\DTO\Product\ProductVariant\CreateProductVariantDTO;
use App\DTO\Product\ProductVariant\UpdateProductVariantDTO;
use App\DTO\Product\UpdateProductBasicDTO;
use App\Exports\Product\ProductExport;
use App\Helpers\ProductHelper;
use App\Helpers\ProductLogHelper;
use App\Helpers\SkuHelper;
use App\Repositories\ImageRepository;
use App\Repositories\ProductAdvancedRepository;
use App\Repositories\ProductColorRepository;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductTagRepository;
use App\Repositories\ProductVariantRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(protected ProductRepository $repository, protected ProductTagRepository $productTagRepository, protected ProductAdvancedRepository $productAdvancedRepository, protected ImageRepository $imageRepository, protected ProductImageRepository $productImageRepository, protected ProductVariantRepository $productVariantRepository, protected ProductColorRepository $productColorRepository) {}

    public function create($request)
    {
        ProductHelper::existsProduct(
            $request->input('basic.name'),
            'create'
        );

        $productDTO = CreateProductDTO::fromRequest($request);

        // Cria o produto
        $product = $this->repository->create($productDTO->toArray());

        // Cria as configurações avançadas
        $this->createAdvancedForProduct($request->advanced, $product->id);

        // Cria as variantes
        $this->createVariantForProduct($request->variants, $product->id);

        // Salva as imagens
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $this->savePathImage($image);

                $imageDTO = CreateImageDTO::fromRequest([
                    'url' => $path,
                    'name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                ]);

                $imageSaved = $this->imageRepository->create($imageDTO->toArray());

                $productImageDTO = CreateProductImageDTO::fromRequest([
                    'imageID' => $imageSaved->id,
                    'productID' => $product->id,
                ]);

                $this->productImageRepository->create($productImageDTO->toArray());
            }
        }

        // Analisa e cria a relação com as tags
        if ($request->tags && count($request->tags) > 0) {
            foreach ($request->tags as $tag) {
                $this->createTagForProduct($tag['id'], $product->id);
            }
        }

        // Criação de log do produto
        $user = $request->user();
        ProductLogHelper::createLog(
            $product->id,
            'create',
            "O usuário(a) {$user->name} ({$user->email}) criou este produto em ".
            Carbon::now()->locale('pt_BR')->translatedFormat('d/m/Y H:i:s')
        );

        return true;
    }

    private function createTagForProduct(int $tagID, int $productID)
    {
        $productTagDTO = CreateProductTagDTO::fromRequest([
            'tagID' => $tagID,
            'productID' => $productID,
        ]);

        $this->productTagRepository->create($productTagDTO->toArray());
    }

    private function createAdvancedForProduct(array $advanced, int $productID)
    {
        $productAdvancedDTO = CreateProductAdvancedDTO::fromRequest([
            'active' => $advanced['active'],
            'allowCoupon' => $advanced['allowCoupon'],
            'allowDiscount' => $advanced['allowDiscount'],
            'discountMaxPercentage' => $advanced['discountMaxPercentage'],
            'hasCommission' => $advanced['hasCommission'],
            'commissionPercentage' => $advanced['commissionPercentage'],
            'productID' => $productID,
        ]);

        $this->productAdvancedRepository->create($productAdvancedDTO->toArray());
    }

    private function createVariantForProduct(array $variants, int $productID): void
    {
        foreach ($variants as $variant) {
            if (count($variant['colors']) === 0) {
                $this->createBaseVariant($variant, $productID);
            } else {
                $this->createVariantWithColors($variant, $productID);
            }
        }
    }

    private function createVariantWithColors(array $variant, int $productID): void
    {
        foreach ($variant['colors'] as $color) {
            $this->productVariantRepository->create(
                $this->buildVariantDTO($variant, $productID, $color)->toArray()
            );
        }
    }

    private function createBaseVariant(array $variant, int $productID): void
    {
        $this->productVariantRepository->create(
            $this->buildVariantDTO($variant, $productID)->toArray()
        );
    }

    private function buildVariantDTO(array $variant, int $productID, ?array $color = null): CreateProductVariantDTO
    {
        if ($color && ! empty($color['sku'])) {
            SkuHelper::existsSKU($color['sku'], 'create');
        } elseif (! empty($variant['sku'])) {
            SkuHelper::existsSKU($variant['sku'], 'create');
        }

        $stockQuantity = $color ? ($color['stockQuantity'] ?? 0) : ($variant['stockQuantity'] ?? 0);
        $minStockAlert = $color ? ($color['minStockAlert'] ?? 0) : ($variant['minStockAlert'] ?? 0);
        $code = $color ? ($color['code'] ?? null) : ($variant['code'] ?? null);
        $sku = $color ? ($color['sku'] ?? null) : ($variant['sku'] ?? null);
        $colorID = $color ? ($color['id'] ?? null) : null;

        return CreateProductVariantDTO::fromRequest([
            'active' => $variant['active'] ?? true,
            'sku' => $sku,
            'code' => $code,
            'description' => $variant['description'] ?? null,
            'offer' => $variant['offer'] ?? false,
            'location' => $variant['location'] ?? null,
            'gridItemID' => $variant['gridItemID'] ?? null,
            'colorID' => $colorID,
            'price' => $variant['price'] ?? 0,
            'cost' => $variant['cost'] ?? 0,
            'stockQuantity' => $stockQuantity,
            'minStockAlert' => $minStockAlert,
            'productID' => $productID,
        ]);
    }

    private function savePathImage($image)
    {
        if (! Storage::disk('public')->exists('images')) {
            Storage::disk('public')->makeDirectory('images');
        }

        $path = $image->store('images', 'public');

        return Storage::url($path);
    }

    public function updateVariant($request)
    {
        if (! empty($request->sku)) {
            SkuHelper::existsSKU($request->sku, 'update', $request->id);
        }

        $productVariantDTO = UpdateProductVariantDTO::fromRequest($request);

        return $this->productVariantRepository->update($request->id, $productVariantDTO->toArray());
    }

    public function updateBasic($request)
    {
        ProductHelper::existsProduct(
            $request->input('name'),
            'update',
            $request->input('productID')
        );

        $productBasicDTO = UpdateProductBasicDTO::fromRequest($request);

        $result = $this->repository->update($request->productID, $productBasicDTO->toArray());

        $user = $request->user();
        ProductLogHelper::createLog(
            $request->productID,
            'update',
            "O usuário(a) {$user->name} ({$user->email}) atualizou os dados básicos deste produto em ".
            Carbon::now()->locale('pt_BR')->translatedFormat('d/m/Y H:i:s')
        );

        return $result;
    }

    public function updateTag($request)
    {
        $this->productTagRepository->deleteByProductID($request->productID);

        if ($request->tags && count($request->tags) > 0) {
            foreach ($request->tags as $tag) {
                $this->createTagForProduct($tag['id'], $request->productID);
            }
        }

        $user = $request->user();
        ProductLogHelper::createLog(
            $request->productID,
            'update',
            "O usuário(a) {$user->name} ({$user->email}) atualizou as tags deste produto em ".
            Carbon::now()->locale('pt_BR')->translatedFormat('d/m/Y H:i:s')
        );
    }

    public function updateAdvanced($request)
    {
        $productAdvancedDTO = UpdateProductAdvancedDTO::fromRequest($request);

        $result = $this->productAdvancedRepository->update($request->productID, $productAdvancedDTO->toArray());

        $user = $request->user();
        ProductLogHelper::createLog(
            $request->productID,
            'update',
            "O usuário(a) {$user->name} ({$user->email}) atualizou os dados avançados deste produto em ".
            Carbon::now()->locale('pt_BR')->translatedFormat('d/m/Y H:i:s')
        );

        return $result;
    }

    public function updateMedia($request)
    {
        $this->deleteImages($request);
        $this->saveNewImages($request);

        $user = $request->user();
        ProductLogHelper::createLog(
            $request->productID,
            'update',
            "O usuário(a) {$user->name} ({$user->email}) atualizou as imagens deste produto em ".
            Carbon::now()->locale('pt_BR')->translatedFormat('d/m/Y H:i:s')
        );
    }

    private function deleteImages($request)
    {
        $imagesToDelete = collect($request->input('imagesToDelete', []))
            ->pluck('id')
            ->filter()
            ->all();

        if (empty($imagesToDelete)) {
            return;
        }

        $imageRecords = DB::table('product_image')
            ->whereIn('image_id', $imagesToDelete)
            ->join('images', 'product_image.image_id', '=', 'images.id')
            ->select('images.id', 'images.url')
            ->get();

        if ($imageRecords->isEmpty()) {
            return;
        }

        DB::table('product_image')->whereIn('image_id', $imagesToDelete)->delete();

        if (app()->environment('local')) {
            foreach ($imageRecords as $image) {
                $filePath = public_path($image->url);
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }
        }

        DB::table('images')->whereIn('id', $imagesToDelete)->delete();
    }

    private function saveNewImages($request)
    {
        if (! $request->hasFile('newImages')) {
            return;
        }

        foreach ($request->file('newImages') as $image) {
            $path = $this->savePathImage($image);

            $imageDTO = CreateImageDTO::fromRequest([
                'url' => $path,
                'name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
            ]);

            $imageSaved = $this->imageRepository->create($imageDTO->toArray());

            $productImageDTO = CreateProductImageDTO::fromRequest([
                'imageID' => $imageSaved->id,
                'productID' => $request->productID,
            ]);

            $this->productImageRepository->create($productImageDTO->toArray());
        }
    }

    public function export($request)
    {
        $dateTime = now()->format('Ymd_His');

        $exportProductDTO = FilterProductDTO::fromRequest([
            ...$request->only(['name', 'sku', 'category', 'active', 'stockCritical']),
        ]);

        $products = $this->productVariantRepository->getAllWithFilter($exportProductDTO);

        $fileName = "products_{$dateTime}.xlsx";

        return (new ProductExport($products))->download($fileName);
    }
}
