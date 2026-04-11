<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Dashboard e Assinatura
            ['slug' => 'dashboard.view', 'description' => 'Visualizar o dashboard'],
            ['slug' => 'subscription.payment', 'description' => 'Realizar pagamento de assinatura'],

            // Organização
            ['slug' => 'enterprise.update', 'description' => 'Atualizar dados da organização'],
            ['slug' => 'enterprise.delete', 'description' => 'Excluir a organização'],
            ['slug' => 'result.view', 'description' => 'Visualizar resultados'],

            // Vendas
            ['slug' => 'sale.view', 'description' => 'Visualizar vendas'],
            ['slug' => 'sale.create', 'description' => 'Criar venda'],
            ['slug' => 'sale-discount.create', 'description' => 'Criar desconto de venda'],
            ['slug' => 'sale.cancel', 'description' => 'Cancelar venda'],
            ['slug' => 'sale.delete', 'description' => 'Excluir vendas'],

            // Entregas e Entregadores
            ['slug' => 'delivery.view', 'description' => 'Visualizar entregas'],
            ['slug' => 'delivery.update', 'description' => 'Atualizar entrega'],
            ['slug' => 'delivery-guy.view', 'description' => 'Visualizar entregadores'],
            ['slug' => 'delivery-guy.create', 'description' => 'Cadastrar entregador'],
            ['slug' => 'delivery-guy.update', 'description' => 'Atualizar entregador'],
            ['slug' => 'delivery-guy.delete', 'description' => 'Excluir entregadores'],

            // Devoluções
            ['slug' => 'return.view', 'description' => 'Visualizar devoluções'],
            ['slug' => 'return.create', 'description' => 'Registrar devolução'],
            ['slug' => 'return.update', 'description' => 'Atualizar devolução'],
            ['slug' => 'return.delete', 'description' => 'Excluir devoluções'],

            // Produtos (Cores, Tags, Categorias)
            ['slug' => 'product-color.view', 'description' => 'Visualizar cores de produtos'],
            ['slug' => 'product-color.create', 'description' => 'Cadastrar cor de produto'],
            ['slug' => 'product-color.update', 'description' => 'Atualizar cor de produto'],
            ['slug' => 'product-color.delete', 'description' => 'Excluir cores de produtos'],

            ['slug' => 'product-tag.view', 'description' => 'Visualizar tags de produtos'],
            ['slug' => 'product-tag.create', 'description' => 'Cadastrar tag de produto'],
            ['slug' => 'product-tag.update', 'description' => 'Atualizar tag de produto'],
            ['slug' => 'product-tag.delete', 'description' => 'Excluir tags de produtos'],

            ['slug' => 'product-category.view', 'description' => 'Visualizar categorias de produtos'],
            ['slug' => 'product-category.create', 'description' => 'Cadastrar categoria de produto'],
            ['slug' => 'product-category.update', 'description' => 'Atualizar categoria de produto'],
            ['slug' => 'product-category.delete', 'description' => 'Excluir categorias de produtos'],

            // Gestão de Produtos e Grades
            ['slug' => 'product.view', 'description' => 'Visualizar produtos'],
            ['slug' => 'product.create', 'description' => 'Cadastrar produto'],
            ['slug' => 'product.movement', 'description' => 'Gerenciar movimentações de produtos'],
            ['slug' => 'product.update', 'description' => 'Atualizar produto'],
            ['slug' => 'product.delete', 'description' => 'Excluir produtos'],

            ['slug' => 'grid.view', 'description' => 'Visualizar grades'],
            ['slug' => 'grid.create', 'description' => 'Criar grade'],
            ['slug' => 'grid.update', 'description' => 'Atualizar grade'],
            ['slug' => 'grid.delete', 'description' => 'Excluir grades'],

            // Usuários e Departamentos
            ['slug' => 'user.view', 'description' => 'Visualizar usuários'],
            ['slug' => 'user.create', 'description' => 'Cadastrar usuário'],
            ['slug' => 'user.update', 'description' => 'Atualizar usuário'],
            ['slug' => 'user.delete', 'description' => 'Excluir usuários'],

            ['slug' => 'employee.view', 'description' => 'Visualizar funcionários'],
            ['slug' => 'employee.create', 'description' => 'Cadastrar funcionário'],
            ['slug' => 'employee.update', 'description' => 'Atualizar funcionário'],
            ['slug' => 'employee.delete', 'description' => 'Excluir funcionários'],

            ['slug' => 'department.view', 'description' => 'Visualizar departamentos'],
            ['slug' => 'department.create', 'description' => 'Criar departamento'],
            ['slug' => 'department.update', 'description' => 'Atualizar departamento'],
            ['slug' => 'department.delete', 'description' => 'Excluir departamentos'],

            // Financeiro
            ['slug' => 'transaction.view', 'description' => 'Visualizar transações'],
            ['slug' => 'transaction.create', 'description' => 'Lançar transação'],
            ['slug' => 'transaction.update', 'description' => 'Atualizar transação'],
            ['slug' => 'transaction.delete', 'description' => 'Excluir transações'],

            ['slug' => 'transaction-category.view', 'description' => 'Visualizar categorias de transações'],
            ['slug' => 'transaction-category.create', 'description' => 'Criar categoria de transação'],
            ['slug' => 'transaction-category.update', 'description' => 'Atualizar categoria de transação'],
            ['slug' => 'transaction-category.delete', 'description' => 'Excluir categorias de transações'],

            ['slug' => 'receipt.view', 'description' => 'Visualizar recebimentos'],
            ['slug' => 'receipt.create', 'description' => 'Gerar recebimento'],
            ['slug' => 'receipt.update', 'description' => 'Atualizar recebimento'],
            ['slug' => 'receipt.delete', 'description' => 'Excluir recebimentos'],

            ['slug' => 'commission.view', 'description' => 'Visualizar comissões'],

            // Clientes e Fornecedores
            ['slug' => 'client.view', 'description' => 'Visualizar clientes'],
            ['slug' => 'client.create', 'description' => 'Cadastrar cliente'],
            ['slug' => 'client.update', 'description' => 'Atualizar cliente'],
            ['slug' => 'client.delete', 'description' => 'Excluir clientes'],

            ['slug' => 'supplier.view', 'description' => 'Visualizar fornecedores'],
            ['slug' => 'supplier.create', 'description' => 'Cadastrar fornecedor'],
            ['slug' => 'supplier.update', 'description' => 'Atualizar fornecedor'],
            ['slug' => 'supplier.delete', 'description' => 'Excluir fornecedores'],

            ['slug' => 'supplier-category.view', 'description' => 'Visualizar categorias de fornecedores'],
            ['slug' => 'supplier-category.create', 'description' => 'Criar categoria de fornecedor'],
            ['slug' => 'supplier-category.update', 'description' => 'Atualizar categoria de fornecedor'],
            ['slug' => 'supplier-category.delete', 'description' => 'Excluir categorias de fornecedores'],

            ['slug' => 'supplier-order.view', 'description' => 'Visualizar pedidos de fornecedores'],
            ['slug' => 'supplier-order.create', 'description' => 'Criar pedido de fornecedor'],
            ['slug' => 'supplier-order.update', 'description' => 'Atualizar pedido de fornecedor'],
            ['slug' => 'supplier-order.delete', 'description' => 'Excluir pedidos de fornecedores'],

            ['slug' => 'supplier-catalog.view', 'description' => 'Visualizar catálogos de fornecedores'],
            ['slug' => 'supplier-catalog.create', 'description' => 'Adicionar produto ao catálogo de fornecedores'],
            ['slug' => 'supplier-catalog.update', 'description' => 'Atualizar produto do catálogo de fornecedores'],
            ['slug' => 'supplier-catalog.delete', 'description' => 'Excluir produto do catálogo de fornecedores'],

            // Configurações
            ['slug' => 'config.view', 'description' => 'Visualizar configurações'],
            ['slug' => 'config.update', 'description' => 'Editar configurações'],

            ['slug' => 'role.view', 'description' => 'Visualizar permissões'],
            ['slug' => 'role.create', 'description' => 'Criar permissões'],
            ['slug' => 'role.update', 'description' => 'Editar permissões'],
            ['slug' => 'role.delete', 'description' => 'Excluir permissões'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                ['description' => $permission['description']]
            );
        }
    }
}
