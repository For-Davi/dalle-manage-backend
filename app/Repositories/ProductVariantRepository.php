<?php

namespace App\Repositories;

use App\DTO\Product\FilterProductDTO;
use App\Models\ProductVariant;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProductVariantRepository extends BaseRepository
{
    protected StockReentryReturnItemRepository $stockReentryReturnItemRepository;

    public function __construct(ProductVariant $model, StockReentryReturnItemRepository $stockReentryReturnItemRepository)
    {
        parent::__construct($model);

        $this->stockReentryReturnItemRepository = $stockReentryReturnItemRepository;
    }

    public function findStockReentryReturnItemById($id)
    {
        $query = $this->stockReentryReturnItemRepository->getAllByEnterprise();

        return $query->where('product_variant_id', $id)->first();
    }

    public function getAllBySearch($value, $relations = null)
    {
        $query = $this->model
            ->leftJoin('products', 'products.id', '=', 'product_variants.product_id');

        if ($relations) {
            $query->with($relations);
        }

        $value = trim($value);
        $lowerValue = mb_strtolower($value);

        $query->where(function ($q) use ($lowerValue) {
            $q->whereRaw('LOWER(product_variants.sku) LIKE ?', ["%{$lowerValue}%"])
                ->orWhereRaw('LOWER(products.name) LIKE ?', ["%{$lowerValue}%"])
                ->orWhereRaw('LOWER(product_variants.code) LIKE ?', ["%{$lowerValue}%"]);
        });

        return $query->select('product_variants.*')->get();
    }

    public function getAllWithFilter(FilterProductDTO $filters)
    {
        $query = $this->model->where('enterprise_id', $filters->enterpriseID)->with([
            'product', 'images', 'color', 'suppliers', 'gridItem.gridGroup',
        ]);

        if ($filters->name !== null) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters->name}%");
            });
        }

        if ($filters->sku !== null) {
            $query->where('sku', 'like', "%{$filters->sku}%");
        }

        if ($filters->categoryID !== null) {
            $query->where('product_category_id', $filters->categoryID);
        }

        if ($filters->stockCritical !== null) {
            $operator = ((int) $filters->stockCritical === 1) ? '<=' : '>';
            $query->whereRaw("
                CAST(REPLACE(stock_quantity, '.', '') AS SIGNED) {$operator}
                CAST(REPLACE(min_stock_alert, '.', '') AS SIGNED)
            ");
        }

        if ($filters->active !== null) {
            $query->where('active', $filters->active);
        }

        return $query->get();
    }

    public function changeStockQuantity(int $variantID, string $type, float $quantity)
    {
        $variant = $this->findById($variantID);
        if ($variant) {
            $currentStock = (float) $variant->stock_quantity;

            $newStock = $type === 'in'
                ? $currentStock + $quantity
                : $currentStock - $quantity;

            $variant->update([
                'stock_quantity' => $newStock,
            ]);
        }
    }

    public function changeStockReentryReturnItemQuantity(int $variantID, string $type, int $quantity)
    {
        $returnItem = $this->findStockReentryReturnItemById($variantID);
        $variant = $this->findById($variantID);
        if ($returnItem && $variant) {
            $currentQuantity = (int) $returnItem->quantity;
            $currentStock = (int) $variant->stock_quantity;

            $newQuantity = $currentQuantity - $quantity;

            if ($type === 'in') {
                $newStock = $currentStock + $quantity;

                $variant->update([
                    'stock_quantity' => $newStock,
                ]);
            }

            if ($newQuantity === 0) {
                return $returnItem->delete();
            }

            return $this->stockReentryReturnItemRepository->updateStockReentryReturnItem(
                $variantID, [
                    'quantity' => $newQuantity,
                ]);
        }

        return null;
    }

    public function delete($id)
    {
        $variant = $this->findById($id);

        if (! $variant) {
            return false;
        }

        return DB::transaction(function () use ($variant) {
            $total = DB::table('product_variants')
                ->where('enterprise_id', $variant->enterprise_id)
                ->where('product_id', $variant->product_id)
                ->count();

            if ($total >= 2) {
                return $variant->delete();
            }

            DB::table('product_log')->where('product_id', $variant->product_id)->delete();
            DB::table('product_tag')->where('product_id', $variant->product_id)->delete();
            DB::table('product_image')->where('product_id', $variant->product_id)->delete();
            DB::table('product_advanced')->where('product_id', $variant->product_id)->delete();
            DB::table('product_variants')->where('product_id', $variant->product_id)->delete();
            DB::table('products')->where('id', $variant->product_id)->delete();

            return true;
        });
    }
}
