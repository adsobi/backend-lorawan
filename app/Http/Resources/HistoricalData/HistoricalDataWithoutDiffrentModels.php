<?php

namespace App\Http\Resources\HistoricalData;

use App\Http\Resources\EndNode\EndNode;
use App\Http\Resources\Gateway\Gateway;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoricalDataWithoutDiffrentModels extends JsonResource
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
            'data'=> $this->data,
            'snr'=> $this->snr,
            'rssi'=> $this->rssi,
            'type'=> $this->type,
        ];
    }
}
