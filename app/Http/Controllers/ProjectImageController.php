<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use App\Http\Requests\ProjectImages\StoreRequest;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Traits\ApiResponser;
use App\Traits\File;
use App\Traits\Image;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProjectImageController extends Controller
{
    use ApiResponser, Image, File;
    
    public function listImages($id)
    {
        $imagesQuery = ProjectImage::where('project_id', $id)->orderByDesc('id')->get();
        return $this->successResponse($imagesQuery);
    }

    public function getImage($id)
    {
        $image = ProjectImage::find($id);
        if(!$image)
        {
            return $this->errorResponse('No se encontro la imagen.', Response::HTTP_NOT_FOUND);
        }
        return Storage::get(env('PROJECTS_IMAGES').$image->name);
    }

    public function store(StoreRequest $request)
    {
        $project = Project::find($request->project_id);
        if(!$project)
        {
            return $this->errorResponse('No se encontro el proyecto.', Response::HTTP_NOT_FOUND);
        }
        try
        {
            $uniqueImgName = $this->generateFileUniqueName(ProjectImage::class, 'name');
            $imgExtension = $request->file('image')->getClientOriginalExtension();
            $this->createImages($request->file('image'), env('PROJECTS_IMAGES'), $uniqueImgName, $imgExtension);
            $image = new ProjectImage();
            $image->name = $uniqueImgName.'.'.$imgExtension;
            $image->project_id = $project->id;
            $image->save();
        }
        catch(Exception $e)
        {
            return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '. $e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
        }
        return $this->successResponse($this->jsonResponse($image));
    }

    public function destroy($projectId, $id)
    {
        $image = ProjectImage::where('project_id', $projectId)->find($id);
        if(!$image)
        {
            return $this->errorResponse('No se encontro la imagen', Response::HTTP_NOT_FOUND);
        }
        $imgName = $image->name;
        $this->deleteImages(env('PROJECTS_IMAGES'), $imgName);
        $image->delete();
        return $this->successResponse($image);
    }

    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'project_id' => $data->project_id
        ];
    }
}
