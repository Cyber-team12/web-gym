<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->code)),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $voucher = $this->route('voucher'); // null saat create

        return [
            'code' => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique('vouchers', 'code')->ignore($voucher)],
            'type' => ['required', 'in:percent,fixed'],
            'value' => ['required', 'numeric', 'min:1', Rule::when($this->type === 'percent', ['max:100'])],
            'quota' => ['required', 'integer', 'min:'.($voucher?->used_count ?? 1)],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after_or_equal:valid_from'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode voucher wajib diisi.',
            'code.unique' => 'Kode voucher sudah ada.',
            'code.alpha_dash' => 'Kode hanya boleh berisi huruf, angka, strip, dan underscore.',
            'type.required' => 'Tipe diskon wajib dipilih.',
            'value.required' => 'Nilai diskon wajib diisi.',
            'value.min' => 'Nilai diskon minimal 1.',
            'value.max' => 'Diskon persen tidak boleh lebih dari 100%.',
            'quota.required' => 'Kuota voucher wajib diisi.',
            'quota.min' => 'Kuota tidak boleh lebih kecil dari jumlah voucher yang sudah terpakai.',
            'valid_from.required' => 'Tanggal mulai berlaku wajib diisi.',
            'valid_until.required' => 'Tanggal berakhir wajib diisi.',
            'valid_until.after_or_equal' => 'Tanggal berakhir harus sama atau setelah tanggal mulai.',
        ];
    }
}
