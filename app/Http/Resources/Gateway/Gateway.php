<?php

namespace App\Http\Resources\Gateway;

use App\Http\Resources\HistoricalData\HistoricalData;
use Illuminate\Http\Resources\Json\JsonResource;

class Gateway extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'model' => 'Gateway',
            'id' => $this->id,
            'name' => $this->name,
            'gateway_eui' => $this->gateway_eui,
            'description' => $this->description,
            'last_activity' => new HistoricalData($this->last_activity),
        ];
    }
}
