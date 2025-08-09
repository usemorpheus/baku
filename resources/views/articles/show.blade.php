@extends('layouts.app')

@section('content')
    <div class="container" style="min-height: 70vh">
        <h2 class="mb-5"><a href="/news"> 🔙 </a></h2>

        <article class="mb-5 article">
            <a href="{{route('news.show', $model)}}">
                <h4 class="fs-4 m-0">{{$model->title}}</h4>
            </a>
            <p class="mt-2">
                {{$model->content}}
            </p>
            <div class="mt-3 text-muted">
                <i class="lar la-clock"></i>
                <span>{{$model->created_at}}</span>
                <i class="las la-eye"></i>
                <span>{{$model->read_count??0}}</span>
            </div>
        </article>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/baku/news.css')}}">
@endpush
