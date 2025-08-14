@extends('layouts.app')

@section('content')
    <div class="container" style="min-height: 70vh">
        <h2 class="mb-5">News</h2>

        @foreach($articles as $model)
            <article class="mb-5 article">
                <a href="{{route('news.show', $model->uuid)}}">
                    <h4 class="fs-4 m-0">{{$model->title}}</h4>
                </a>
                <p class="mt-2">
                    {{\Illuminate\Support\Str::limit(strip_tags($model->content), 200, '...')}}
                    <a class="text-primary" href="{{route('news.show', $model->uuid)}}">继续阅读</a>
                </p>
                <div class="mt-3 text-muted">
                    <i class="lar la-clock"></i>
                    <span>{{$model->created_at}}</span>
                    <i class="las la-eye"></i>
                    <span>{{$model->read_count??0}}</span>
                </div>
            </article>
        @endforeach

        <div>
            {{$articles->links()}}
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/baku/news.css')}}">
@endpush
