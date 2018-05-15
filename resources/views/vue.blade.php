@extends('layouts.app')
@section('content')
<noscript>
      <strong>We're sorry but picker doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
    </noscript>
    <div id="vue_app"></div>
@endsection

@section('scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endsection