@extends('layouts.app')

@section('content')
    <div class="container" style="min-height: 70vh">
        <h2 class="mb-5"><a href="/news"> 🔙 </a></h2>

        <article class="mb-5 article">
            <h4 class="fs-4 m-0">{{$article->title}}</h4>
            <div class="mt-3">
                {!! $article->content !!}
            </div>
            <div class="mt-3 text-muted">
                <i class="lar la-clock"></i>
                <span>{{$article->created_at}}</span>
                <i class="las la-eye"></i>
                <span>{{$article->read_count??0}}</span>
            </div>
        </article>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/baku/news.css')}}">
@endpush
