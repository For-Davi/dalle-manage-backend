<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Enterprise;
use App\Models\GridGroup;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\Role;
use App\Models\SettingAppearance;
use App\Models\SettingSystem;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Models\Tag;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class StartDatabaseSeeder extends Seeder
{
    public function run()
    {
        Enterprise::factory()
            ->count(10)
            ->create()
            ->each(function ($enterprise) {

                // ------------------------------------------------------------
                SettingSystem::factory()->create([
                    'enterprise_id' => $enterprise->id,
                ]);

                SettingAppearance::factory()->create([
                    'enterprise_id' => $enterprise->id,
                ]);

                ProductColor::factory()->count(7)->create([
                    'enterprise_id' => $enterprise->id,
                ]);

                $departments = Department::factory()->count(5)->create([
                    'enterprise_id' => $enterprise->id,
                ]);

                $roles = Role::factory()->count(1)->create([
                    'enterprise_id' => $enterprise->id,
                ]);

                Employee::factory()
                    ->count(25)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                    ]);

                User::factory()
                    ->count(2)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                        'role_id' => $roles->random()->id,
                        'department_id' => $departments->random()->id,
                    ]);

                Tag::factory()->count(3)->create([
                    'enterprise_id' => $enterprise->id,
                ]);

                // ------------------------------------------------------------
                $transactionCategories = [
                    'Aluguel',
                    'Energia',
                    'Água',
                    'Internet',
                    'Combustível',
                    'Gás',
                    'Manutenção',
                    'Limpeza',
                    'Material de Escritório',
                    'Construção',
                    'Marketing',
                    'Transporte',
                    'Impostos',
                    'Segurança',
                    'Salários',
                    'Licenças de Software',
                    'Telefone',
                    'Compras',
                ];
                foreach ($transactionCategories as $transactionCategory) {
                    TransactionCategory::factory()->create([
                        'name' => $transactionCategory,
                        'enterprise_id' => $enterprise->id,
                    ]);
                }

                // ------------------------------------------------------------
                $supplierCategories = SupplierCategory::factory()
                    ->count(8)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                    ]);
                // ------------------------------------------------------------

                Supplier::factory()
                    ->count(30)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                        'supplier_category_id' => $supplierCategories->random()->id,
                    ]);

                $gridGroups = ['LETRAS', 'NÚMEROS', 'TAMANHO ÚNICO'];
                foreach ($gridGroups as $groupName) {
                    GridGroup::factory()->create([
                        'name' => $groupName,
                        'enterprise_id' => $enterprise->id,
                    ]);
                }

                // ------------------------------------------------------------
                $productCategories = ['Blusa', 'Bermuda', 'Calçado'];
                foreach ($productCategories as $productCategory) {
                    ProductCategory::factory()->create([
                        'name' => $productCategory,
                        'enterprise_id' => $enterprise->id,
                    ]);
                }

                Client::factory()->count(50)->create([
                    'enterprise_id' => $enterprise->id,
                ]);
            });
    }
}
