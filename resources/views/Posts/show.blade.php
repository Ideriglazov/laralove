@extends('layouts.layout', ['title' => "Post №$post->post_id. $post->title"])
@section('content')


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h2>{{  $post->title }}</h2> </div>
                <div class="card-body">
                    <div class="card-img card-img_max" style="background-image: url({{ $post->img ?? asset('img/default.jpg') }})"></div>
                    <div class="card-description">Description: {{$post->description}}</div>
                    <div class="card-author">Author: {{$post->name}}</div>
                    <div class="card-date">Post created at: {{$post->created_at->diffForHumans() }}</div><!--Выводим дату создания поста. Функция diffForHumans, преобразует дату в формат 'Post created at: 1 week ago'-->
                    <div class="card-btn">
                        <a href="{{ route('post.index') }}" class="btn btn-outline-primary">Home page</a>
                        @auth()
                            @if(Auth::user()->id == $post->author_id||(Auth::user()->id == 15))<!--проверяем является ли пользователь открывший пост его автором, сравнивая user id with post id, либо если user id равен 15, то пользователь admin может редактировать и удалять посты-->
                        <a href="{{ route('post.edit', ['id'=>$post->post_id]) }}" class="btn btn-outline-success">Edit post</a>
                        <form action="{{ route('post.destroy',  ['id'=>$post->post_id]) }}" method="post" onsubmit="if(confirm('Are you sure you want to delete this post?')) {return true} else {return false}">
                            @csrf
                            @method('DELETE')
                            <input type="submit" class="btn btn-outline-danger" value="Delete post">
                        </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
