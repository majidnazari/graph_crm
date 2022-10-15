<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class CreateStudentInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            // TODO Add your validation rules
            'phone' => [
                'required',
                Rule::unique('students', 'phone')->ignore($this->arg('id'), 'id'),
            ],
            'nationality_code' => [
                'nullable',
                Rule::unique('students', 'nationality_code')->ignore($this->arg('id'), 'id'),
            ],
        ];
    }
}
