<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOrderRequest extends FormRequest
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
            'status' => 'required|in:Drop,Cancel',
            'cancel_reason' => 'required_if:status,Cancel',
            'type' => 'sometimes|integer',
            'amount_paid' => 'required_if:type,1|numeric|gt:0',
            'amount_received' => 'required_if:type,1|numeric|gt:0',
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
