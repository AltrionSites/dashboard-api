<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use App\Traits\File;
use App\Traits\Image;
use Illuminate\Http\Response;
use Exception;

use App\Models\Service;
use App\Models\User;

use App\Http\Requests\IndexRequest;
use App\Http\Requests\Service\PutRequest;
use App\Http\Requests\Service\StoreRequest;


class ServiceController extends Controller
{
    use ApiResponser, File, Image;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(IndexRequest $request)
    {
        $servicesQuery = Service::orderByDesc('id');
        $pageSize = $request->has('page_size') ? $request->page_size : $request::MAX_PAGE_SIZE;

        $result = $servicesQuery->paginate($pageSize)->through(function($s) {
            return [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description,
                'image' => $s->description,
                'user' => $this->getUserInfo($s->user_service_manager),
            ];
        });

        return $this->successResponse($result);
    }

    public function view($id)
    {
        $service = Service::find($id);
        if(!$service)
        {
            return $this->errorResponse('No se encontro el servicio.', Response::HTTP_NOT_FOUND);
        }
        return $this->successResponse($this->jsonResponse($service));
    }

    public function store(StoreRequest $request)
    {
        $service = new Service();
        $service->name = $request->validated('name');
        $service->description = $request->validated('description');
        $service->user_service_manager = $request->validated('user_service_manager');
        if(!is_null($request->image))
        {
            try
            {
                $uniqueImgName = $this->generateFileUniqueName(Service::class, 'image');
                $imgExtension = $request->file('image')->getClientOriginalExtension();
                $this->createImages($request->file('image'), env('SERVICES_IMAGES'), $uniqueImgName, $imgExtension);
                $service->image = $uniqueImgName.'.'.$imgExtension;
            }
            catch(Exception $e)
            {
                return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '. $e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
            }
        }
        $service->save();
        return $this->successResponse($this->jsonResponse($service));
    }

    public function update(PutRequest $request, $id)
    {
        $service = Service::find($id);
        if(!$service)
        {
            return $this->errorResponse('No se encontro el servicio.', Response::HTTP_NOT_FOUND);
        }   
        if(!is_null($request->image))
        {
            try
            {
                $uniqueImgName = $this->generateFileUniqueName(Service::class, 'image');
                $imgExtension = $request->file('image')->getClientOriginalExtension();
                $this->createImages($request->file('image'), env('SERVICES_IMAGES'), $uniqueImgName, $imgExtension);
                $imgName = $service->image;
                $this->deleteImages(env('SERVICES_IMAGES'), $imgName);
                $service->image = $uniqueImgName.'.'.$imgExtension;
            }
            catch(Exception $e)
            {
                return $this->errorResponse('Ocurrió un error al subir la imagen. Excepción: '. $e->getMessage(), Response::HTTP_EXPECTATION_FAILED);
            }
        } else
        {
            $service->image = $service->image;
        }

        $service->update([
            'name' => $request->validated('name'),
            'description' => $request->validated('description'),
            'user_service_manager' => $request->validated('user_service_manager')
        ]);

        return $this->successResponse($this->jsonResponse($service));
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        if(!$service)
        {
            return $this->errorResponse('No se encontro el servicio.', Response::HTTP_NOT_FOUND);
        } 
        $service->delete();
        return $this->successResponse($this->jsonResponse($service));
    }

    private function getUserInfo($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return $this->errorResponse('No se encontro el usuario.', Response::HTTP_NOT_FOUND);
        }
        return [
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'linkedin' => $user->linkedin,
        ];
    }

    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'image' => $data->image,
            'user' => $this->getUserInfo($data->user_service_manager),
        ];
    }
}
