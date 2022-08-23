<?php

namespace App\Http\Resources\HistoricalData;

use App\Http\Resources\EndNode\EndNode;
use App\Http\Resources\Gateway\Gateway;
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
            'end_node' => new EndNode($this->endNode),
            'gateway' => new Gateway($this->gateway),
            'data'=> $this->data,
            'snr'=> $this->snr,
            'rssi'=> $this->rssi,
            'type'=> $this->type,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
