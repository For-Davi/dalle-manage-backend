<?php

namespace App\Repositories;

use App\Models\Returns;
use App\Repositories\Base\BaseRepository;
use App\Repositories\SaleRepository;
use App\Repositories\ClientRepository;
use Illuminate\Support\Facades\DB;

class ReturnRepository extends BaseRepository
{
    protected $saleRepository;
    protected $clientRepository;

    public function __construct(Returns $model, SaleRepository $saleRepository, ClientRepository $clientRepository)
    {
        parent::__construct($model);
        $this->saleRepository = $saleRepository;
        $this->clientRepository = $clientRepository;
    }

    public function getAllBySale($id)
    {
        return $this->model->where('sale_id', $id)->get();
    }

    public function deleteReturn($id)
    {
        $return = $this->findById($id);

        if ($return) {
            if($return->exchange_value > 0 && $return->status === 'active'){
                $sale = $this->saleRepository->findById($return->sale_id);

                if($sale->client_id){
                    $client = $this->clientRepository->findById($sale->client_id);
                    $credit = $client->credits;

                    $credit -= $return->exchange_value;

                    if($credit <= 0){
                        $updateData = ['credits' => 0, 'credit_expires_at' => null];
                    } else {
                        $updateData = ['credits' => $credit];
                    }

                    $this->clientRepository->update($client->id, $updateData);
                }
            }

            DB::table('commissions')->where('return_id', $id)->delete();
            DB::table('sale_deliveries')->where('return_id', $id)->delete();
            DB::table('sale_payments_methods')->where('return_id', $id)->delete();
            DB::table('exchange_payments_methods')->where('return_id', $id)->delete();
            DB::table('product_movements')->where('return_id', $id)->delete();
            DB::table('return_items')->where('return_id', $id)->delete();
            DB::table('return_exchange_items')->where('return_id', $id)->delete();

            return $return->delete();
        }
    }
}
