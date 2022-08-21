<?php

namespace App\Http\Resources\Gateway;

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
            'key' => $this->key,
        ];
    }
}
