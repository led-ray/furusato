<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use App\Models\Tag;

class ArticleController extends Controller
{
    //記事一覧を表示する
    public function index()
    {
        $articles = Article::orderBy('created_at', 'desc')->get();
        $tags = Tag::all(); // すべてのタグを取得

        return view('home.list', compact('articles', 'tags'));
    }

    //記事一覧の取得(API)
    public function list(Request $request)
    {
        $articles = Article::with('tags')->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($articles);
    }

    //タグで絞り込んだ記事一覧を表示する
    public function indexByTag($tagName)
    {
        $tag = Tag::where('name', $tagName)->first();
        $articles = $tag ? $tag->articles()->orderBy('created_at', 'desc')->get() : collect();
        $tags = Tag::all(); // すべてのタグを取得
    
        return view('home.list', compact('articles', 'tags', 'tagName'));
    }

    //記事の新規投稿画面を表示する
    public function showEditor()
    {
        return view('editor.new');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    //記事を投稿する
    public function store(StoreArticleRequest $request)
    {
        // 記事を保存
        $article = new Article();
        $article->title = $request->title;
        $article->description = $request->description;
        $article->body = $request->body;
        $article->slug = Str::slug($request->input('article.title'), '-');
        $article->save();

        // タグの処理
        $tagsInput = explode(',', $request->tags); // コンマ区切りを配列に変換
        $tags = [];
        foreach ($tagsInput as $tagName) {
            $tagName = trim($tagName); // 空白があれば削除
            if ($tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]); // タグが存在しない場合のみ新規作成
                $tags[] = $tag->id;
            }
        }
        $article->tags()->sync($tags); // 記事とタグの関連付けを更新

        // 投稿した記事にリダイレクト
        return redirect()->route('articles.show', $article->id);
    }

    //記事を投稿する（API）
    public function store_API(Request $request)
    {
        $request->validate([
            'article.title' => 'required',
            'article.description' => 'required',
            'article.body' => 'required',
        ]);
    
        $article = new Article($request->input('article'));
        $article->slug = Str::slug($request->input('article.title'), '-');
        $article->save();

        $tagsInput = explode(',', $request->tags); // コンマ区切りを配列に変換
        $tags = [];
        foreach ($tagsInput as $tagName) {
            $tagName = trim($tagName); // 空白があれば削除
            if ($tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]); // タグが存在しない場合のみ新規作成
                $tags[] = $tag->id;
            }
        }
        $article->tags()->sync($tags); // 記事とタグの関連付けを更新
    
        return response()->json($article, 201);
    }

    // 記事の編集ページを表示する
    public function edit(Article $article)
    {
        return view('editor.edit', compact('article'));
    }

    // 記事を更新する
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'body' => 'required',
        ]);

        $article->update([
            'title' => $request->title,
            'description' => $request->description,
            'body' => $request->body,
        ]);

        return redirect()->route('articles.show', $article->id);
    }

    // 記事を更新する（API）
    public function update_API(Request $request, $slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $articleData = $request->input('article');
        if (isset($articleData['title'])) {
            $articleData['slug'] = Str::slug($articleData['title'], '-');
        }
        $article->update($articleData);
    
        return response()->json($article);
    }

    // 記事を表示する
    public function show(Article $article)
    {
        return view('article.article', compact('article'));
    }

    // 記事を表示する（API）
    public function show_API($id)
    {
        $article = Article::with('tags')->where('id', $id)->firstOrFail();
        return response()->json($article);
    }

    // 記事の削除
    public function destroy(Article $article)
    {
        $article->delete();
    
        // 記事一覧ページへリダイレクト
        $articles = Article::orderBy('created_at', 'desc')->get();
        $tags = Tag::all(); // すべてのタグを取得
        
        return view('home.list', compact('articles', 'tags'));
    }

    // 記事の削除（API）
    public function destroy_API($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();
        $article->delete();
    
        return response()->json(null, 204);
    }
}
