<?php

namespace App\DTO\Supplier\Order\Status;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateSupplierOrderStatusHistoryDTO extends BaseDTO
{
    public function __construct(
        public readonly int $supplier_order_id,
        public readonly int $changed_by,
        public readonly string $notes,
    ) {}

    public static function start(int $orderID, string $initialNote = 'Pedido criado com sucesso'): self
    {
        return new self(
            supplier_order_id: $orderID,
            notes: $initialNote,
            changed_by: Auth::user()->id,
        );
    }
}
