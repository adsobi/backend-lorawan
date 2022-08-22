<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;

class AppSaveRequest extends FormRequest
{
    public function rules()
    {
        return [
            'key' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:65535'
        ];
    }
}