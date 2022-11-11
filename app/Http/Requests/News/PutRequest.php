<?php

namespace App\Http\Requests\News;

use App\Traits\ApiResponser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
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

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str($this->title)->slug()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|min:3|max:100',
            'content' => 'required',
            'link' => 'nullable|url',
            'slug' => 'unique:news,slug',
            'image' => 'mimes:jpg,png,jpeg,JPG,PNG,JPEG',
            'visible' => 'required|'.Rule::in([0 ,1]),
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El titulo es requerido',
            'title.min' => 'El titulo debe contener como minimo 3 caracteres.',
            'title.max' => 'El titulo debe contener como máximo 100 caracteres.',
            'content.required' => 'El contenido es requerido',
            'link.url' => 'Debes introducir un URL válido.',
            'slug.unique' => 'El slug ya esta en uso, cambie el titulo de la noticia.',
            'image.required' => 'La imagen es requerida.',
            'image.mimes' => 'El formato de la imagen es incorrecto, debe ser de tipo < JPG, PNG, JPEG >',
            'visible.required' => 'Debe indicar la visibilidad de la noticia.',
            'visible.in' => 'Los valores para la visibilidad de la noticia es incorrecto.',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = $this->errorResponse($validator->errors(), Response::HTTP_BAD_REQUEST);
        throw new ValidationException($validator, $response);
    }
}
