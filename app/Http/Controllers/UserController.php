<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexRequest;
use App\Http\Requests\User\PutRequest;
use App\Http\Requests\User\StoreRequest;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(IndexRequest $request)
    {
        $usersQuery = User::orderByDesc('id');
        $pageSize = $request->has('page_size') ? $request->page_size : $request::MAX_PAGE_SIZE;

        $result = $usersQuery->paginate($pageSize)->through(function($u) {
            return [
                'id' => $u->id,
                'username' => $u->username,
                'firstname' => $u->firstname,
                'lastname' => $u->lastname,
                'email' => $u->email,
                'location' => $u->location,
            ];
        });

        return $this->successResponse($result);
    }

    public function view($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return $this->errorResponse('No se encontro el usuario.', Response::HTTP_NOT_FOUND);
        }
        return $this->successResponse($this->jsonResponse($user));
    }

    public function store(StoreRequest $request)
    {
        $user = new User();
        $user->username = $request->validated('username');
        $user->firstname = $request->validated('firstname');
        $user->lastname = $request->validated('lastname');
        $user->email = $request->validated('email');
        $user->password = Hash::make($request->validated('password'));
        $user->location = $request->validated('location');
        $user->save();

        return $this->successResponse($this->jsonResponse($user));
    }

    public function update(PutRequest $request, $id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return $this->errorResponse('No se encontro el usuario.', Response::HTTP_NOT_FOUND);
        }

        $user->update([
            'username' => $request->validated('username'),
            'firstname' => $request->validated('firstname'),
            'lastname' => $request->validated('lastname'),
            'email' => $request->validated('email'),
            'location' => $request->validated('location')
        ]);

        return $this->successResponse($this->jsonResponse($user));
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return $this->errorResponse('No se encontro el usuario.', Response::HTTP_NOT_FOUND);
        }
        $user->delete();
        return $this->successResponse($this->jsonResponse($user));
    }

    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        if(!$user)
        {
            return $this->errorResponse('No se encontro el usuario.', Response::HTTP_NOT_FOUND);
        }
        $user->restore();
        return $this->successResponse($this->jsonResponse($user));
    }

    private function jsonResponse($data)
    {
        return [
            'id' => $data->id,
            'username' => $data->username,
            'firstname' => $data->firstname,
            'lastname' => $data->lastname,
            'email' => $data->email,
            'location' => $data->location
        ];
    }
}
