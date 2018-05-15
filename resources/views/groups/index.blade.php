@extends('layouts.app')

@section('content')
    <div class="container">        
        @forelse($group_teams as $key=>$teams)
            <div class="panel panel-default">
                <div class="panel-heading">{{ $groups[$key-1]->name }}</div>
                <div class="panel-body">   
                    @forelse($teams as $team)
                        <img alt="{{$team->name}}" title="{{$team->name}}" class="i-4-flag flag" src="{{$team->image_path}}" data-src="{{$team->image_path}}"> {{$team->name}}
                    @empty
                    @endif
                </div>                
            </div>
        @empty
        @endforelse
    </div>
@endsection
