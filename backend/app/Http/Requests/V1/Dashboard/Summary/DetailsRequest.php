<?php

namespace App\Http\Requests\V1\Dashboard\Summary;

use App\Constants\DashboardConstants;
use Illuminate\Foundation\Http\FormRequest;
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
            'range' => ['sometimes', 'string', Rule::in(DashboardConstants::VALID_RANGES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'range' => 'range',
        ];
    }
}
