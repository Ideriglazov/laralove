@extends('layouts.layout',['title' => "Error 404. Incorrect URL"])
@section('content')
    <div class="card">
        <h2 class="card-header">Error 404</h2>
        <img src="{{ asset('img/pigeon.jpg') }}" alt="pigeon">
    </div>

    <a href="/" class="btn btn-outline-primary">Hurry back to the home page</a>
@endsection
