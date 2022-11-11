<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class IndexRequest extends FormRequest
{
    use ApiResponser;

    const MAX_PAGE_SIZE = 10;
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
            'limit' => 'integer|min:1|max:'.self::MAX_PAGE_SIZE,
            'page' => 'integer|min:1',
            'page_size' => 'integer|min:1|max:'.self::MAX_PAGE_SIZE,
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
    */
    public function messages()
    {
        return [
            'limit.integer' => 'El límite debe ser un número entero.',
            'limit.min' => 'El límite debe ser mayor que 0.',
            'limit.max' => 'El límite no puede ser mayor que '.self::MAX_PAGE_SIZE,
            'page.integer' => 'La página debe ser un número entero.',
            'page.min' => 'La página debe ser mayor que 0.',
            'page_size.integer' => 'El tamaño de la página debe ser un número entero.',
            'page_size.min' => 'El tamaño de la página debe ser mayor que 0.',
            'page_size.max' => 'El tamaño de la página no puede ser mayor que '.self::MAX_PAGE_SIZE,
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
