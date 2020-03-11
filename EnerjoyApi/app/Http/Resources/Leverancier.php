<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Leverancier extends JsonResource
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
            'companyname' => $this->companyname,
            'vat' => $this->vatnumbre,
            'email' => $this->email, 
            'addressId' => (string) $this->addressId,
            'phonenumber' => $this->phonenumber,
          ];
    }
}
