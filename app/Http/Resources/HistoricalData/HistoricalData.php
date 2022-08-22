<?php

namespace App\Http\Resources\HistoricalData;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoricalData extends JsonResource
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
            'model' => 'HistoricalData',
            'id' => $this->id,
        ]
    }
}
