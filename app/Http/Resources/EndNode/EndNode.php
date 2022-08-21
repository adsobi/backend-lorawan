<?php

namespace App\Http\Resources\EndNode;

use App\Http\Resources\App\App;
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
            'nwk_s_key' => $this->nwk_s_key,
            'app_s_key' => $this->app_s_key,
            'dev_eui' => $this->dev_eui,
            'join_eui' => $this->join_eui,
        ];
    }
}
