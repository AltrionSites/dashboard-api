<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskImage\StoreRequest;
use App\Models\Task;
use App\Models\TaskImage;
use App\Traits\ApiResponser;
use App\Traits\File;
use App\Traits\Image;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class TaskImageController extends Controller
{
    use ApiResponser, Image, File;

    public function listImages($id)
    {
        $imagesQuery = TaskImage::where('task_id', $id)->orderByDesc('id')->get();
        return $this->successResponse($imagesQuery);
    }

    public function store(StoreRequest $request)
    {
        $task = Task::find($request->task_id);
        if(!$task)
        {
            return $this->errorResponse('No se encontro la tarea.', Response::HTTP_NOT_FOUND);
        }
        if(!is_null($request->image))
        {
            try
            {
                $uniqueImgName = $this->generateFileUniqueName(Task::class, 'description');
                $imgExtension = $request->file('image')->getClientOriginalExtension();
                $this->createImages($request->file('image'), env('TASKS_IMAGES'), $uniqueImgName, $imgExtension);
                $image = new TaskImage();
                $image->name = $uniqueImgName.'.'.$imgExtension;
                $image->task_id = $request->validated('task_id');
                $image->save();
            }
            catch(Exception $e)
            {
                return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '. $e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
            }
        }
        return $this->successResponse($this->jsonResponse($image));
    }

    public function destroy($taskId, $id)
    {
        $image = TaskImage::where('task_id', $taskId)->find($id);
        if(!$image)
        {
            return $this->errorResponse('No se encontro la imagen', Response::HTTP_NOT_FOUND);
        }
        $imgName = $image->name;
        $this->deleteImages(env('TASKS_IMAGES'), $imgName);
        $image->delete();
        return $this->successResponse($image);
    }

    public function getImage($id)
    {
        $image = TaskImage::find($id);
        if(!$image)
        {
            return $this->errorResponse('No se encontro la imagen de la tarea.', Response::HTTP_NOT_FOUND);
        }
        return Storage::get(env('TASKS_IMAGES').$image->name);
    }

    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'task_id' => $data->task_id
        ];
    }
}
