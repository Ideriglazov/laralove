<?php

namespace App\Http\Controllers;
use App\Http\Requests\PostRequest;//В этом файле мы создаем условия для длины title, description и формата картинки jpg/jpeg
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index','show');//give non-authorized user access to only index and show methods
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ip = $_SERVER['REMOTE_ADDR'];//Сохраняем ip адрес посетителя в переменную $ip
        $visitor = new Visitor();//Создаем объект класса Visitor(модель Visitor)
        $visitor->ip = $ip;//Записываем в столбец ip таблицы visitors пойманный ip address
        $visitor->save();//Сохраняем внесенные выше изменения в базу данных
        //
        if($request->search)//если мы что-то ввели в строку поиска
        {
            $posts = Post::join('users','author_id','=','users.id')//то мы объединяем таблицы posts и users по столбцам author_id и users.id
                ->where('title','like','%'.$request->search.'%')//где title,description(столбцы из таблицы posts) или name(столбец из таблицы
                ->orWhere('description','like','%'.$request->search.'%')// users) совпадают с результатами поиска
                ->orWhere('name','like','%'.$request->search.'%')
                ->orderBy('posts.created_at','desc')//сортируем посты по дате в убывающем порядке
                ->get();//непонятно зачем нужен этот get.В туториале сказано что здесь пагинации не будет, и поэтому мы пишем этот get
            return view('posts.index',compact('posts'));//передаем во view index.blade массив из тех постов, которые мы ищем в search bar
        }
        $posts = Post::join('users','author_id','=','users.id')//объединяем таблицы posts и users по столбцам author_id и users.id
        ->orderBy('posts.created_at','desc')//сортируем посты по дате в убывающем порядке
        ->paginate(4);//получаем все записи из таблицы Posts(модель управляющая ей называется Post)
        return view('posts.index',compact('posts'));//передаем во view index.blade массив из всех постов из таблицы posts

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*
        if(\Auth::user()->id != null)
        {
            return view('posts.create');
        }
        else
        {
            return redirect()->route('login');
        }
        */
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)//метод принимает в качестве параметра введенные в форму данные в файле create.blade.php, и соблюдает правила прописанные в файле PostRequest
    {
        //
        $post = new Post();//create an object using Post model of a posts table
        $post->title = $request->title;//title равняется тому, что будет введено в поле title
        $post->short_title = Str::length($request->title)>30 ? Str::substr($request->title,0,30).'...':$request->title;//short_title равняется первым 30 символам title и добавится троеточие в конце. Иначе он будет равен title
        $post->description = $request->description;//description равняется тому, что будет введено в поле description
        $post->author_id = rand(1,4);//присваиваем новому посту id случайного автора
        $post->author_id = \Auth::user()->id;//присваиваем новому посту id of the current user(authorized user)
        if($request->file('img'))//если пользователь загрузил картинку
        {
            $path = Storage::putfile('public',$request->file('img'));//помещаем картинку в папку public
            //$path = Storage::disk('public')->putFile('folders/inside/public', $request->file('img'));
            $url = 'public'.Storage::url($path);//сохраняем адрес картинки в переменную $url
            $post->img = $url;//...
        }
        $post->save();//сохраняем все введенные в форму данные в базу данных
        return redirect()->route('post.index')->with('success','Post has been created successfully!');//После сохранения возвращаемся на главную страницу и отображаем флэшку под названием success, с сообщением что Post has been created
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)//метод для отображения одного поста. Параметр $id передается из index.blade
    {
        //$post = Post::find($id);//

        $post = Post::join('users','author_id','=','users.id')->find($id);//объединяем таблицы posts и users по столбцам author_id и users_id и сохраняем все данные по запрашиваемому посту в массив $posts
        if(!$post)//если введен url несуществующего поста
        {
            return redirect()->route('post.index')->withErrors('Post id not found');//возвращаемся на главную страницу и выводим alert(флэшку), что id поста не найден
        }
        else if($post->img == null)//если мы открываем пост в который не загружена картинка
        {
            $post->img = '/img/default.jpg';//записываем в $post->img(элемент массива $post) url дефолтной картинки
        }
        return view('posts.show',compact('post'));//возвращаем массив $post c данными по текущему посту во view posts.show
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)//параметр $id передается сюда из show.blade.php при нажатии кнопки edit post
    {
        //
        $post = Post::find($id);//ищем пост для редактирования в базе данных по полученному id поста. Мы не объединяем таблицы posts и users потому что здесь мы редактируем title, description and image, но не автора поста. Поэтому author id из таблицы users нам не нужен
        if(!$post)//если введен url несуществующего поста
        {
            return redirect()->route('post.index')->withErrors('Post id not found');//возвращаемся на главную страницу и выводим alert(флэшку), что id поста не найден
        }
        if($post->author_id != \Auth::user()->id && \Auth::user()->id != 5)//Даем возможность редактировать пост только его автору и админу. Если id автора поста не равно id залогиневшегося пользователя, либо 5(5 - это id админа)
        {
            return redirect()->route('post.index')->withErrors('You do not have permission to edit this post');//открываем главную страницу и выводим флэшку с сообщением, что вы не можете редактировать данный пост
        }
        return view('posts.edit',compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, $id)//Параметр $request это то что пользователь ввел в форму в form.blade.php, $id - это id текущего поста, переданный сюда из edit.blade.php. Метод соблюдает правила прописанные в PostRequest.php
    {
        //
        $post = Post::find($id);//ищем пост для редактирования в базе данных по полученному id поста. Мы не объединяем таблицы posts и users потому что здесь мы редактируем title, description and image, но не автора поста. Поэтому author id из таблицы users нам не нужен
        if(!$post)//если введен url несуществующего поста
        {
            return redirect()->route('post.index')->withErrors('Post id not found');//возвращаемся на главную страницу и выводим alert(флэшку), что id поста не найден
        }
        if($post->author_id != \Auth::user()->id && \Auth::user()->id != 5)//Даем возможность редактировать пост только его автору и админу. Если id автора поста не равно id залогиневшегося пользователя, либо 5(5 - это id админа)
        {
            return redirect()->route('post.index')->withErrors('You do not have permission to edit this post');//открываем главную страницу и выводим флэшку с сообщением, что вы не можете редактировать данный пост
        }
        $post->title = $request->title;//title равняется тому, что будет введено в поле title
        $post->short_title = Str::length($request->title)>30 ? Str::substr($request->title,0,30).'...':$request->title;//short_title равняется первым 30 символам title и добавится троеточие в конце. Иначе он будет равен title
        $post->description = $request->description;
        if($request->file('img'))//если пользователь загрузил картинку
        {
            $path = Storage::putfile('public',$request->file('img'));//помещаем картинку в папку public(зачем-то)
            $url = Storage::url($path);//сохраняем адрес картинки в переменную $url
            $post->img = $url;//...
        }
        $post->update();//записываем в пост новые, введенные в форму данные вместо старых
        $id = $post->post_id;//получаем id редактируемого поста, чтобы использовать его в следующей строке кода
        return redirect()->route('post.show',compact('id'))->with('success','Post has been edited successfully!');//После редактирования поста вызываем метод show(через route post.show), передаем в него id поста чтобы показать отредактированный пост. И демонстрируем флэшку под названием success
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $post = Post::find($id);//ищем пост для редактирования в базе данных по полученному id поста. Мы не объединяем таблицы posts и users потому что здесь мы редактируем title, description and image, но не автора поста. Поэтому author id из таблицы users нам не нужен
        if(!$post)//если введен url несуществующего поста
        {
            return redirect()->route('post.index')->withErrors('Post id not found');//возвращаемся на главную страницу и выводим alert(флэшку), что id поста не найден
        }
        if($post->author_id == \Auth::user()->id || \Auth::user()->id == 5)//Даем возможность редактировать пост только его автору и админу. Если id автора поста не равно id залогиневшегося пользователя, либо 5(5 - это id админа)
        {
            $post->delete();//удаляем пост
            return redirect()->route('post.index')->with('success','Post has been deleted successfully!');//возвращаемся на главную страницу и выводим alert(флэшку), с сообщением что пост был удален

        }
        else
        {
            return redirect()->route('post.index')->withErrors('You do not have permission to edit this post');//возвращаемся на главную страницу и выводим alert(флэшку), с сообщением что пост у вас нет прав для удаления поста
        }
    }

    public function show_ip()
    {
        $visitors = DB::table('visitors')
            ->select('ip', 'created_at')
            ->orderBy('visitors.created_at','desc')
            ->paginate(30);
        return view('visitors.visitors',compact('visitors'));
    }
}

