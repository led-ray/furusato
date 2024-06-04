@extends('layout')

@section('content')

<div class="container page">
  <div class="row">
    <div class="col-md-10 offset-md-1 col-xs-12">

      <form method="POST" action="{{ route('articles.store') }}">
        @csrf
        <fieldset>
          <fieldset class="form-group">
            <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="Article Title" />
          </fieldset>
          <fieldset class="form-group">
            <input type="text" class="form-control" id="description" name="description" placeholder="What's this article about?" />
          </fieldset>
          <fieldset class="form-group">
            <textarea
              id="body"
              name="body"
              class="form-control"
              rows="8"
              placeholder="Write your article (in markdown)"
            ></textarea>
          </fieldset>
          <fieldset class="form-group">
            <input type="text" class="form-control" id="tags" name="tags"  placeholder="Enter tags" />
          </fieldset>
          <button class="btn btn-lg pull-xs-right btn-primary"  type="submit">
            Publish Article
          </button>
        </fieldset>
      </form>
    </div>
  </div>
</div>
@endsection