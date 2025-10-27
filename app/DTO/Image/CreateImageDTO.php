<?php

namespace App\DTO\Image;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateImageDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
        public readonly int $size,
        public readonly int $enterprise_id,
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            name: $data['name'],
            size: $data['size'],
            url: $data['url'],
            enterprise_id: Auth::user()->enterprise_id
        );
    }
}
