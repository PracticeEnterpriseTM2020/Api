<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class address extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'street' => $this->street,
            'number' => $this->number,
            'city' => new city($this->city)
        ];
    }
}
