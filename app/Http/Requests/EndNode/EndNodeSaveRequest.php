<?php

namespace App\Http\Requests\EndNode;

use Illuminate\Foundation\Http\FormRequest;

class EndNodeSaveRequest extends FormRequest
{
    public function rules()
    {
        return [
            'app_id'  => 'required|integer|exists:apps,id',
            'name'  => 'required|string|max:255',
            'dev_eui'  => 'required|string|max:255',
            'join_eui'  => 'required|string|max:255',
            'count_to_response' => 'required|integer|min:1|max:127'
        ];
    }
}
