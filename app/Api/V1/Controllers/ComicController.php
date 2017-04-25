<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\ComicCommentRequest;
use App\Api\V1\Requests\ComicStoreRequest;
use App\Comic;
use App\Comment;
use App\User;
use App\UserLike;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class ComicController extends Controller
{
    public function index(JWTAuth $auth)
    {
        $comics = Comic::orderBy('created_at', 'DESC')->with('author')->with('user')->paginate(15);

        return response()->json($comics, 200);
    }

    public function show(Comic $comic, JWTAuth $auth)
    {
        $likes    = $comic->likes()->with('user')->orderBy('created_at', 'DESC')->get();
        $comments = $comic->comments()->with('user')->orderBy('created_at', 'ASC')->get();

        return response()->json([
            'comic'    => $comic,
            'likes'    => $likes,
            'comments' => $comments,
        ], 200);
    }

    public function store(ComicStoreRequest $request, JWTAuth $auth)
    {
        $user = $auth->toUser();

        $slug = $request->has('name') ? $request->get('name') : str_random(6);
        $slugName = str_slug($slug);
        $slug = $slugName;
        $count = 2;
        while(Comic::where('slug', $slug)->count() > 0){
            $slug = $slugName."-".$count;
            $count++;
        }

        if($request->hasFile('image')){
            $image = $request->file('image');

            $imageName = $slug.'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images/comic'), $imageName);

            $imagePath = "images/comic/".$imageName;
        } else {
            $imagePath = null;
        }

        $comic = new Comic();
        $comic->name = $request->get('name');
        $comic->description = $request->get('description');
        $comic->slug = $slug;
        $comic->image = $imagePath;
        $comic->is_active = 1;
        $comic->user_id = $user->id;
        $comic->author_id = $request->has('author_id') ? $request->get('author_id') : null;
        $comic->is_verified = 0;
        $comic->save();

        return response()->json([
            'message' => 'ok'
        ], 201);
    }

    public function comment(ComicCommentRequest $request, Comic $comic, JWTAuth $auth)
    {
        $user = $auth->toUser();

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->comic_id = $comic->id;
        $comment->comment = $request->get('comment');
        $comment->save();

        return response()->json([
            'message' => 'ok'
        ], 201);
    }

    public function commentDestroy(Comic $comic, Comment $comment, JWTAuth $auth)
    {
        $user = $auth->toUser();

        if($user->id == $comment->user_id){
            $comment->delete();

            return response()->json([
                'message' => 'ok'
            ], 200);
        } else {
            throw new AccessDeniedHttpException();
        }
    }

    public function like(Comic $comic, JWTAuth $auth)
    {
        $user = $auth->toUser();

        if($comic->likes()->where('user_id', $user->id)->count() > 0)
            throw new AccessDeniedHttpException();

        $like = new UserLike();
        $like->comic_id = $comic->id;
        $like->user_id = $user->id;
        $like->save();

        return response()->json([
            'message' => 'ok'
        ], 201);
    }

    public function unlike(Comic $comic, JWTAuth $auth)
    {
        $user = $auth->toUser();

        if($comic->likes()->where('user_id', $user->id)->count() == 0)
            throw new AccessDeniedHttpException();

        $like = $comic->likes()->where('user_id', $user->id)->first();
        $like->delete();

        return response()->json([
            'message' => 'ok'
        ], 200);
    }
}
