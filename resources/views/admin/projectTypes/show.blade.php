@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.projectType.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.project-types.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.projectType.fields.id') }}
                        </th>
                        <td>
                            {{ $projectType->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.projectType.fields.name') }}
                        </th>
                        <td>
                            {{ $projectType->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.projectType.fields.slug') }}
                        </th>
                        <td>
                            {{ $projectType->slug }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.projectType.fields.photo') }}
                        </th>
                        <td>
                            @if($projectType->photo)
                                <a href="{{ $projectType->photo->getUrl() }}" target="_blank" style="display: inline-block">
                                    <img src="{{ $projectType->photo->getUrl('thumb') }}">
                                </a>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.project-types.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection