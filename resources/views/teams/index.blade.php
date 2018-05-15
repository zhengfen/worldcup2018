@extends('layouts.app')

@section('content')
    <div class="container">        
        @forelse($teams as $team)
            <div class="panel panel-default">
                <div class="panel-heading">{{$team->name}}</div>
                <div class="panel-body"> <img alt="{{$team->name}}" title="{{$team->name}}" class="i-4-flag flag" src="{{$team->image_path}}" data-src="{{$team->image_path}}"> </div>
                <div class="panel-footer">{{$team->group->name}}</div>
            </div>
        @empty
        @endif
    </div>
@endsection
