<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    use GeneralTrait;

    public function addStudentsIT()
    {

        $validation = Validator::make(['students'=>request()->file('students')],[
            'students' => ['required', 'file' , 'mimes:xlsx']
        ]);
        if ($validation->fails()) {
            return $this->apiResponse(null, false, $validation->errors(), 422);
        }
        try{
            Excel::import(new StudentsImport,request()->file('students'));
            return $this->apiResponse('Sucess Add Students');
        }catch(\Exception $e)
        {
            return $this->apiResponse(null, false, $e->getMessage(), 500);
        }
    }
}
