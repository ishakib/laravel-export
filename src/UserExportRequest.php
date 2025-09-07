<?php

namespace Vendor\Export;

use Illuminate\Foundation\Http\FormRequest;

class UserExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'columns' => 'required|array|min:1',
            'type' => 'required|in:csv,pdf',
        ];
    }
}
