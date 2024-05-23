<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    //コメントを保存
    public function store(Request $request)
    {
        $comment = new Comment();
        $comment->body = $request->body;
        $comment->article_id = $request->article_id; // article_id をリクエストから取得して設定
        $comment->save();
    
        return back();
    }
}
