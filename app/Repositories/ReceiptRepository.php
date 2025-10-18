<?php

namespace App\Repositories;

use App\DTO\Receipt\FilterReceiptDTO;
use App\Models\Receipts;

class ReceiptRepository
{
    public function __construct(protected Receipts $model) {}

    public function getAllWithFilter(FilterReceiptDTO $filters)
    {
        $query = $this->model->where('enterprise_id', $filters->enterpriseID);

        if ($filters->active !== null) {
            $query->where('active', $filters->active)->with('type');
        }

        return $query->get();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model
            ->where('enterprise_id', $enterpriseId)
            ->with('type')
            ->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $receipt = $this->findById($id);
        if ($receipt) {
            $receipt->update($data);

            return $receipt;
        }

        return null;
    }

    public function delete($id)
    {
        $receipt = $this->findById($id);

        if ($receipt) {
            return $receipt->delete();
        }

        return false;
    }
}
