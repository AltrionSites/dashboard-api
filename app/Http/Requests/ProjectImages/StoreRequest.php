<?php

namespace App\Http\Requests\ProjectImages;

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
            'images.*' => 'required|mimes:jpg,png,jpeg,JPG,PNG,JPEG',
            'images' => 'max:4',
        ];
    }

    public function messages()
    {
        return [
            'images.*.required' => 'Las imágenes son requeridas.',
            'images.*.mimes' => 'El formato de las imágenes es incorrecto, debe ser de tipo < JPG, PNG, JPEG >',
            'images.max' => 'La cantidad máxima de imágenes que puedes subir es de 4.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
