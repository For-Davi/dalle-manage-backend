<?php

namespace App\Repositories;

use App\Models\Enterprise;
use Illuminate\Support\Facades\DB;

class EnterpriseRepository
{
    public function __construct(protected Enterprise $model) {}

    public function getAll()
    {
        return $this->model->all();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByCpf($cpf)
    {
        return $this->model->where('cpf', $cpf)->first();
    }

    public function findByCnpj($cnpj)
    {
        return $this->model->where('cnpj', $cnpj)->first();
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $enterprise = $this->findById($id);
        if ($enterprise) {
            $enterprise->update($data);

            return $enterprise;
        }

        return null;
    }

    public function delete($id)
    {
        $enterprise = $this->findById($id);
        if ($enterprise) {
            // CLIENTS
            DB::table('clients')->where('enterprise_id', $id)->delete();

            // DEPARTMENTS
            DB::table('users')->where('enterprise_id', $id)
                ->whereNotNull('department_id')
                ->update(['department_id' => null]);
            DB::table('employees')->where('enterprise_id', $id)
                ->whereNotNull('department_id')
                ->update(['department_id' => null]);
            DB::table('departments')->where('enterprise_id', $id)->delete();

            // EMPLOYEES
            DB::table('employees')->where('enterprise_id', $id)->delete();

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
            DB::table('images')->where('enterprise_id', $id)->delete();

            // MOVEMENTS
            DB::table('movements')->where('enterprise_id', $id)->delete();

            // NOTIFICATIONS
            DB::table('notifications')->where('enterprise_id', $id)->delete();

            // RECEIPTS
            DB::table('receipts')->where('enterprise_id', $id)->delete();

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
