<?php

namespace App\Http\Requests;

use App\Traits\ApiResponser;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class DirectionRequest extends FormRequest
{
    use ApiResponser;

    const UP_DIRECTION = 'up';
    const DOWN_DIRECTION = 'down';
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
            'direction' => Rule::In([self::UP_DIRECTION, self::DOWN_DIRECTION]),
        ];
    }
    
    public function messages()
    {
        return [
            'direction.in' => 'La dirección debe ser UP o DOWN'
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
