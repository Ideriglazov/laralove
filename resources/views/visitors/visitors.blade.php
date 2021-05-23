@extends('layouts.layout', ['title' => 'Home page'])
@section('content')
    <div class="row">
        @foreach ($visitors as $visitor)
            <div class="col-6">
                <div class="card">
                    <div class="card-header"><h2>IP address: {{  $visitor->ip }}</h2> </div>
                    <div class="card-body">
                        <div class="card-author">Visited at: {{$visitor->created_at}}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $visitors->links() }}

@endsection
