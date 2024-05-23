

@extends('layout')

@section('content')

<!-- 記事画面 -->
<head>
  <link href="{{ asset('css/main.css') }}" rel="stylesheet">
</head>

<!-- タイトル/メタデータ -->
<div class="banner">
  <div class="container">
    <h1>{{ $article->title }}</h1>

    <div class="article-meta">
      <a href="/profile/eric-simons"><img src="http://i.imgur.com/Qr71crq.jpg" /></a>
      <div class="info">
        <a href="/profile/eric-simons" class="author">Eric Simons</a>
        <span class="date">{{ $article->created_at->format('F j, Y') }}</span>
      </div>
      <button class="btn btn-sm btn-outline-secondary">
        <i class="ion-plus-round"></i>
        &nbsp; Follow Eric Simons <span class="counter">(10)</span>
      </button>
      &nbsp;&nbsp;
      <button class="btn btn-sm btn-outline-primary">
        <i class="ion-heart"></i>
        &nbsp; Favorite Post <span class="counter">(29)</span>
      </button>
      <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-sm btn-outline-secondary">
        <i class="ion-edit"></i> Edit Article
      </a>
      <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger" id="deletebtn">
            <i class="ion-trash-a"></i> Delete Article
        </button>
      </form>
    </div>
  </div>
</div>

<!-- 記事内容 -->
<div class="container page">

  <div class="card-text">
    {!! $parsedown->text($article->body) !!}
  </div>

  <div class="tags">
    <ul class="tag-list">
          @foreach ($article->tags as $tag)
              <li class="tag-default tag-pill tag-outline">{{ $tag->name }}</li>
          @endforeach
    </ul>
  </div>

  <!-- コメントフォーム -->
  <div class="card-block">
    <form method="POST" action="{{ route('comments.store') }}">
      @csrf
      <input type="hidden" name="article_id" value="{{ $article->id }}">
      <div class="form-group">
          <textarea name="body" class="form-control" rows="3" placeholder="Write a comment..."></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Post Comment</button>
    </form>
  </div>

  <!-- コメント -->
  @foreach ($article->comments as $comment)
    <div class="card">
      <div class="card-block">
        <p class="card-text">{{ $comment->body }}</p>
      </div>
      <div class="card-footer">
        <a href="/profile/author" class="comment-author">
          <!-- <img src="#" class="comment-author-img" />　投稿者のプロフィール画像 -->
        </a>
        &nbsp;
        <a href="/profile/jacob-schmidt" class="comment-author">Jacob Schmidt</a>
        <span class="date-posted">{{ $comment->created_at->format('F j, Y') }}</span>
        <span class="mod-options">
          <i class="ion-trash-a"></i>
        </span>
    </div>
  @endforeach

</div>

@endsection