<?php

namespace App\Http\Requests;

use App\Project;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreProjectRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('project_create');
    }

    public function rules()
    {
        return [
            'name'            => [
                'string',
                'required',
            ],
            'slug'            => [
                'string',
                'required',
            ],
            'location_id'     => [
                'required',
                'integer',
            ],
            'project_types.*' => [
                'integer',
            ],
            'project_types'   => [
                'array',
            ],
            'address'         => [
                'string',
                'required',
            ],
        ];
    }
}
