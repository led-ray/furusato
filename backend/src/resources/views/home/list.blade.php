
@extends('layout')

@section('content')
<!-- 記事一覧画面 -->
<div class="banner">
  <div class="container">
    <h1 class="logo-font">conduit</h1>
    <p>A place to share your knowledge.</p>
  </div>
</div>


<div class="container page">
  <div class="row">
    <div class="col-md-9">
      <div class="feed-toggle">
          <ul class="nav nav-pills outline-active">
              <!-- タブ -->
              <li class="nav-item">
                  <a class="nav-link" href="/">Your Feed</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link active" href="/">Global Feed</a>
              </li>
              <!-- 絞り込みタブ -->
              @if(isset($tagName))
                  <li class="nav-item">
                      <a class="nav-link active" href="">{{ '#' . $tagName }}</a>
                  </li>
              @endif
          </ul>
      </div>

      <!-- 記事一覧を表示 -->
      @foreach ($articles as $article)
        <div class="article-preview">
          <div class="article-meta">
            <a href="/profile/eric-simons"><img src="http://i.imgur.com/Qr71crq.jpg" /></a>
            <div class="info">
              <a href="/profile/eric-simons" class="author">Eric Simons</a>
              <span class="date">{{ $article->created_at->format('F j, Y') }}</span>
            </div>
            <button class="btn btn-outline-primary btn-sm pull-xs-right">
              <i class="ion-heart"></i> 29
            </button>
          </div>
          <a href="{{ route('articles.show', $article) }}" class="preview-link">
            <h1>{{ $article->title }}</h1>
            <p>{{ $article->description }}</p>
            <span>Read more...</span>
            <ul class="tag-list">
              @foreach ($article->tags as $tag)
                <li class="tag-default tag-pill tag-outline">{{ $tag->name }}</li>
              @endforeach
            </ul>
          </a>
        </div>
      @endforeach

      <!-- ページ -->
      <ul class="pagination">
        <li class="page-item active">
          <a class="page-link" href="">1</a>
        </li>
        <li class="page-item">
          <a class="page-link" href="">2</a>
        </li>
      </ul>
    </div>

    <!-- タグ -->
    <div class="col-md-3">
      <div class="sidebar">
        <p>Popular Tags</p>

        <div class="tag-list">
          @foreach($tags as $tag)
            <a href="{{ route('articles.indexByTag', $tag->name) }}" class="tag-pill tag-default">{{ $tag->name }}</a>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
