<?php

namespace App\Http\Requests\V1\Invoice\UpdateStatus;

use App\Constants\CommonConstant;
use App\Constants\HttpStatusConstant;
use App\Constants\InvoiceConstants;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'status' => ['required', 'string', Rule::in(InvoiceConstants::VALID_STATUSES)],
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
            'id' => 'Invoice',
            'status' => 'Status',
            'notes' => 'Notes',
        ];
    }

    protected $fields = [
        'id',
        'status',
        'notes',
    ];
}
