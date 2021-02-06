<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\Admin\ProjectResource;
use App\Project;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('project_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProjectResource(Project::with(['location', 'project_types'])->get());
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->all());
        $project->project_types()->sync($request->input('project_types', []));

        if ($request->input('main_photo', false)) {
            $project->addMedia(storage_path('tmp/uploads/' . $request->input('main_photo')))->toMediaCollection('main_photo');
        }

        if ($request->input('gallery', false)) {
            $project->addMedia(storage_path('tmp/uploads/' . $request->input('gallery')))->toMediaCollection('gallery');
        }

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Project $project)
    {
        abort_if(Gate::denies('project_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ProjectResource($project->load(['location', 'project_types']));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->all());
        $project->project_types()->sync($request->input('project_types', []));

        if ($request->input('main_photo', false)) {
            if (!$project->main_photo || $request->input('main_photo') !== $project->main_photo->file_name) {
                if ($project->main_photo) {
                    $project->main_photo->delete();
                }

                $project->addMedia(storage_path('tmp/uploads/' . $request->input('main_photo')))->toMediaCollection('main_photo');
            }
        } elseif ($project->main_photo) {
            $project->main_photo->delete();
        }

        if ($request->input('gallery', false)) {
            if (!$project->gallery || $request->input('gallery') !== $project->gallery->file_name) {
                if ($project->gallery) {
                    $project->gallery->delete();
                }

                $project->addMedia(storage_path('tmp/uploads/' . $request->input('gallery')))->toMediaCollection('gallery');
            }
        } elseif ($project->gallery) {
            $project->gallery->delete();
        }

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Project $project)
    {
        abort_if(Gate::denies('project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
