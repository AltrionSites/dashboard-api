<?php

namespace App\Http\Requests\Service;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class StoreRequest extends FormRequest
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
            'name' => 'required|max:30',
            'description' => 'required',
            'image' => 'required|mimes:jpg,jpeg,png,JPG,JPEG,PNG',
            'user_service_manager' => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del servicio es requerido.',
            'name.max' => 'El máximo de caracteres para el nombre es de 30.',
            'description.required' => 'La descripción es requerida.',
            'image.required' => 'La imagen es requerida.',
            'image.mimes' => 'El formato de la imagen es incorrecto, debe ser de tipo < JPG, PNG, JPEG >',
            'user_service_manager.required' => 'Debes introducir al encargado de este servicio.',
            'user_service_manager.exists' => 'El usuario ingresado no existe.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
