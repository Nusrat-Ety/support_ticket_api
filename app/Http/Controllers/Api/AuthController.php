<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Login
     * 
     * Authenticates the user and returns the user's API token
     * 
     * @unauthenticated
     * 
     * @group Authentication
     * 
     * @response 200 {
        "data": {
                "data": "{YOUR_AUTH_TOKEN}"
            },
            "message": "Authenticated",
            "status": 200
        }
     */

    public function login(UserLoginRequest $request)
    {
        $request->validated($request->all());
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid Credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'data' => $user->createToken(
                    'Api token for' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth()
                )->plainTextToken
            ]
        );
    }

    /**
     * Logout
     * 
     * Signs out the user and destroys the API token
     * 
     * @group Authentication
     * 
     * @response 200 {
            "data": [],
            "message": "",
            "status": 200
     * }
     * 
     */

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->ok('');
    }
}
