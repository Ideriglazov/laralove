<?php

namespace App\Http\Controllers;
use App\Http\Requests\PostRequest;
use App\models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        //
        if($request->search)//если мы что-то ввели в строку поиска
        {
            $posts = Post::join('users','author_id','=','users.id')//то мы объединяем таблицы posts и users по столбцам author_id и users.id
                ->where('title','like','%'.$request->search.'%')//где title,description или name совпадает с
                ->orWhere('description','like','%'.$request->search.'%')//результатами поиска
                ->orWhere('name','like','%'.$request->search.'%')
                ->orderBy('posts.created_at','desc')//сортируем посты по дате в убывающем порядке
                ->get();//непонятно зачем нужен этот get.В туториале сказано что здесь пагинации не будет, и поэтому мы пишем этот get
            return view('posts.index',compact('posts'));
        }
        $posts = Post::join('users','author_id','=','users.id')
        ->orderBy('posts.created_at','desc')
        ->paginate(4);//получаем все записи из таблицы Posts(модель управляющая ей называется Post)
        return view('posts.index',compact('posts'));
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
    public function store(PostRequest $request)
    {
        //
        $post = new Post();//create an object using Post model of a posts table
        $post->title = $request->title;//title равняется тому, что будет введено в поле title
        $post->short_title = Str::length($request->title)>30 ? Str::substr($request->title,0,30).'...':$request->title;//short_title равняется первым 30 символам title и добавится троеточие в конце. Иначе он будет равен title
        $post->description = $request->description;
        $post->author_id = rand(1,4);//присваиваем новому посту id случайного автора
        $post->author_id = \Auth::user()->id;//присваиваем новому посту id of the current user(authorized user)
        if($request->file('img'))//если пользователь загрузил картинку
        {
            $path = Storage::putfile('public',$request->file('img'));//помещаем картинку в папку public(зачем-то)
            $url = Storage::url($path);//сохраняем адрес картинки в переменную $url
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
    public function show($id)
    {
        //$post = Post::find($id);//

        $post = Post::join('users','author_id','=','users.id')->find($id);
        if(!$post)
        {
            return redirect()->route('post.index')->withErrors('Post id not found');
        }
        return view('posts.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $post = Post::find($id);
        if(!$post)
        {
            return redirect()->route('post.index')->withErrors('Post id not found');
        }
        if($post->author_id != \Auth::user()->id)
        {
            return redirect()->route('post.index')->withErrors('You do not have permission to edit this post');
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
    public function update(PostRequest $request, $id)
    {
        //
        $post = Post::find($id);
        if(!$post)
        {
            return redirect()->route('post.index')->withErrors('Post id not found');
        }
        if($post->author_id != \Auth::user()->id)
        {
            return redirect()->route('post.index')->withErrors('You do not have permission to edit this post');
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
        $post = Post::find($id);
        if(!$post)
        {
            return redirect()->route('post.index')->withErrors('Post id not found');
        }
        if($post->author_id != \Auth::user()->id)
        {
            return redirect()->route('post.index')->withErrors('You do not have permission to edit this post');
        }
        $post->delete();
        return redirect()->route('post.index')->with('success','Post has been deleted successfully!');
    }
}
