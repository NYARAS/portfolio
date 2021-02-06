<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyProjectRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Location;
use App\Project;
use App\ProjectType;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('project_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $projects = Project::with(['location', 'project_types', 'media'])->get();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        abort_if(Gate::denies('project_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $locations = Location::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $project_types = ProjectType::all()->pluck('name', 'id');

        return view('admin.projects.create', compact('locations', 'project_types'));
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->all());
        $project->project_types()->sync($request->input('project_types', []));

        if ($request->input('main_photo', false)) {
            $project->addMedia(storage_path('tmp/uploads/' . $request->input('main_photo')))->toMediaCollection('main_photo');
        }

        foreach ($request->input('gallery', []) as $file) {
            $project->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('gallery');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $project->id]);
        }

        return redirect()->route('admin.projects.index');
    }

    public function edit(Project $project)
    {
        abort_if(Gate::denies('project_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $locations = Location::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $project_types = ProjectType::all()->pluck('name', 'id');

        $project->load('location', 'project_types');

        return view('admin.projects.edit', compact('locations', 'project_types', 'project'));
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

        if (count($project->gallery) > 0) {
            foreach ($project->gallery as $media) {
                if (!in_array($media->file_name, $request->input('gallery', []))) {
                    $media->delete();
                }
            }
        }

        $media = $project->gallery->pluck('file_name')->toArray();

        foreach ($request->input('gallery', []) as $file) {
            if (count($media) === 0 || !in_array($file, $media)) {
                $project->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('gallery');
            }
        }

        return redirect()->route('admin.projects.index');
    }

    public function show(Project $project)
    {
        abort_if(Gate::denies('project_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->load('location', 'project_types');

        return view('admin.projects.show', compact('project'));
    }

    public function destroy(Project $project)
    {
        abort_if(Gate::denies('project_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $project->delete();

        return back();
    }

    public function massDestroy(MassDestroyProjectRequest $request)
    {
        Project::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('project_create') && Gate::denies('project_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Project();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
