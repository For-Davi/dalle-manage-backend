<?php

namespace App\Repositories;

use App\Models\SaleDelivery;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;

class SaleDeliveryRepository extends BaseRepository
{
    public function __construct(SaleDelivery $model)
    {
        parent::__construct($model);
    }

    public function getDeliveries(string $status)
    {
        $query = Sale::where('enterprise_id', Auth::user()->enterprise_id)
                    ->with('delivery');

        if ($status === 'delivered') {
            $query->whereHas('delivery', function ($q) {
                $q->whereIn('status', ['delivered', 'partial_delivery', 'delivered_in_person']);
            });
        } elseif ($status === 'pendent') {
            $query->whereHas('delivery', function ($q) {
                $q->where('status', 'pendent');
            });
        } elseif ($status === 'scheduled') {
            $query->whereHas('delivery', function ($q) {
                $q->where('status', 'scheduled');
            });
        }

        $sales = $query->get();

        return $sales->pluck('delivery')->filter()->values();
    }

    public function getDeliveryStats()
    {
        $deliveries = SaleDelivery::whereHas('sale', function ($query) {
            $query->where('enterprise_id', Auth::user()->enterprise_id);
        })->get();

        return [
            'pending'   => $deliveries->where('status', 'pendent')->count(),
            'scheduled' => $deliveries->where('status', 'scheduled')->count(),
            'delivered' => $deliveries->filter(function ($delivery) {
                return in_array($delivery->status, [
                    'delivered', 
                    'partial_delivery', 
                    'delivered_in_person'
                ]);
            })->count(),
        ];
    }
}
