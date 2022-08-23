<?php

namespace App\Http\Resources\EndNode;

use App\Http\Resources\App\App;
use App\Http\Resources\HistoricalData\HistoricalDataWithoutDiffrentModels;
use Illuminate\Http\Resources\Json\JsonResource;

class EndNode extends JsonResource
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
            'model' => 'EndNode',
            'id' => $this->id,
            'app' => new App($this->app),
            'dev_addr' => $this->dev_addr,
            'name' => $this->name,
            'description' => $this->description,
            'dev_eui' => $this->dev_eui,
            'join_eui' => $this->join_eui,
            'count_to_response' => $this->count_to_response,
            'last_activity' => $this->last_activity->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
