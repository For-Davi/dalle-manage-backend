<?php

namespace App\Repositories;

use App\DTO\Receipt\FilterReceiptDTO;
use App\Models\Receipt;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class ReceiptRepository extends BaseRepository
{
    public function __construct(Receipt $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter(FilterReceiptDTO $filters)
    {
        $query = $this->model->query();

        if ($filters->active !== null) {
            $query->where('active', $filters->active)->with('type');
        }

        return $query->get();
    }

    public function delete($id)
    {
        $receipt = $this->findById($id);

        if ($receipt) {
            DB::table('sale_payments_methods')->where('receipt_id', $id)->update(['receipt_id' => null]);
            DB::table('exchange_payments_methods')->where('receipt_id', $id)->update(['receipt_id' => null]);

            return $receipt->delete();
        }

        return false;
    }
}
