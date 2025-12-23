<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Enterprise;
use App\Models\GridGroup;
use App\Models\Movement;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\SettingAppearance;
use App\Models\SettingSystem;
use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Models\Tag;
use App\Models\TransactionCategory;
use App\Models\TypeReceipt;
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

                // ------------------------------------------------------------
                SettingAppearance::factory()->create([
                    'enterprise_id' => $enterprise->id,
                ]);

                // ------------------------------------------------------------
                $colors = [
                    ['name' => 'VERMELHO', 'hex_color_code' => '#FF0000'],
                    ['name' => 'AZUL', 'hex_color_code' => '#0000FF'],
                    ['name' => 'VERDE', 'hex_color_code' => '#00FF00'],
                    ['name' => 'AMARELO', 'hex_color_code' => '#FFFF00'],
                    ['name' => 'PRETO', 'hex_color_code' => '#000000'],
                    ['name' => 'BRANCO', 'hex_color_code' => '#FFFFFF'],
                    ['name' => 'ROXO', 'hex_color_code' => '#800080'],
                    ['name' => 'LARANJA', 'hex_color_code' => '#FFA500'],
                    ['name' => 'CINZA', 'hex_color_code' => '#808080'],
                    ['name' => 'ROSA', 'hex_color_code' => '#FFC0CB'],
                ];
                foreach ($colors as $color) {
                    ProductColor::create([
                        'enterprise_id' => $enterprise->id,
                        'name' => $color['name'],
                        'hex_color_code' => $color['hex_color_code'],
                    ]);
                }

                // ------------------------------------------------------------
                $departmentsData = [
                    'FINANCEIRO',
                    'RECURSOS HUMANOS',
                    'COMERCIAL',
                    'LOGÍSTICA',
                    'MARKETING',
                    'TI',
                    'ATENDIMENTO',
                    'ALMOXARIFADO',
                ];
                $departments = collect($departmentsData)->map(function ($name) use ($enterprise) {
                    return Department::create([
                        'enterprise_id' => $enterprise->id,
                        'name' => $name,
                    ]);
                });

                // ------------------------------------------------------------
                $rolesData = [
                    [
                        'name' => 'Master',
                        'permissions' => [],
                    ],
                ];
                $roles = collect($rolesData)->map(function ($role) use ($enterprise) {
                    return Role::create([
                        'enterprise_id' => $enterprise->id,
                        'name' => $role['name'],
                        'permissions' => $role['permissions'],
                    ]);
                });

                // ------------------------------------------------------------
                Employee::factory()
                    ->count(25)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                    ]);

                // ------------------------------------------------------------
                User::factory()
                    ->count(2)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                        'role_id' => $roles->random()->id,
                        'department_id' => $departments->random()->id,
                    ]);

                // ------------------------------------------------------------
                $transactionCategoriesData = [
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
                $transactionCategories = collect($transactionCategoriesData)->map(function ($name) use ($enterprise) {
                    return TransactionCategory::create([
                        'enterprise_id' => $enterprise->id,
                        'name' => $name,
                    ]);
                });

                // ------------------------------------------------------------
                Movement::factory()
                    ->count(50)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                        'transaction_category_id' => $transactionCategories->random()->id,
                    ]);

                // ------------------------------------------------------------
                Schedule::factory()
                    ->count(50)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                        'transaction_category_id' => $transactionCategories->random()->id,
                    ]);

                // ------------------------------------------------------------
                $typesReceiptData = [
                    'PIX',
                    'CREDIT_CARD',
                    'DEBT_CARD',
                    'MONEY',
                ];

                $typesReceipt = collect($typesReceiptData)->map(function ($type) use ($enterprise) {
                    return TypeReceipt::factory()->create([
                        'enterprise_id' => $enterprise->id,
                        'name' => $type,
                    ]);
                });

                // ------------------------------------------------------------
                $receiptIdentifiersByType = [
                    'PIX' => [
                        'Mercado Pago AG: 2089 CC: 34566-7',
                        'Banco Inter AG: 0001 CP: 545987-7',
                        'Caixa Econômica AG: 2344 CC: 4545665-6',
                        'Bradesco AG: 1275 CP: 54534-0',
                    ],
                    'MONEY' => [
                        'Caixinha',
                    ],
                    'CREDIT_CARD' => [
                        'Máquina CIELO 1',
                        'Maquina Infinite Pay 1',
                        'Maquina CIELO 2',
                    ],
                    'DEBT_CARD' => [
                        'Máquina CIELO 1',
                        'Maquina Infinite Pay 1',
                        'Maquina CIELO 2',
                    ],
                ];

                foreach ($typesReceipt as $typeReceipt) {
                    foreach ($receiptIdentifiersByType[$typeReceipt->name] as $identifier) {
                        Receipt::factory()->create([
                            'enterprise_id' => $enterprise->id,
                            'type_receipt_id' => $typeReceipt->id,
                            'identifier' => $identifier,
                        ]);
                    }
                }

                // ------------------------------------------------------------
                $supplierCategoriesData = [
                    'Alimentação',
                    'Limpeza',
                    'Construção',
                    'Tecnologia',
                    'Escritório',
                    'Transporte',
                    'Manutenção',
                    'Equipamentos',
                    'Serviços Gerais',
                    'Vestuário',
                    'Material Escolar',
                    'Higiene',
                    'Farmácia e Saúde',
                    'Ferramentas',
                    'Automotivo',
                    'Roupas e Acessórios',
                ];

                $supplierCategories = collect($supplierCategoriesData)->map(function ($name) use ($enterprise) {
                    return SupplierCategory::factory()->create([
                        'enterprise_id' => $enterprise->id,
                        'name' => $name,
                    ]);
                });

                // ------------------------------------------------------------
                Supplier::factory()
                    ->count(30)
                    ->create([
                        'enterprise_id' => $enterprise->id,
                        'supplier_category_id' => $supplierCategories->random()->id,
                    ]);

                // ------------------------------------------------------------
                $tagsData = [
                    'Versão',
                    'Inverno',
                    'Primavera',
                    'Verão',
                    'Outono',
                    'Padrão',
                    'Promoção',
                    'Beta',
                    'Ativo',
                    'Prioridade',
                    'Especial',
                    'Limitado',
                ];

                $tags = collect($tagsData)->map(function ($tag) use ($enterprise) {
                    return Tag::factory()->create([
                        'enterprise_id' => $enterprise->id,
                        'name' => $tag,
                    ]);
                });

                // ------------------------------------------------------------
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

                // ------------------------------------------------------------
                Client::factory()->count(50)->create([
                    'enterprise_id' => $enterprise->id,
                ]);
            });
    }
}
