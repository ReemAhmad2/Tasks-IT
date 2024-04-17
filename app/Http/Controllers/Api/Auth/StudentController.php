<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\GeneralTrait;
use App\Models\Category;
use App\Models\StudentIt;

class StudentController extends Controller
{

    use GeneralTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email', 'max:255','exists:students_it,email' ,'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'year'=>['required','integer','min:1','max:5'],
            'category'=>['required','integer'],
            'number'=>['required','integer','exists:students_it,number','unique:students,number_of_student' ],
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null,false,$validator->errors(),422);
        }

        try {

        $uuid = Str::uuid();
        $year = $request->year;
        $category = $request->category;

        $st_category = Category::where('year', $year)
            ->where('number',$category)
            ->first();
        if($st_category == null)
        {
            return $this->apiResponse(null,false,'Enter Year Or Category True PLZ',500);
        }

        $student = StudentIt::where('number','=',$request->number)->where('email',$request->email)->first();
        if($student == null)
        {
            return $this->apiResponse(null,false,'Not IT student!! Please Enter Email and Number True',500);
        }

        $user = new User;
        $user->uuid = Str::uuid();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->type='student';
        $user->save();

        $user->student()->create([
            'number_of_student'=>$request->number,
            'category_id'=>$st_category->id,
            'uuid'=>$uuid,
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->apiResponse(['access_token' => $token, 'token_type' => 'Bearer'],true,null,201);

        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e,500);
        }
    }
}


