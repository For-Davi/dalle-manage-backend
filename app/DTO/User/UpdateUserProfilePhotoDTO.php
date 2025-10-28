<?php

namespace App\DTO\User;

use App\DTO\BaseDTO;

class UpdateUserProfilePhotoDTO extends BaseDTO
{
    public function __construct(
        public readonly ?int $photo_add_id,
        public readonly ?int $photo_delete_id,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            photo_add_id: $data['photoAdd'] ?? null,
            photo_delete_id: $data['photoDelete'] ?? null,
        );
    }

    public function hasAddOperation(): bool
    {
        return ! is_null($this->photo_add_id);
    }

    public function hasDeleteOperation(): bool
    {
        return ! is_null($this->photo_delete_id);
    }

    public function hasBothOperations(): bool
    {
        return $this->hasAddOperation() && $this->hasDeleteOperation();
    }
}
