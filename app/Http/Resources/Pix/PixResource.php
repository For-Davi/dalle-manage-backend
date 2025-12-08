<?php

namespace App\Http\Resources\Pix;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PixResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'encodedImage' => $this['encodedImage'],
            'payload' => $this['payload'],
        ];
    }
}
