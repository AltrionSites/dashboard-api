<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use App\Http\Requests\Project\StoreRequest;
use App\Http\Requests\Project\PutRequest;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Traits\ApiResponser;
use App\Traits\Image;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    use ApiResponser, Image;
    public function index(IndexRequest $request)
    {
        $projectsQuery = Project::orderByDesc('id')->withCount('images');
        $pageSize = $request->has('page_size') ? $request->page_size : $request::MAX_PAGE_SIZE;

        $result = $projectsQuery->paginate($pageSize)->through(function($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'description' => $p->description,
                'link' => $p->link,
                'images' => $p->images
            ];
        });

        return $this->successResponse($result);
    }

    public function view($id)
    {
        $project = Project::find($id);
        if(!$project)
        {
            return $this->errorResponse('No se encontro el proyecto', Response::HTTP_NOT_FOUND);
        }
        return $this->successResponse($this->jsonResponse($project));
    }

    public function store(StoreRequest $request)
    {
        $project = new Project();
        $project->name = $request->validated('name');
        $project->description = $request->validated('description');
        $project->link = $request->validated('link');
        $project->save();
        return $this->successResponse($this->jsonResponse($project));
    }

    public function update(PutRequest $request, $id)
    {
        $project = Project::find($id);
        if(!$project)
        {
            return $this->errorResponse('No se encontro el proyecto', Response::HTTP_NOT_FOUND);
        }
        $project->update([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'link' => $request->validated('link'),
        ]);
        return $this->successResponse($this->jsonResponse($project));
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        if(!$project)
        {
            return $this->errorResponse('No se encontro el proyecto', Response::HTTP_NOT_FOUND);
        }
        $images = ProjectImage::where('project_id', $id)->get();
        foreach($images as $image)
        {
            $this->deleteImages(env('PROJECTS_IMAGES'), $image->name);
            $image->delete();
        }
        $project->delete();
        return $this->successResponse($this->jsonResponse($project));   
    }

    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'link' => $data->link
        ];
    }

}
