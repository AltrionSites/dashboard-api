<?php

namespace App\Http\Requests\User;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PutRequest extends FormRequest
{
    use ApiResponser;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|min:3',
            'email' => 'required|email',
            'firstname' => 'required|min:3',
            'lastname' => 'required|min:3',
            'location' => 'required',
            'linkedin' => 'required|url',
            'position' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Es requerido un nombre de usuario.',
            'username.unique' => 'El nombre de usuario ya esta en uso.',
            'username.min' => 'El nombre de usuario debe contener como minimo 3 caracteres.',
            'email.required' => 'El email es requerido.',
            'email.unique' => 'El email ya se esta en uso.',
            'email.email' => 'Formato de email incorrecto.',
            'firstname.required' => 'El nombre es requerido.',
            'firstname.min' => 'El nombre debe contener como minimo 3 caracteres.',
            'lastname.required' => 'El apellido es requerido',
            'lastname.min' => 'El apellido debe contener como minimo 3 caracteres.',
            'location.required' => 'La locación es requerida.',
            'linkedin.required' => 'Es necesario el enlace de Linkedin para actualizar el usuario.',
            'linkedin.url' => 'El formato de enlace es inválido.',
            'position.required' => 'El cargo del usuario es obligatorio.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
