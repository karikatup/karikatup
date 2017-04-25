<?php

namespace App\Api\V1\Controllers;

use App\Comic;
use App\User;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class UserController extends Controller
{
    public function show(User $user, JWTAuth $auth)
    {
        $comics = $user->comics()->with('author')->orderBy('created_at', 'DESC')->paginate(15);
        $likes = $user->likes()->with('comic')->orderBy('created_at', 'DESC')->paginate(15);

        return response()->json([
            "user" => $user,
            "comics" => $comics,
            "likes" => $likes
        ], 200);
    }
}
