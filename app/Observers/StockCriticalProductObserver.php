<?php

namespace App\Observers;

use App\Jobs\Notification\SendNotificationJob;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class StockCriticalProductObserver
{
    public function updated(ProductVariant $variant): void
    {
        if (! $this->isStockCritical($variant)) {
            return;
        }

        if (! $this->isNotificationEnabled($variant->enterprise_id)) {
            return;
        }

        SendNotificationJob::dispatch(
            'Estoque Crítico',
            $this->buildMessage($variant),
            null,
            $variant->enterprise_id
        );
    }

    private function isStockCritical(ProductVariant $variant): bool
    {
        return $variant->stock_quantity <= $variant->min_stock_alert;
    }

    private function isNotificationEnabled(int $enterpriseId): bool
    {
        $system = DB::table('setting_system')
            ->where('enterprise_id', $enterpriseId)
            ->value('send_notification_stock_critical');

        return $system === 1;
    }

    private function buildMessage(ProductVariant $variant): string
    {
        $color = $variant->color->name ?? 'Não definida';
        $category = $variant->product->category->name ?? 'Não definida';
        $grid = $variant->gridItem->name ?? 'Não definida';
        $sku = $variant->sku ?? 'Não definido';
        $dataHora = now()->format('d/m/Y H:i');

        return <<<MSG
        O produto **{$variant->product->name}** atingiu o nível crítico de estoque.

        📋 **Detalhes do Produto:**
        • **SKU:** {$sku}
        • **Categoria:** {$category}
        • **Cor:** {$color}
        • **Grade:** {$grid}
        • **Estoque Atual:** {$variant->stock_quantity} unidades
        • **Nível de Alerta:** {$variant->min_stock_alert} unidades

        🚨 **Ação Recomendada:** Realizar reposição de estoque para evitar ruptura.

        _Data do alerta: {$dataHora} (Horário de Brasília)_
        MSG;
    }
}
