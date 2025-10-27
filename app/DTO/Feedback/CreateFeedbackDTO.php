<?php

namespace App\DTO\Feedback;

use App\DTO\BaseDTO;
use Illuminate\Support\Facades\Auth;

class CreateFeedbackDTO extends BaseDTO
{
    public function __construct(
        public readonly string $text,
        public readonly string $enterprise_name,
        public readonly string $user_name,
        public readonly string $user_email,
        public readonly ?int $image_id,
    ) {}

    public static function fromRequest($data): self
    {
        $user = Auth::user();

        return new self(
            text: $data['text'],
            enterprise_name: $user->enterprise->name,
            user_name: $user->name,
            user_email: $user->email,
            image_id: $data['image_id'] ?? null,
        );
    }
}
