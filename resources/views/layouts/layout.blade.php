<!doctype html>
<html lang="eng">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalabele=no, initial-scale=1.0,maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="x-ua-compatible" content="ie-edge">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('img/light_PNG14440.png') }}"><!--Вставляем картинку favicon из папки public/img-->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">


    <div class="container collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="col-6 navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item active offset-3">
                <a class="nav-link" href="{{ route('post.create') }}">Create post <span class="sr-only">(current)</span></a><!--Когда нажимаем на кнопку Create Post, вызывается метод create из PostController, который открывает view create.blade.php-->
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0" action="{{ route('post.index') }}"><!--Когда что-то вводим в строку поиска вызывается метод index из PostController-->
            <input class="form-control mr-sm-2" name="search" type="search" placeholder="Search post" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
        <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
            @guest<!--Если зашел как гость выводятся кнопки с login and register-->
                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @endif

                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else<!--Если пользователь зашел залогиненным выводим то что ниже-->
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>
<?php
//echo "<pre>";
//var_dump($posts);
//echo "</pre>";
?>
<div class="container">
@if($errors->any())
    @foreach($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{$error}}<!--Выводим сообщение об ошибке-->
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span area-hidden="true">&times;</span>
        </button>
    </div>
        @endforeach
@endif
    @if(session('success'))<!--Если пост успешно создан(метод store в PostController сработал успешно-->
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success')}}<!--Выводим зеленую флэшку с сообщением и крестиком справа, чтобы его закрыть-->
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span area-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @yield('content')
</div>
<script src="{{ asset('js/app.js') }}"></script><!--подключаем скрипты, это нужно чтобы срабатывал крестик во флешке при создании поста-->
</body>
</html>
