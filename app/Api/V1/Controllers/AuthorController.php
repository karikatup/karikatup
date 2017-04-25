<?php

namespace App\Api\V1\Controllers;

use App\Author;
use Tymon\JWTAuth\JWTAuth;

class AuthorController extends Controller
{
    public function index(JWTAuth $auth)
    {
        $authors = Author::orderBy('created_at', 'DESC')->paginate(15);

        return response()->json($authors, 200);
    }

    public function show(Author $author, JWTAuth $auth)
    {
        $comics = $author->comics()->where('is_verified', 1)->orderBy('created_at', 'DESC')->paginate(15);

        return response()->json([
            "author" => $author,
            "comics" => $comics,
        ], 200);
    }
}
