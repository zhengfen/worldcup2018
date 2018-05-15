@extends('layouts.app')

@section('content')
    <div class="">
        <section class="groups">
            @include('pronostics._group')
        </section>
        
        <section class="knockouts">
           @include('pronostics._knockout')
        </section>
    </div>
@endsection


