<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\ApiResponses;
use Symfony\Component\HttpFoundation\Request;


class AuthController extends Controller
{
    use ApiResponses;

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
                'data' => $user->createToken('Api token for' . $user->email, ['*'], now()->addMonth())->plainTextToken
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->ok('');
    }
}
