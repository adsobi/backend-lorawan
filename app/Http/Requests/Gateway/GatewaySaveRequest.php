<?php

namespace App\Http\Requests\Gateway;

use Illuminate\Foundation\Http\FormRequest;

class GatewaySaveRequest extends FormRequest
{
    public function rules()
    {
        return [
            'gateway_eui' => 'required|string|max:255',
            'name' => 'required|string|max:255',
        ];
    }
}