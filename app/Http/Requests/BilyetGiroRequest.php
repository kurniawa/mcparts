<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BilyetGiroRequest extends FormRequest
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
            'received_date' => 'required|date',
            'issuer_bank' => 'required|string|max:100',
            'bilyet_number' => 'required|string|max:20',
            'due_date' => 'required|date',
            'amount' => 'required|numeric',
            'issuer_name' => 'required|string|max:255',
            'issuer_account_number' => 'required|string|max:50',
            'beneficiary_name' => 'required|string|max:255',
            'beneficiary_bank' => 'required|string|max:100',
            'beneficiary_account_number' => 'required|string|max:50',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'amount' => $this->cleanCurrency($this->amount),
        ]);
    }

    private function cleanCurrency($value)
    {
        if (is_null($value)) {
            return $value;
        }

        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return $value;
    }
}
