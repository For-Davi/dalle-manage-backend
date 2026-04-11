<?php

namespace App\Http\Controllers;

use App\DTO\Product\FilterProductDTO;
use App\Helpers\ProductVariantHelper;
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
use App\Http\Requests\Product\Variant\CheckCodesRequest;
use App\Http\Requests\Product\Variant\DeleteProductVariantRequest;
use App\Http\Requests\Product\Variant\ShowProductVariantRequest;
use App\Http\Requests\Product\Variant\UpdateProductVariantRequest;
use App\Http\Resources\Product\ProductVariantTableResource;
use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function __construct(
        private ProductService $service,
        private ProductRepository $repository,
        private ProductVariantRepository $productVariantRepository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $productsVariants = $this->productVariantRepository->getAllByEnterprise(['product', 'images', 'color', 'suppliers', 'gridItem']);

            return response()->json(['products' => ProductVariantTableResource::collection($productsVariants)], 200);
        }, 'Erro ao buscar produtos', $request);
    }

    public function show(ShowProductRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $product = $this->repository->findById((int) $request->route('productID'), [
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
        }, 'Erro ao buscar produto', $request);
    }

    public function showVariant(ShowProductVariantRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
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
        }, 'Erro ao buscar variante', $request);
    }

    public function checkCodes(CheckCodesRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $codes = $request->input('codes', []);
            $skus = $request->input('skus', []);

            $usedCodes = ProductVariantHelper::getUsedCodes($codes);
            $usedSkus = ProductVariantHelper::getUsedSkus($skus);

            if (! empty($usedCodes) || ! empty($usedSkus)) {
                $messages = [];
                if (! empty($usedCodes)) {
                    $messages[] = 'Códigos já em uso: '.implode(', ', $usedCodes);
                }
                if (! empty($usedSkus)) {
                    $messages[] = 'SKUs já em uso: '.implode(', ', $usedSkus);
                }

                return response()->json([
                    'available' => false,
                    'message' => implode(' | ', $messages),
                    'used_codes' => $usedCodes,
                    'used_skus' => $usedSkus,
                ], 200);
            }

            return response()->json([
                'available' => true,
                'message' => 'Todos os códigos e SKUs estão disponíveis para uso.',
                'used_codes' => [],
                'used_skus' => [],
            ], 200);
        }, 'Erro ao validar códigos e SKUs das variantes', $request);
    }

    public function store(CreateProductRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.create');
            $this->service->create($request);
            $productsVariants = $this->productVariantRepository->getAllByEnterprise(['product', 'images', 'color', 'suppliers', 'gridItem.gridGroup']);

            return response()->json(['products' => ProductVariantTableResource::collection($productsVariants), 'message' => 'Produto cadastrado'], 201);
        }, 'Erro ao cadastrar produto', $request);
    }

    public function export(ExportProductRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            return $this->service->export($request);
        }, 'Erro ao exportar produtos', $request);
    }

    public function filter(FilterProductRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $productFilterDTO = FilterProductDTO::fromRequest($request);
            $productsVariants = $this->productVariantRepository->getAllWithFilter($productFilterDTO);

            return response()->json(['products' => ProductVariantTableResource::collection($productsVariants)], 200);
        }, 'Erro ao filtrar produtos', $request);
    }

    public function search(SearchProductRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $productsVariants = $this->productVariantRepository->getAllBySearch($request->value, ['product', 'color', 'gridItem.gridGroup', 'suppliers']);

            return response()->json(['products' => $productsVariants], 200);
        }, 'Erro ao buscar produtos', $request);
    }

    public function updateBasic(UpdateProductBasicRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.update');
            $product = $this->service->updateBasic($request);
            $product->load(['category', 'logs']);

            return response()->json(['basic' => $product, 'logs' => $product->logs, 'message' => 'Dados básicos de produto atualizado'], 200);
        }, 'Erro ao atualizar dados básicos de produto', $request);
    }

    public function updateAdvanced(UpdateProductAdvancedRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.update');
            $advanced = $this->service->updateAdvanced($request);
            $product = $this->repository->findById($request->productID, ['logs']);

            return response()->json(['advanced' => $advanced, 'logs' => $product->logs, 'message' => 'Configurações avançadas de produto atualizado'], 200);
        }, 'Erro ao atualizar configurações avançadas de produto', $request);
    }

    public function updateTag(UpdateProductTagRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.update');
            $this->service->updateTag($request);
            $product = $this->repository->findById($request->productID, ['tags', 'logs']);

            return response()->json(['tags' => $product->tags, 'logs' => $product->logs, 'message' => 'Tags do produto atualizada'], 200);
        }, 'Erro ao atualizar as tags do produto', $request);
    }

    public function updateMedia(UpdateProductMediaRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.update');
            $this->service->updateMedia($request);
            $product = $this->repository->findById($request->productID, ['images', 'logs']);
            $product->images->transform(function ($image) {
                $image->url = asset($image->url);

                return $image;
            });

            return response()->json(['images' => $product->images, 'logs' => $product->logs, 'message' => 'Imagens do produto atualizada'], 200);
        }, 'Erro ao atualizar as imagens do produto', $request);
    }

    public function updateVariant(UpdateProductVariantRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.update');
            $this->service->updateVariant($request);
            $productsVariants = $this->productVariantRepository->getAllByEnterprise(['product', 'images', 'color']);

            return response()->json(['products' => ProductVariantTableResource::collection($productsVariants), 'message' => 'Produto atualizado'], 200);
        }, 'Erro ao atualizar produto', $request);
    }

    public function destroy(DeleteProductRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.delete');
            $this->repository->delete($request->route('productID'));
            $productsVariants = $this->productVariantRepository->getAllByEnterprise(['product', 'images', 'color', 'suppliers', 'gridItem.gridGroup']);

            return response()->json(['products' => ProductVariantTableResource::collection($productsVariants), 'message' => 'Produto excluído'], 200);
        }, 'Erro ao excluir produto', $request);
    }

    public function destroyVariant(DeleteProductVariantRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.delete');
            $this->productVariantRepository->delete($request->route('variantID'));
            $productsVariants = $this->productVariantRepository->getAllByEnterprise(['product', 'images', 'color', 'suppliers', 'gridItem.gridGroup']);

            return response()->json(['products' => ProductVariantTableResource::collection($productsVariants), 'message' => 'Variante excluída'], 200);
        }, 'Erro ao excluir variante', $request);
    }
}
