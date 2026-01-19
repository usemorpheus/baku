@extends('layouts.app')

@section('content')
    <div style="min-height: 80vh" class="container">
        <section class="p-4 bg-gray shadow rounded">
            <div class="row gy-3">
                <div class="col-lg-5 col-12">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <img src="{{asset('images/baku/banner.png')}}" width="100%" alt="">
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-lg-7">
                                <a class="btn btn-dark w-100" href="{{$telegram_bot_link}}" target="_blank">
                                    Invite Baku
                                </a>
                            </div>
                            <div class="col-12 col-lg-5">
                                <button class="btn btn-secondary w-100">Share</button>
                            </div>
                        </div>
                        <div>
                            <small
                                class="text-muted">Invite baku to join my telegram community for follow-up
                                coveerage</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 col-12">
                    <div class="d-flex h-100 bg-dark p-4 rounded flex-column justify-content-between text-white">
                        <div class="fw-light opacity-75">Total points distributed</div>
                        <div class="fs-2 fw-bold">
                            {{\Illuminate\Support\Number::format($total_points)}}
                        </div>
                        <div class="row">
                            @foreach($kpis as $kpi)
                                <div class="col-lg-3 col-12">
                                    <div class="row">
                                        <div class="col-8 col-lg-12 text-start text-lg-center">
                                            <small class="opacity-50">
                                                {{$kpi['label']}}
                                            </small>
                                        </div>
                                        <div class="col-4 col-lg-12 text-end text-lg-center">
                                            <div class="fs-4 fw-medium">{{$kpi['value']}}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="mt-5">
            <div class="my-3">
                <div class="d-flex gap-3 flex-wrap justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a href="/activity/community?dimension={{$dimension}}"
                                   class="fs-6 nav-link {{$tab=='community' ? 'active':''}}">Baku Community
                                    Ranking</a>
                            </li>
                            <li class="nav-item">
                                <a href="/activity/points?dimension={{$dimension}}"
                                   class="fs-6 nav-link {{$tab=='points' ? 'active':''}}">Baku Star Scout
                                    Points</a>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex gap-1">
                        @foreach($dimensions as $key => $label)
                            <a href="?dimension={{$key}}&tab={{$tab}}"
                               class="btn btn-{{$key==$dimension?'dark':'light'}}">{{$label}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            @yield('tab_content')
        </section>
    </div>
@endsection

@push('styles')
    <style>

        .nav-link {
            color: #888888;
        }

        .nav-link:hover {
            color: #000000;
        }

        .table th {
            font-weight: 400;
            font-size: 12px;
            padding-top: 10px;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        table td {
            vertical-align: middle;
        }

        .bg-gray {
            background-color: #f8f9fa;
        }

        table thead th {
            background-color: #eeeeee !important;
            color: #fff;
            vertical-align: middle;
        }

        /* 左上角、左下角圆角 */
        table thead th:first-child {
            border-top-left-radius: 1rem;
            border-bottom-left-radius: 1rem;
        }

        /* 右上角、右下角圆角 */
        table thead th:last-child {
            border-top-right-radius: 1rem;
            border-bottom-right-radius: 1rem;
        }
    </style>
    <link rel="stylesheet" href="{{asset('assets/baku/news.css')}}">
@endpush
