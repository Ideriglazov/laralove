@extends('layouts.layout',['title' => "Create new Post"])
@section('content')
<form action="{{route('post.store')}}" method="post" enctype="multipart/form-data">
   @csrf
    <h3>Create new post</h3>
@include('posts.parts.form')
    <input type="submit" value="Create post" class="btn btn-outline-success">
</form>

    @endsection
