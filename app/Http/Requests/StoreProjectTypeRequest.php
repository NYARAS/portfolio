<?php

namespace App\Http\Requests;

use App\ProjectType;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreProjectTypeRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('project_type_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'slug' => [
                'string',
                'required',
            ],
        ];
    }
}
