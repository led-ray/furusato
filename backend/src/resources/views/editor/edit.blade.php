@extends('layout')

@section('content')

<div class="container page">
  <div class="row">
    <div class="col-md-10 offset-md-1 col-xs-12">

    {{-- 記事編集フォーム --}}
      <form method="POST" action="{{ route('articles.update', $article->id) }}">
        @csrf
        @method('PUT')
        <fieldset>
          <fieldset class="form-group">
            <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="Article Title" value="{{ old('title', $article->title) }}" />
          </fieldset>
          <fieldset class="form-group">
            <input type="text" class="form-control" id="description" name="description" placeholder="What's this article about?" value="{{ old('description', $article->description) }}" />
          </fieldset>
          <fieldset class="form-group">
            <textarea
              id="body"
              name="body"
              class="form-control"
              rows="8"
              placeholder="Write your article (in markdown)"
            >{{ old('body', $article->body) }}</textarea>
          </fieldset>
          <fieldset class="form-group">

            <input type="text" class="form-control" id="tags" name="tags" placeholder="Enter tags" value="{{ implode(',', $article->tags->pluck('name')->toArray()) }}" />
          </fieldset>
          <button class="btn btn-lg pull-xs-right btn-primary" type="submit">
            Update Article
          </button>
        </fieldset>
      </form>
    </div>
  </div>
</div>
@endsection
