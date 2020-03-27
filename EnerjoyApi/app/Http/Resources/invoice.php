<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class invoice extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customerId' => new Customer($this->customer), //Connect the customerId to the customer to get access to their details (Name,...)
            'price' => $this->price,
            'date' => $this->date,
        ];
    }
}
