<?php

namespace App\Repositories;

use App\Models\SaleDelivery;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SaleDeliveryRepository extends BaseRepository
{
    public function __construct(SaleDelivery $model)
    {
        parent::__construct($model);
    }

    public function getDeliveries(string $status, ?array $filters = null, ?bool $returnProducts = null)
    {
        $enterpriseId = Auth::user()->enterprise_id;

        $query = SaleDelivery::whereHas('sale', function ($q) use ($enterpriseId) {
            $q->where('status', 'active');
            $q->where('enterprise_id', $enterpriseId);
        });

        if ($status === 'delivered') {
            $query->whereIn('status', ['delivered', 'partial_delivered', 'delivered_in_person']);
        } elseif ($status === 'pendent') {
            $query->where('status', 'pendent');
        } elseif ($status === 'scheduled') {
            $query->where('status', 'scheduled');
        }

        $hasStart = ! empty($filters['startDate']);
        $hasEnd = ! empty($filters['endDate']);

        if ($hasStart && $hasEnd) {
            $start = Carbon::createFromFormat('d/m/Y', $filters['startDate'])->startOfDay()->utc();
            $end = Carbon::createFromFormat('d/m/Y', $filters['endDate'])->endOfDay()->utc();
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($hasStart) {
            $start = Carbon::createFromFormat('d/m/Y', $filters['startDate'])->startOfDay()->utc();
            $query->where('created_at', '>=', $start);
        } elseif ($hasEnd) {
            $end = Carbon::createFromFormat('d/m/Y', $filters['endDate'])->endOfDay()->utc();
            $query->where('created_at', '<=', $end);
        }

        $hasStartSched = ! empty($filters['startScheduledDate']);
        $hasEndSched = ! empty($filters['endScheduledDate']);

        if ($hasStartSched && $hasEndSched) {
            $start = Carbon::createFromFormat('d/m/Y', $filters['startScheduledDate'])->startOfDay();
            $end = Carbon::createFromFormat('d/m/Y', $filters['endScheduledDate'])->endOfDay();
            $query->whereBetween('scheduled_date', [$start, $end]);
        } elseif ($hasStartSched) {
            $start = Carbon::createFromFormat('d/m/Y', $filters['startScheduledDate'])->startOfDay();
            $query->where('scheduled_date', '>=', $start);
        } elseif ($hasEndSched) {
            $end = Carbon::createFromFormat('d/m/Y', $filters['endScheduledDate'])->endOfDay();
            $query->where('scheduled_date', '<=', $end);
        }

        if (! empty($filters['deliveryGuy'])) {
            $query->where('delivery_guy_id', $filters['deliveryGuy']);
        }

        $deliveries = $query->get();

        if ($returnProducts) {
            $deliveries->each(function ($delivery) {
                if ($delivery->return_id) {
                    $delivery->load('returnExchangeItems');
                } else {
                    $delivery->load('saleItems');
                }
            });
        }

        return $deliveries;
    }

    public function getDeliveryStats()
    {
        $deliveries = SaleDelivery::whereHas('sale', function ($query) {
            $query->where('enterprise_id', Auth::user()->enterprise_id);
        })->get();

        return [
            'pending' => $deliveries->where('status', 'pendent')->count(),
            'scheduled' => $deliveries->where('status', 'scheduled')->count(),
            'delivered' => $deliveries->filter(function ($delivery) {
                return in_array($delivery->status, [
                    'delivered',
                    'partial_delivered',
                    'delivered_in_person',
                ]);
            })->count(),
        ];
    }
}
