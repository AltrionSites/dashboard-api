<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectImages\StoreRequest;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Traits\ApiResponser;
use App\Traits\File;
use App\Traits\Image;
use Exception;
use Illuminate\Http\Response;

class ProjectImageController extends Controller
{
    use ApiResponser, Image, File;

    public function store(StoreRequest $request)
    {
        $project = Project::find($request->project_id);
        if(!$project)
        {
            return $this->errorResponse('No se encontro el proyecto.', Response::HTTP_NOT_FOUND);
        }
        $images = ProjectImage::where('project_id', $request->project_id)->count();
        if($images >= 4)
        {
            return $this->errorResponse('El máximo de imágenes por proyecto es de 4.', Response::HTTP_EXPECTATION_FAILED);
        }
        if(!is_null($request->images))
        {
            foreach($request->file('images') as $image)
            {
                try
                {
                    $uniqueImgName = $this->generateFileUniqueName(ProjectImage::class, 'name');
                    $imgExtension = $image->getClientOriginalExtension();
                    $this->createImages($image, env('PROJECTS_IMAGES'), $uniqueImgName, $imgExtension);
                    $image = new ProjectImage();
                    $image->name = $uniqueImgName.'.'.$imgExtension;
                    $image->project_id = $project->id;
                    $image->save();
                }
                catch(Exception $e)
                {
                    return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '. $e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
                }
            }
        }
        return $this->successResponse($this->jsonResponse($project));
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
            'description' => $data->description,
            'link' => $data->link,
            'images' => $data->images,
        ];
    }
}
