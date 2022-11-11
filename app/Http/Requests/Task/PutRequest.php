<?php

namespace App\Http\Requests\Task;

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
            'user_id' => 'required|exists:users,id',
            'description' => 'required',
            'link' => 'nullable|url',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'El usuario es requerido.',
            'user_id.exists' => 'El usuario ingresado no existe',
            'description.required' => 'La descripción es requerida.',
            'link.url' => 'El enlace es inválido.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
