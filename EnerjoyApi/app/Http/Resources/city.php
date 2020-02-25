<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class city extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'name' => $this->name,
            'postalcode'=>$this->postalcode,
            'country' => new CountryCollection($this->country)
        ];
    }
}
