<?php

namespace App\Http\Controllers;

use App\DTO\Product\FilterProductDTO;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\DeleteProductRequest;
use App\Http\Requests\Product\ExportProductRequest;
use App\Http\Requests\Product\FilterProductRequest;
use App\Http\Requests\Product\SearchProductRequest;
use App\Http\Requests\Product\ShowProductRequest;
use App\Http\Requests\Product\UpdateProductAdvancedRequest;
use App\Http\Requests\Product\UpdateProductBasicRequest;
use App\Http\Requests\Product\UpdateProductMediaRequest;
use App\Http\Requests\Product\UpdateProductTagRequest;
use App\Http\Requests\Product\Variant\DeleteProductVariantRequest;
use App\Http\Requests\Product\Variant\ShowProductVariantRequest;
use App\Http\Requests\Product\Variant\UpdateProductVariantRequest;
use App\Http\Resources\Product\ProductVariantTableResource;
use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use App\Services\ProductService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController
{
    public function __construct(
        private ProductService $service,
        private ProductRepository $repository,
        private ProductVariantRepository $productVariantRepository
    ) {}

    public function index(Request $request)
    {
        try {
            $productsVariants = $this->productVariantRepository->getAllByEnterprise($request->get('enterprise_id'), ['product', 'images', 'color', 'suppliers']);

            return response()->json(['products' => ProductVariantTableResource::collection($productsVariants)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar produtos:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar produtos'], 500);
        }
    }

    public function show(ShowProductRequest $request)
    {
        try {
            $product = $this->repository->findById($request->route('productID'), [
                'variants.gridItem.gridGroup',
                'variants.color',
                'tags',
                'logs',
                'advanced',
                'images',
                'category',
            ]);

            $product->images->transform(function ($image) {
                $image->url = asset($image->url);

                return $image;
            });

            return response()->json(['product' => $product]);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar produto:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar produto'], 500);
        }
    }

    public function showVariant(ShowProductVariantRequest $request)
    {
        try {
            $variant = $this->productVariantRepository->findById($request->route('variantID'));

            $variant->load([
                'product' => function ($query) {
                    $query->select(['id', 'name', 'type', 'product_category_id']);
                },
                'color' => function ($query) {
                    $query->select(['id', 'name', 'hex_color_code']);
                },
            ]);

            return response()->json(['variant' => $variant]);
        } catch (\Exception $e) {

            ErrorLogger::log('Erro ao buscar variante:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar variante'], 500);
        }
    }

    public function store(CreateProductRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = $this->service->create($request);
            if ($product) {
                DB::commit();
                $productsVariants = $this->productVariantRepository->getAllByEnterprise($request->get('enterprise_id'), ['product', 'images', 'color']);

                return response()->json(['products' => ProductVariantTableResource::collection($productsVariants), 'message' => 'Produto cadastrado'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar produto:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar produto'], 500);
        }
    }

    public function export(ExportProductRequest $request)
    {
        try {
            return $this->service->export($request);
        } catch (\Exception $e) {

            ErrorLogger::log('Erro ao exportar produtos:', $e, $request);

            return response()->json(['message' => 'Erro ao exportar produtos'], 500);
        }
    }

    public function filter(FilterProductRequest $request)
    {
        try {
            $productFilterDTO = FilterProductDTO::fromRequest([
                ...$request->only(['name', 'active', 'stockCritical', 'sku', 'category']),
                'enterpriseID' => $request->get('enterprise_id'),
            ]);
            $productsVariants = $this->productVariantRepository->getAllWithFilter($productFilterDTO);

            return response()->json(['products' => ProductVariantTableResource::collection($productsVariants)], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao filtrar produtos:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function search(SearchProductRequest $request)
    {
        try {
            $productsVariants = $this->productVariantRepository->getAllBySearch($request->get('enterprise_id'), $request->value, ['product', 'color', 'gridItem.gridGroup']);

            return response()->json(['products' => $productsVariants], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar produtos:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateBasic(UpdateProductBasicRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = $this->service->updateBasic($request);

            if ($product) {
                DB::commit();

                $product->load(['category', 'logs']);

                return response()->json(['basic' => $product, 'logs' => $product->logs, 'message' => 'Dados básicos de produto atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar dados básicos de produto:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar dados básicos de produto'], 500);
        }
    }

    public function updateAdvanced(UpdateProductAdvancedRequest $request)
    {
        try {
            DB::beginTransaction();
            $advanced = $this->service->updateAdvanced($request);

            if ($advanced) {
                DB::commit();

                $product = $this->repository->findById($request->productID, [
                    'logs',
                ]);

                return response()->json(['advanced' => $advanced, 'logs' => $product->logs, 'message' => 'Configurações avançadas de produto atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar configurações avançadas de produto:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar configurações avançadas de produto'], 500);
        }
    }

    public function updateTag(UpdateProductTagRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->service->updateTag($request);

            DB::commit();

            $product = $this->repository->findById($request->productID, [
                'tags', 'logs',
            ]);

            return response()->json(['tags' => $product->tags, 'logs' => $product->logs,  'message' => 'Tags do produto atualizada'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar as tags do produto:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar as tags do produto'], 500);
        }
    }

    public function updateMedia(UpdateProductMediaRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->service->updateMedia($request);

            DB::commit();

            $product = $this->repository->findById($request->productID, [
                'images', 'logs',
            ]);

            $product->images->transform(function ($image) {
                $image->url = asset($image->url);

                return $image;
            });

            return response()->json(['images' => $product->images, 'logs' => $product->logs,  'message' => 'Imagens do produto atualizada'], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar as imagens do produto:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar as imagens do produto'], 500);
        }
    }

    public function updateVariant(UpdateProductVariantRequest $request)
    {
        try {
            DB::beginTransaction();
            $variant = $this->service->updateVariant($request);

            if ($variant) {
                DB::commit();

                $productsVariants = $this->productVariantRepository->getAllByEnterprise($request->get('enterprise_id'), ['product', 'images', 'color']);

                return response()->json(['products' => ProductVariantTableResource::collection($productsVariants), 'message' => 'Produto atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar prodputo:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar produto'], 500);
        }
    }

    public function destroy(DeleteProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $product = $this->repository->delete($request->route('productID'));

            if ($product) {
                DB::commit();
                $productsVariants = $this->productVariantRepository->getAllByEnterprise($request->get('enterprise_id'), ['product', 'images', 'color']);

                return response()->json(['products' => ProductVariantTableResource::collection($productsVariants), 'message' => 'Produto excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir produto:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir produto'], 500);
        }
    }

    public function destroyVariant(DeleteProductVariantRequest $request)
    {
        try {
            DB::beginTransaction();

            $variant = $this->productVariantRepository->delete($request->route('variantID'));

            if ($variant) {
                DB::commit();
                $productsVariants = $this->productVariantRepository->getAllByEnterprise($request->get('enterprise_id'), ['product', 'images', 'color']);

                return response()->json(['products' => ProductVariantTableResource::collection($productsVariants), 'message' => 'Variante excluída'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir variante:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir variante'], 500);
        }
    }
}
