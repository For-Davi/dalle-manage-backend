<?php

namespace App\DTO\Sale\SaleDelivery;

class CreateSaleDeliveriesDTO
{
    public function __construct(
        public readonly int $sale_id,
        public readonly float $freight_value,
        public readonly ?string $cep,
        public readonly string $state,
        public readonly string $city,
        public readonly string $neighborhood,
        public readonly ?string $address,
        public readonly string $number_address,
        public readonly ?string $complement,
        public readonly ?string $recipient_name,
        public readonly ?string $recipient_phone,
        public readonly ?string $observation,
    ) {}

    public static function fromRequest($data, $saleID): self
    {
        return new self(
            sale_id: $saleID,
            freight_value: $data['freightValue'],
            cep: $data['cep'],
            city: $data['city'],
            state: $data['state'],
            neighborhood: $data['neighborhood'],
            address: $data['address'],
            number_address: $data['numberAddress'],
            complement: $data['complement'],
            recipient_name: $data['recipientName'],
            recipient_phone: $data['recipientPhone'],
            observation: $data['observation'],
        );
    }

    public function toArray(): array
    {
        return [
            'sale_id' => $this->sale_id,
            'freight_value' => $this->freight_value,
            'cep' => $this->cep,
            'city' => $this->city,
            'state' => $this->state,
            'neighborhood' => $this->neighborhood,
            'address' => $this->address,
            'number_address' => $this->number_address,
            'complement' => $this->complement,
            'recipient_name' => $this->recipient_name,
            'recipient_phone' => $this->recipient_phone,
            'observation' => $this->observation,
        ];
    }
}
