@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-9">
                <div class="card">
                    <div class="card-header">Add Match</div>
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                    <div class="card-body">
                        {!! Form::open(['route'=>'matches.store']) !!}
                            @include('matches._match_form')
                    </div>
                </div>
            </div>
          
        </div>
    </div>
@endsection
