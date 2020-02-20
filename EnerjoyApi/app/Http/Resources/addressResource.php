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
            'address' => $this->street,
            'number' => $this->number,
            'city' => $this->cityId,
            'country' => $this->countryId
        ];
    }
}
