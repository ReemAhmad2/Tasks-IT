<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Http\Resources\StudentsByYearResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\GeneralTrait;
use App\Models\Category;
use App\Models\Student;
use App\Models\StudentIt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        DB::transaction(function () use ($request, $st_category ,$uuid,$user)
            {
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
            });
                $token = $user->createToken('auth_token')->plainTextToken;
                return $this->apiResponse(['access_token' => $token, 'token_type' => 'Bearer' , 'type' =>'student' ],true,null,201);

        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e->getMessage(),500);
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return $this->apiResponse(StudentResource::make($user));
    }

    public function allStudents()
    {
        $users = User::where('type','student')->get();
        return $this->apiResponse(StudentResource::collection($users));
    }

    public function studentsByYear(Request $request)
    {
        try{

            $validator = Validator::make($request->all(), [
                'year'=>['required','integer','min:1','max:5'],
            ]);


            if($validator->fails()){
                return $this->apiResponse(null,false,$validator->errors(),422);
            }

            $students = Student::whereHas('category', function ($query) {
                $query->where('year', request()->year);
            })->get();

            return $this->apiResponse(StudentsByYearResource::collection($students));
        }catch(\Exception $e){
            return $this->apiResponse(null,false,$e->getMessage(),500);
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'uuid' => ['required','string','exists:students,uuid']
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, false, $validator->errors(), 422);
        }

        try {
            $student =Student::whereUuid($request->uuid)->firstOrFail();
            $user = $student->user;
            $student->delete();
            $user->delete();
            return $this->apiResponse('deleted student successfully');

        }catch(\Exception $e)
        {
            return $this->apiResponse(null, false,$e->getMessage(), 500);
        }
    }

    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required|string',
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($request->user()->id)],
            'year'=>'required|integer|min:1|max:5',
            'category'=>'required|integer|min:1'
        ]);


        if ($validator->fails()) {
            return $this->apiResponse(null, false, $validator->errors(), 422);
        }

        $year = $request->year;
        $category = $request->category;

        $st_category = Category::where('year', $year)
            ->where('number',$category)
            ->first();
        if($st_category == null)
        {
            return $this->apiResponse(null,false,'Enter Year Or Category True PLZ',500);
        }

        $data = $request->all();

        $data['category_id']= $st_category->id ;

        $user = $request->user();
        $student = $user->student;

        $user->update($request->all());
        $student->update($data);

        return $this->apiResponse('success update profile');
    }

}


