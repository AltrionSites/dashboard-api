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

class TaskImageController extends Controller
{
    use ApiResponser, Image, File;
    public function store(StoreRequest $request)
    {
        $task = Task::find($request->task_id);
        if(!$task)
        {
            return $this->errorResponse('No se encontro la tarea.', Response::HTTP_NOT_FOUND);
        }
        if(!is_null($request->images))
        {
            foreach($request->file('images') as $image)
            {
                try
                {
                    $uniqueImgName = $this->generateFileUniqueName(Task::class, 'description');
                    $imgExtension = $image->getClientOriginalExtension();
                    $this->createImages($image, env('TASKS_IMAGES'), $uniqueImgName, $imgExtension);
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
        }
        return $this->successResponse($this->jsonResponse($task));
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

    private function jsonResponse($data)
    {
        return [
            'user_id' => $data->user_id,
            'description' => $data->description,
            'link' => $data->link,
            'images' => $data->images,
            'complete' => $data->complete,
        ];
    }
}
