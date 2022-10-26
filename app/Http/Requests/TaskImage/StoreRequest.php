<?php

namespace App\Http\Requests\TaskImage;

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
            'task_id' => 'required|exists:users_tasks,id',
            'images.*' => 'required|mimes:jpg,jpeg,png,JPG,JPEG,PNG'
        ];
    }

    public function messages()
    {
        return [
            'task_id.required' => 'La tarea es requerido.',
            'task_id.exists' => 'La tarea ingresada no existe',
            'images.*.mimes' => 'La imagen es requerida.',
            'images.*.mimes' => 'El formato de las imágenes es incorrecto, debe ser de tipo < JPG, PNG, JPEG >',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
