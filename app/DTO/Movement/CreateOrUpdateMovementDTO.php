<?php

namespace App\DTO\Movement;

use App\DTO\BaseDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreateOrUpdateMovementDTO extends BaseDTO
{
    public function __construct(
        public readonly string $date,
        public readonly string $type,
        public readonly ?int $transaction_category_id,
        public readonly float $value,
        public readonly ?string $description,
        public readonly string $enterprise_id,
    ) {}

    public static function fromRequest($request, array $overrides = []): self
    {
        $data = [
            'date' => $request->date ?? null,
            'type' => $request->type,
            'transactionCategoryID' => $request->transactionCategoryID ?? null,
            'value' => $request->value,
            'description' => $request->description,
        ];

        $data = array_merge($data, $overrides);

        $date = $data['date'];
        if (is_string($date)) {
            $date = str_contains($date, '/')
                ? Carbon::createFromFormat('d/m/Y', $date)
                : Carbon::parse($date);
        }

        return new self(
            date: $date->format('Y-m-d'),
            type: $data['type'],
            transaction_category_id: $data['transactionCategoryID'],
            value: $data['value'],
            description: $data['description'],
            enterprise_id: Auth::user()->enterprise_id,
        );
    }
}
