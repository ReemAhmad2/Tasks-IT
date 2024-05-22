<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeacherResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\GeneralTrait;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{

    use GeneralTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, false, $validator->errors(), 422);
        }
        try {
            $user = new User;
            DB::transaction(function () use ($request,$user){
                $user->uuid = Str::uuid();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->type = 'teacher';
                $user->save();
                $uuid = Str::uuid();
                $user->teacher()->create([
                    'uuid'=>$uuid,
                ]);
            });

            return $this->apiResponse('Create Teacher Access', true, null, 201);
        } catch (\Exception $e) {
            return $this->apiResponse(null,false,$e->getMessage(),500);
        }
    }

    public function allTeachers()
    {
        $users = User::where('type','teacher')->get();
        return $this->apiResponse(TeacherResource::collection($users));
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'uuid' => ['required','string','exists:teachers,uuid']
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, false, $validator->errors(), 422);
        }

        try {
            $teacher =Teacher::whereUuid($request->uuid)->firstOrFail();
            $teacher->delete();
            return $this->apiResponse('deleted teacher successfully');

        }catch(\Exception $e)
        {
            return $this->apiResponse(null, false,$e->getMessage(), 500);
        }
    }
}
