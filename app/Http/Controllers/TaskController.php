<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use App\Http\Requests\Task\PutRequest;
use App\Http\Requests\Task\StoreRequest;
use App\Models\Task;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    use ApiResponser;

    public function index(IndexRequest $request)
    {
        $tasksQuery = Task::where('user_id', auth()->user()->id)->orderByDesc('id')->withCount('images');
        $pageSize = $request->has('page_size') ? $request->page_size : $request::MAX_PAGE_SIZE;
        $result = $tasksQuery->paginate($pageSize)->through(function($t){
            return [
                'id' => $t->id,
                'user_id' => $t->user_id,
                'description' => $t->description,
                'link' => $t->link,
                'images' => $t->images,
                'complete' => $t->complete,

            ];
        });
        
        return $this->successResponse($result);
    }

    public function view($id)
    {
        $task = Task::where('user_id', auth()->user()->id)->with('images')->find($id);
        if(!$task)
        {
            return $this->errorResponse('No se encontro la tarea.', Response::HTTP_NOT_FOUND);
        }
        return $this->successResponse($this->jsonResponse($task));
    }

    public function store(StoreRequest $request)
    {
        $task = new Task();
        $task->user_id = $request->validated('user_id');
        $task->description = $request->validated('description');
        $task->link = $request->validated('link');
        $task->complete = 0;
        $task->save();

        return $this->successResponse($this->jsonResponse($task));
    }

    public function update(PutRequest $request, $id)
    {
        $task = Task::find($id);
        if(!$task)
        {
            return $this->errorResponse('No se encontro la tarea.', Response::HTTP_NOT_FOUND);
        }
        $task->update([
            'user_id' => $request->validated('user_id'),
            'description' => $request->validated('description'),
            'link' => $request->validated('link'),
        ]);

        return $this->successResponse($this->jsonResponse($task));
    }

    public function destroy($id)
    {
        $task = Task::where('user_id', auth()->user()->id)->find($id);
        if(!$task)
        {
            return $this->errorResponse('No se encontro la tarea.', Response::HTTP_NOT_FOUND);
        }
        $task->delete();
        return $this->successResponse($this->jsonResponse($task));
    }

    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'user_id' => $data->user_id,
            'description' => $data->description,
            'link' => $data->link,
            'images' => $data->images,
            'complete' => $data->complete
        ];
    }
}
