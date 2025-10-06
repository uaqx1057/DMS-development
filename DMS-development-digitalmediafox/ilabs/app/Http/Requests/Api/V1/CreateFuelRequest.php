<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateFuelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'total_orders' => 'required|integer|min:1',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ];
    }

     /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        // $errors = $validator->errors()->toArray();
        throw new HttpResponseException(response()->json([
            'code' => 422,
            'status'   => 'error',
            'message'   => 'Validation errors',
            'response'      => $validator->errors()
        ]));
    }
}
