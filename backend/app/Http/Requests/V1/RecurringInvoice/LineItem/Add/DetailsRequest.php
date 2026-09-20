<?php

namespace App\Http\Requests\V1\RecurringInvoice\LineItem\Add;

use App\Constants\CommonConstant;
use App\Constants\HttpStatusConstant;
use App\Constants\LineItemConstants;
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
            'recurring_invoice_id' => ['required', 'integer'],
            'description' => ['required', 'string', 'max:'.LineItemConstants::DESCRIPTION_MAX_LENGTH],
            'quantity' => ['required', 'numeric', 'min:'.LineItemConstants::MIN_QUANTITY],
            'unit_price' => ['required', 'numeric', 'min:'.LineItemConstants::MIN_UNIT_PRICE],
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
            'recurring_invoice_id' => 'Recurring invoice',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit price',
        ];
    }

    protected $fields = [
        'recurring_invoice_id',
        'description',
        'quantity',
        'unit_price',
    ];
}
