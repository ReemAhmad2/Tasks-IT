<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\GeneralTrait;
use Illuminate\Support\Str;


class UserController extends Controller
{
    use GeneralTrait;

    // LOGIN

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(null,false,$validator->errors(),422);
        }

        try {

            $user = User::where('email', $request->email)->first();
            if($user && Hash::check($request->password, $user->password))
            {
                $token = $user->createToken('web')->plainTextToken;
                return $this->apiResponse(['access_token' => $token, 'token_type' => 'Bearer' , 'type' => $user->type ]);
            }
            else
            {
                    return $this->apiResponse(null,false,'failed login! email or password is failed',401);
            }

        }catch (\Exception $e) {
            return $this->apiResponse(null,false,$e->getMessage(),500);
        }
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->apiResponse('Logged out Successfully');
    }
}
