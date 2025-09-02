<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="style-src 'self' 'unsafe-inline'">
    <title>Baku | Web3 Community Reporter</title>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.5-3-7.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/harmonyos_sans.css')}}">
    <link rel="stylesheet" href="{{asset('assets/baku/main.css')}}">
    <style>
        a, a.nav-link {
            color: unset;
            text-decoration: none;
        }

        .table th {
            font-weight: 400;
            font-size: 12px;
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
</head>
<body>
<div id="landing-page" class="container pt-4">
    <section class="p-4 bg-gray shadow rounded">
        <div class="row gy-3">
            <div class="col-lg-5 col-12">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <img src="{{asset('images/baku/banner.png')}}" width="100%" alt="">
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-lg-7">
                            <button class="btn btn-dark w-100">Invite Baku</button>
                        </div>
                        <div class="col-12 col-lg-5">
                            <button class="btn btn-secondary  w-100">Share</button>
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
                            <a href="?tab=ranking&category={{$category}}"
                               class="fs-6 nav-link {{$tab=='ranking' ? 'active':''}}">Baku community
                                ranking</a>
                        </li>
                        <li class="nav-item">
                            <a href="?tab=points&category={{$category}}"
                               class="fs-6 nav-link {{$tab=='points' ? 'active':''}}">Baku Star Scout
                                Points</a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex gap-1">
                    @foreach($categories as $key => $label)
                        <a href="?category={{$key}}&tab={{$tab}}"
                           class="btn btn-{{$key==$category?'dark':'light'}}">{{$label}}</a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless">
                <thead class="text-center">
                <tr>
                    <th>Rank Name</th>
                    <th>Market Cap</th>
                    <th>Change</th>
                    <th>Group Messages</th>
                    <th>Active Members</th>
                    <th>Key Builders</th>
                    <th>Builder Level</th>
                    <th>Baku Interactions</th>
                    <th>Community activities</th>
                    <th>Voice Communication</th>
                    <th>Community Sentiment</th>
                    <th>Ranking Growth Rate</th>
                    <th>Baku Index</th>
                </tr>
                </thead>
                <tbody>
                <tr class="bg-gray">
                    <td>
                        <div class="d-flex gap-2">
                            <img class="rounded-circle" width="30" height="30" src="{{asset('images/baku/avatar.png')}}"
                                 alt="">
                            <div class="d-flex flex-column">
                                <div style="font-size: 14px; font-weight: 500;">Baku Community</div>
                                <div style="font-size: 11px; color: #888888;">
                                    <a href="#">https://t.me/baku_community</a>
                                </div>
                            </div>
                        </div>

                    </td>
                    <td><strong>$56.4M</strong></td>
                    <td><span class="text-success">+1.08%</span></td>
                    <td>1987</td>
                    <td>205/1024</td>
                    <td><img class="rounded-circle" width="30" height="30" src="{{asset('images/baku/avatar.png')}}"
                             alt=""></td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                </tr>
                <tr class="bg-gray">
                    <td>
                        <div class="d-flex gap-2">
                            <img class="rounded-circle" width="30" height="30" src="{{asset('images/baku/avatar.png')}}"
                                 alt="">
                            <div class="d-flex flex-column">
                                <div style="font-size: 14px; font-weight: 500;">Baku Community</div>
                                <div style="font-size: 11px; color: #888888;">
                                    <a href="#">https://t.me/baku_community</a>
                                </div>
                            </div>
                        </div>

                    </td>
                    <td><strong>$56.4M</strong></td>
                    <td><span class="text-success">+1.08%</span></td>
                    <td>1987</td>
                    <td>205/1024</td>
                    <td><img class="rounded-circle" width="30" height="30" src="{{asset('images/baku/avatar.png')}}"
                             alt=""></td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                    <td>2</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
<script src="{{asset('assets/js/jquery-3.7.1.min.js')}}"></script>
</body>
</html>
