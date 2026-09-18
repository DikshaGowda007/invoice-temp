<?php

namespace App\Http\Requests\V1\Client\Edit;

use App\Constants\CommonConstant;
use App\Constants\HttpStatusConstant;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();
        throw new HttpResponseException(response()->json([
            'status' => CommonConstant::ERROR,
            'message' => $firstError,
        ], HttpStatusConstant::UNPROCESSABLE_ENTITY));
    }

    public function attributes(): array
    {
        return [
            'id' => 'Client',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'notes' => 'Notes',
        ];
    }

    protected $fields = [
        'id',
        'name',
        'email',
        'phone',
        'address',
        'notes',
    ];
}
