<?php

namespace App\Repositories;

use App\Models\Returns;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class ReturnRepository extends BaseRepository
{
    public function __construct(Returns $model)
    {
        parent::__construct($model);
    }

    public function getAllBySale($id)
    {
        return $this->model->where('sale_id', $id)->get();
    }

    public function deleteReturn($id)
    {
        $return = $this->findById($id);

        if ($return) {
            DB::table('commissions')->where('return_id', $id)->delete();
            $exchange = DB::table('exchanges')->where('return_id', $id)->first();
            if ($exchange) {
                DB::table('sale_deliveries')->where('exchange_id', $exchange->id)->delete();
                DB::table('sale_payments_methods')->where('exchange_id', $exchange->id)->delete();
                DB::table('exchange_payments_methods')->where('exchange_id', $exchange->id)->delete();
                DB::table('exchange_additional')->where('exchange_id', $exchange->id)->delete();
                DB::table('exchanges')->where('id', $exchange->id)->delete();
            }
            DB::table('product_movements')->where('return_id', $id)->delete();
            DB::table('return_items')->where('return_id', $id)->delete();
            DB::table('return_exchange_items')->where('return_id', $id)->delete();

            return $return->delete();
        }
    }
}
