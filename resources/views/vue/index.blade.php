<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.9.0/css/flag-icon.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Worldcup 2018 - Russia</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  </head>
  <body>
    <noscript>
      <strong>We're sorry but picker doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>
    </noscript>
    <div id="vue_app"></div>
    <input type="hidden" id="baseurl" name="baseurl" value="{{ route('root') }}" />   
    <script src="{{ asset('js/app.js') }}" defer></script>
  </body>
</html>
