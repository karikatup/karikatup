<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\ForgotPasswordRequest;
use App\Api\V1\Requests\LoginRequest;
use App\Api\V1\Requests\RegisterRequest;
use App\Api\V1\Requests\ResetPasswordRequest;
use App\User;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, JWTAuth $auth)
    {
        $user = new User();
        $user->username = $request->get('username');
        $user->email = $request->get('email');
        $user->password = bcrypt($request->get('password'));
        $user->name = $request->get('name');
        if(!$user->save())
            throw new HttpException(500);

        $token = $auth->fromUser($user);
        return response()->json([
            'status' => 'ok',
            'token'  => $token,
        ], 201);
    }

    public function login(LoginRequest $request, JWTAuth $auth)
    {
        try {
            $token = $auth->attempt(['username' => $request->get('username'), 'password' => $request->get('password')]);

            if(!$token)
                throw new AccessDeniedHttpException();
        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        return response()->json([
            'status' => 'ok',
            'token'  => $token
        ]);
    }

    public function send(ForgotPasswordRequest $request, JWTAuth $auth)
    {
        $user = User::where('email', $request->get('email'))->first();

        if(!$user)
            throw new NotFoundHttpException();

        $send = Password::broker()->sendResetLink(['email' => $request->get('email')]);
        if($send !== Password::RESET_LINK_SENT)
            throw new HttpException(500);

        return response()->json([
            'status' => 'ok'
        ], 200);
    }

    public function reset(ResetPasswordRequest $request, JWTAuth $auth)
    {
        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function($user, $password){
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if($response !== Password::PASSWORD_RESET)
            throw new HttpException(500);

        $user = User::where('email', $request->get('email'))->first();
        return response()->json([
            'status' => 'ok',
            'token'  => $auth->fromUser($user)
        ]);
    }
}
