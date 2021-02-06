<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProjectTypeRequest;
use App\Http\Requests\StoreProjectTypeRequest;
use App\Http\Requests\UpdateProjectTypeRequest;
use App\ProjectType;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ProjectTypesController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('project_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projectTypes = ProjectType::with(['media'])->get();

        return view('admin.projectTypes.index', compact('projectTypes'));
    }

    public function create()
    {
        abort_if(Gate::denies('project_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.projectTypes.create');
    }

    public function store(StoreProjectTypeRequest $request)
    {
        $projectType = ProjectType::create($request->all());

        if ($request->input('photo', false)) {
            $projectType->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $projectType->id]);
        }

        return redirect()->route('admin.project-types.index');
    }

    public function edit(ProjectType $projectType)
    {
        abort_if(Gate::denies('project_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.projectTypes.edit', compact('projectType'));
    }

    public function update(UpdateProjectTypeRequest $request, ProjectType $projectType)
    {
        $projectType->update($request->all());

        if ($request->input('photo', false)) {
            if (!$projectType->photo || $request->input('photo') !== $projectType->photo->file_name) {
                if ($projectType->photo) {
                    $projectType->photo->delete();
                }

                $projectType->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }
        } elseif ($projectType->photo) {
            $projectType->photo->delete();
        }

        return redirect()->route('admin.project-types.index');
    }

    public function show(ProjectType $projectType)
    {
        abort_if(Gate::denies('project_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.projectTypes.show', compact('projectType'));
    }

    public function destroy(ProjectType $projectType)
    {
        abort_if(Gate::denies('project_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projectType->delete();

        return back();
    }

    public function massDestroy(MassDestroyProjectTypeRequest $request)
    {
        ProjectType::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('project_type_create') && Gate::denies('project_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new ProjectType();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
