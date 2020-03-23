<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class customer extends JsonResource
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
            'id' => $this->id,
            'first' => $this->firstname,
            'last' => $this->lastname,
            'email' => $this->email,
            'active' => $this->active,
            'address' => new address($this->address)
        ];
    }
}
