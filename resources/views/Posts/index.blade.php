@extends('layouts.layout', ['title' => 'Home page'])
@section('content')
{{ 'Displaying a random text to test git commands' }}
@if(isset($_GET['search']))
        @if(count($posts)>0)
            <h2>Search results on request "<?= $_GET['search']?>"</h2>
            <p class="lead">{{ count($posts) }} posts have been found</p>
    @else
            <h2>On request "<?=htmlspecialchars($_GET['search'])?>" nothing has been found</h2><!--экранируем результаты поиска поста. Функция htmlspecialchars используется для защиты от использования html characters in the search bar-->
            <a href="{{ route('post.index')}}" class="btn btn-outline-primary">Display all posts</a>
        @endif
    @endif
    <div class="row">
        @foreach ($posts as $post)
        <div class="col-6">
            <div class="card">
                <div class="card-header"><h2>{{  $post->short_title }}</h2> </div>
                <div class="card-body">
                    <div class="card-img" style="background-image: url({{ $post->img ?? asset('img/default.jpg') }})"></div>
                    <div class="card-author">Author: {{$post->name}}</div>
                    <a href="{{route('post.show',['id'=> $post->post_id])}}" class="btn btn-outline-primary">View post</a><!--Вызываем route, под названием post.show(из файла web.php), который вызовет метод show из PostController.php, и передаем в него параметр 'id', значение которого берется из столбца post_id из таблицы posts-->
                </div>
            </div>
        </div>
            @endforeach
    </div>
    @if(!isset($_GET['search']))
    {{ $posts->links() }} <!-- Если мы пытались найти посты в строке поиска, то пагинация не выводится-->
    @endif

@endsection
