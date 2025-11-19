<?php

namespace App\Enums\Supplier\Order;

enum SupplierOrderStatus: string
{
    case CANCELED = 'canceled';
    case COMPLETELY_FINISHED = 'completely_finished';
    case PARTIAL_FINISHED = 'partial_finished';
    case WAITING = 'waiting';
    case CONFERENCE = 'conference';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
