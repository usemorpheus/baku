<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="style-src 'self' 'unsafe-inline'">
    <title>Baku | Web3 Community Reporter</title>
    @stack('head')
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.5-3-7.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/harmonyos_sans.css')}}">
    <link rel="stylesheet" href="{{asset('assets/baku/main.css')}}">
    @stack('styles')
</head>
<body>

@include('layouts.partials.nav')

@yield('content')

{{--section footer--}}
@include('layouts.partials.footer')

<script src="{{asset('assets/js/jquery-3.7.1.min.js')}}"></script>
@stack('scripts')
</body>
</html>





