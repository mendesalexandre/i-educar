<?php

namespace App\Http\Requests\Api\Resource\SchoolAcademicYear;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResourceSchoolAcademicYearRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'school' => ['required', 'integer'],
            'year' => ['nullable', 'integer', 'digits:4'],
            'limit' => ['nullable', 'integer']
        ];
    }

    public function attributes()
    {
        return [
            'school' => 'Escola',
            'year' => 'Ano'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([]));
    }
}
