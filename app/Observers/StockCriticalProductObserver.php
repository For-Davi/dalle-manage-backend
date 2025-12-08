<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Notification\SendNotification;
use Illuminate\Support\Facades\DB;

class StockCriticalProductObserver
{
    public function __construct(
        protected SendNotification $notification,
    ) {}

    public function updated(ProductVariant $variant): void
    {
        if ($variant->stock_quantity <= $variant->min_stock_alert) {
            $system = DB::table('setting_system')->where('enterprise_id', $variant->enterprise_id)->first();

            if ($system->send_notification_stock_critical === 1) {

                $color = $variant->color->name ?? 'Não definida';
                $category = $variant->product->category->name ?? 'Não definida';
                $grid = $variant->gridItem->name ?? 'Não definida';
                $sku = $variant->sku ?? 'Não definido';

                $brasiliaTime = now()->timezone('America/Sao_Paulo');
                $dataHora = $brasiliaTime->format('d/m/Y H:i');

                $this->notification->notifyAllUsersByEnterprise(
                    $variant->enterprise_id,
                    '⚠️ ALERTA: Estoque Crítico',
                    "O produto **{$variant->product->name}** atingiu o nível crítico de estoque.
                        
                        📋 **Detalhes do Produto:**
                        • **SKU:** {$sku}
                        • **Categoria:** {$category}
                        • **Cor:** {$color}
                        • **Grade:** {$grid}
                        • **Estoque Atual:** {$variant->stock_quantity} unidades
                        • **Nível de Alerta:** {$variant->min_stock_alert} unidades

                        🚨 **Ação Recomendada:** Realizar reposição de estoque para evitar ruptura.

                        _Data do alerta: {$dataHora} (Horário de Brasília)_",
                );
            }
        }
    }
}
