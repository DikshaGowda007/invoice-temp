<?php

namespace App\Http\Requests\V1\Invoice\LineItem\Update;

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
            'line_item_id' => ['required', 'integer'],
            'description' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required', 'numeric', 'min:0'],
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
            'line_item_id' => 'Line item',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit price',
        ];
    }

    protected $fields = [
        'line_item_id',
        'description',
        'quantity',
        'unit_price',
    ];
}
