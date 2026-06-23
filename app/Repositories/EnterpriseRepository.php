<?php

namespace App\Repositories;

use App\Models\Enterprise;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnterpriseRepository extends BaseRepository
{
    public function __construct(Enterprise $model)
    {
        parent::__construct($model);
    }

    public function findEnterpriseWithId(int $id)
    {
        return $this->model->where('id', $id);
    }

    public function updateWithoutCache(int $id, array $data)
    {
        $enterprise = $this->model->where('id', $id)->first();

        if ($enterprise) {
            return $enterprise->update($data);
        }

        return null;
    }

    public function findMyEnterprise(): ?Enterprise
    {
        $query = $this->model->query();

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->where('id', Auth::user()->enterprise_id)->first();
    }

    public function delete()
    {
        $id = Auth::user()->enterprise_id;
        $enterprise = $this->findById($id);
        if ($enterprise) {
            // DELIVERY GUYS
            $deliveryGuyIds = DB::table('delivery_guys')
                ->where('enterprise_id', $id)
                ->pluck('id');

            if ($deliveryGuyIds->isNotEmpty()) {
                DB::table('sale_deliveries')
                    ->whereIn('delivery_guy_id', $deliveryGuyIds)
                    ->update(['delivery_guy_id' => null]);
            }

            DB::table('delivery_guys')->where('enterprise_id', $id)->delete();

            // CLIENTS
            $clientsIds = DB::table('clients')
                ->where('enterprise_id', $id)
                ->pluck('id');

            if ($clientsIds->isNotEmpty()) {
                DB::table('sales')
                    ->whereIn('client_id', $clientsIds)
                    ->update(['client_id' => null]);
            }

            DB::table('clients')->where('enterprise_id', $id)->delete();

            // DEPARTMENTS
            DB::table('users')->where('enterprise_id', $id)
                ->whereNotNull('department_id')
                ->update(['department_id' => null]);
            DB::table('employees')->where('enterprise_id', $id)
                ->whereNotNull('department_id')
                ->update(['department_id' => null]);
            DB::table('departments')->where('enterprise_id', $id)->delete();

            // STOCK REENTRIES RETURN PRODUCTS
            DB::table('stock_reentries_return_products')->where('enterprise_id', $id)->delete();

            // PRODUCTS ADVANCED
            DB::table('product_advanced')
                ->whereIn('product_id', function ($query) use ($id) {
                    $query->select('id')
                        ->from('products')
                        ->where('enterprise_id', $id);
                })->delete();

            // PRODUCT LOG
            DB::table('product_log')
                ->whereIn('product_id', function ($query) use ($id) {
                    $query->select('id')
                        ->from('products')
                        ->where('enterprise_id', $id);
                })->delete();

            // PRODUCT IMAGE
            DB::table('product_image')
                ->whereIn('product_id', function ($query) use ($id) {
                    $query->select('id')
                        ->from('products')
                        ->where('enterprise_id', $id);
                })->delete();

            // PRODUCT TAG
            DB::table('product_tag')
                ->whereIn('product_id', function ($query) use ($id) {
                    $query->select('id')
                        ->from('products')
                        ->where('enterprise_id', $id);
                })->delete();

            // PRODUCT MOVEMENTS
            DB::table('product_movements')->where('enterprise_id', $id)->delete();

            // PEGAR OS IDS DE SALES
            $salesIds = DB::table('sales')
                ->where('enterprise_id', $id)
                ->pluck('id');

            // RETURNS
            $returnIds = DB::table('returns')
                ->whereIn('sale_id', $salesIds)
                ->pluck('id');

            if ($returnIds->isNotEmpty()) {
                DB::table('return_items')->whereIn('return_id', $returnIds)->delete();
                DB::table('return_exchange_items')->whereIn('return_id', $returnIds)->delete();
                DB::table('exchange_payments_methods')->whereIn('return_id', $returnIds)->delete();
                DB::table('returns')
                    ->whereIn('linked_return_id', $returnIds)
                    ->update(['linked_return_id' => null]);

                // COMMISSIONS
                DB::table('commissions')->whereIn('return_id', $returnIds)->delete();
            }

            DB::table('returns')->whereIn('sale_id', $salesIds)->delete();

            // SALES
            if ($salesIds->isNotEmpty()) {
                DB::table('sale_deliveries')->whereIn('sale_id', $salesIds)->delete();
                DB::table('sale_cancellations')->whereIn('sale_id', $salesIds)->delete();
                DB::table('sale_payments_methods')->whereIn('sale_id', $salesIds)->delete();
                DB::table('sale_itens')->whereIn('sale_id', $salesIds)->delete();

                // COMISSIONS
                DB::table('commissions')->whereIn('sale_id', $salesIds)->delete();
            }

            DB::table('sales')->whereIn('id', $salesIds)->delete();

            // EMPLOYEES
            DB::table('employees')->where('enterprise_id', $id)->delete();

            // SUPPLIER ORDER
            $supplierOrdersIds = DB::table('supplier_orders')
                ->where('enterprise_id', $id)
                ->pluck('id');

            if ($supplierOrdersIds->isNotEmpty()) {
                DB::table('supplier_order_status_history')->whereIn('supplier_order_id', $supplierOrdersIds)->delete();

                $supplierOrdersItemsIds = DB::table('supplier_order_items')
                    ->whereIn('supplier_order_id', $supplierOrdersIds)
                    ->pluck('id');

                if ($supplierOrdersItemsIds->isNotEmpty()) {
                    DB::table('supplier_order_receivings')->whereIn('supplier_order_item_id', $supplierOrdersItemsIds)->delete();
                }

                DB::table('supplier_order_items')->whereIn('supplier_order_id', $supplierOrdersIds)->delete();
            }

            DB::table('supplier_orders')->whereIn('id', $supplierOrdersIds)->delete();

            // PRODUCT VARIANTS
            DB::table('supplier_catalog')->where('enterprise_id', $id)
                ->whereNotNull('product_variant_id')
                ->update(['product_variant_id' => null]);
            DB::table('product_variants')->where('enterprise_id', $id)->delete();

            // PRODUCT COLORS
            DB::table('product_colors')->where('enterprise_id', $id)->delete();

            // PRODUCT CATEGORIES
            DB::table('products')->where('enterprise_id', $id)
                ->whereNotNull('product_category_id')
                ->update(['product_category_id' => null]);
            DB::table('product_categories')->where('enterprise_id', $id)->delete();

            // PRODUCTS
            DB::table('products')->where('enterprise_id', $id)->delete();

            // GRID ITEMS
            DB::table('grid_items')->where('enterprise_id', $id)->delete();

            // GRID GROUPS
            DB::table('grid_groups')->where('enterprise_id', $id)->delete();

            // IMAGES
            DB::table('feedbacks')
                ->whereIn('image_id', function ($query) use ($id) {
                    $query->select('id')
                        ->from('images')
                        ->where('enterprise_id', $id);
                })->delete();

            DB::table('users')
                ->whereIn('image_id', function ($query) use ($id) {
                    $query->select('id')
                        ->from('images')
                        ->where('enterprise_id', $id);
                })
                ->update(['image_id' => null]);

            DB::table('images')->where('enterprise_id', $id)->delete();

            // MOVEMENTS
            DB::table('movements')->where('enterprise_id', $id)->delete();

            // NOTIFICATIONS
            DB::table('notifications')->where('enterprise_id', $id)->delete();

            // RECEIPTS
            DB::table('receipts')->where('enterprise_id', $id)->delete();

            // PERMISSION ROLE
            $roleIds = DB::table('roles')->where('enterprise_id', $id)->pluck('id');

            if ($roleIds->isNotEmpty()) {
                DB::table('permission_role')->whereIn('role_id', $roleIds)->delete();
            }

            // ROLES
            DB::table('users')->where('enterprise_id', $id)
                ->whereNotNull('role_id')
                ->update(['role_id' => null]);
            DB::table('roles')->where('enterprise_id', $id)->delete();

            // SCHEDULES
            DB::table('schedules')->where('enterprise_id', $id)->delete();

            // SETTING APPEARANCE
            DB::table('setting_appearance')->where('enterprise_id', $id)->delete();

            // SETTING SYSTEM
            DB::table('setting_system')->where('enterprise_id', $id)->delete();

            // SUPPLIER CATALOG
            DB::table('supplier_catalog')->where('enterprise_id', $id)->delete();

            // SUPPLIER CATEGORIES
            DB::table('suppliers')->where('enterprise_id', $id)
                ->whereNotNull('supplier_category_id')
                ->update(['supplier_category_id' => null]);
            DB::table('supplier_categories')->where('enterprise_id', $id)->delete();

            // SUPPLIERS
            DB::table('suppliers')->where('enterprise_id', $id)->delete();

            // TAGS
            DB::table('tags')->where('enterprise_id', $id)->delete();

            // TRANSACTION CATEGORIES
            DB::table('transaction_categories')->where('enterprise_id', $id)->delete();

            // TYPES RECEIPT
            DB::table('types_receipt')->where('enterprise_id', $id)->delete();

            // USERS
            DB::table('users')->where('enterprise_id', $id)->delete();

            return $enterprise->delete();
        }

        return false;
    }
}
