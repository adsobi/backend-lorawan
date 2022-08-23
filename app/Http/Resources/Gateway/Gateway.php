<?php

namespace App\Http\Resources\Gateway;

use App\Http\Resources\HistoricalData\HistoricalDataWithoutDiffrentModels;
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
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'last_activity' => $this->last_activity->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
