@extends('layouts.app')

@php
    $categories = [
        'private_interview' => 'Private Interview',
        'group_report'      => 'Group report',
        'buzz_news'         => 'Buzz news',
    ];
@endphp

@section('content')
    <div class="container" style="min-height: 70vh">
        <h2 class="mb-5"><a href="{{route('articles.index')}}"> 🔙 </a></h2>

        @if(!$article->published)
            <div class="alert alert-success">
                <h4>This news is not published yet.</h4>
                <a href="{{route('articles.publish', $article->uuid)}}" class="btn btn-primary">Publish now</a>
            </div>
        @endif

        <article class="mb-5 article">
            <h4 class="fs-4 m-0">{{$article->title}}</h4>
            <span class="badge bg-primary">{{$categories[$article->category]??''}}</span>

            @if($article->image)
                <div class="my-3">
                    <img src="{{$article->image}}" alt="" style="max-width: 100%;">
                </div>
            @endif
            <div class="mt-3">
                {!! $article->content !!}
            </div>
            <div class="mt-3 text-muted">
                <i class="lar la-clock"></i>
                <span>{{$article->created_at}}</span>

                @if($article->author)
                    <i class="las la-microphone"></i>
                    <span>{{$article->author ?: '-'}}</span>
                @endif
                <i class="las la-eye"></i>
                <span>{{$article->read_count??0}}</span>
            </div>
        </article>
    </div>
@endsection

@pushonce('head')
    @if($article->image)
        <meta name="twitter:card" content="summary"/>
        <meta name="twitter:image" content="{{$article->image}}"/>
    @endif
    <meta name="twitter:title" content="{{$article->title}}"/>
@endpushonce

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/baku/news.css')}}">
@endpush
