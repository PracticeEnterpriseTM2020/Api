<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MeterCustomer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer_email' => $this->meter_email,
            'meter_id' => $this->meter_id
        ];
    }
}
