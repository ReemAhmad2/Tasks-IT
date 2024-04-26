<?php

namespace App\Imports;

use App\Models\StudentIt;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow , WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $uuid = Str::uuid();
        return new StudentIt([
            'number'  => $row['number'],
            'email' => $row['email'],
            'uuid'  => $uuid,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.email' => ['required' , 'string' , 'email' ,'unique:students_it,email'],
            '*.number' => ['required','integer','unique:students_it,number'],
        ];
    }
}
