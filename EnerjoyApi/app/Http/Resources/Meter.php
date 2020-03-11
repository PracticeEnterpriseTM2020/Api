<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Meter extends JsonResource
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
            'meter_id' => $this->meter_id,
            'creation_timestamp' => $this->creation_timestamp,
            'isUsed' => $this->isUsed
        ];
    }
}
