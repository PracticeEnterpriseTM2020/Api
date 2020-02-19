<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class addressResource extends JsonResource
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
            'address' => $this->address,
            'number' => $this->number,
            'city' => $this->city,
            'postalcode' => $this->postalcode,
            'custId' => $this->custId
        ];
    }
}
