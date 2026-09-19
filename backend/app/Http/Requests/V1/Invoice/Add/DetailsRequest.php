<?php

namespace App\Http\Requests\V1\Invoice\Add;

use App\Constants\CommonConstant;
use App\Constants\HttpStatusConstant;
use App\Http\Services\AuthService;
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
        $userId = app(AuthService::class)->getData()->get('userId');

        return [
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')
                    ->where('user_id', $userId)
                    ->where('is_deleted', CommonConstant::IS_DELETED_NO),
            ],
            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
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
            'client_id' => 'Client',
            'issue_date' => 'Issue date',
            'due_date' => 'Due date',
            'tax_rate' => 'Tax rate',
            'discount_amount' => 'Discount amount',
            'notes' => 'Notes',
        ];
    }

    protected $fields = [
        'client_id',
        'issue_date',
        'due_date',
        'tax_rate',
        'discount_amount',
        'notes',
    ];
}
