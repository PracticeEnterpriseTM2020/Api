<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class invoice extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customerId' => $this->customerId,
            'price' => $this->price,
            'date' => $this->date,
        ];
    }
}
