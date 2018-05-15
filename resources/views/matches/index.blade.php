@extends('layouts.app')

@section('content')
    <div class="">
        <section class="groups">
            @include('matches._group')
        </section>
        
        <section class="knockouts">
           @include('matches._knockout')
        </section>
    </div>
@endsection
