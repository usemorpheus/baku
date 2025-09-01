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
        a {
            color: unset;
            text-decoration: none;
        }

        .hover-card .server-box:hover {
            box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .table th {
            font-weight: 400;
            font-size: 12px;
        }

        table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container pt-4">
    <section class="p-4 border bg-gray rounded">
        <div class="row gy-3">
            <div class="col-lg-5 col-12">
                <div class="d-flex flex-column  gap-3">
                    <div>
                        <img src="{{asset('images/baku/banner.png')}}" width="100%" alt="">
                    </div>
                    <div class="d-flex gap-3">
                        <button class="btn btn-dark w-75">Invite Baku</button>
                        <button class="btn btn-secondary w-25">Share</button>
                    </div>
                    <div>
                        <small
                            class="text-muted">Invite baku to join my telegram community for follow-up coveerage</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-12">
                <div class="d-flex h-100 bg-dark p-4 rounded flex-column justify-content-between text-white">
                    <div class="fs-5 fw-light">Total points distributed</div>
                    <div class="fs-2 fw-bold">
                        13,750,098
                    </div>
                    <div class="row">
                        <div class="col text-center">
                            <small class="opacity-50">Commuities Served</small>
                            <div class="fs-4 fw-medium">720</div>
                        </div>
                        <div class="col text-center">
                            <small class="opacity-50">Commuities Scounts</small>
                            <div class="fs-4 fw-medium">120</div>
                        </div>
                        <div class="col text-center">
                            <small class="opacity-50">News Generated</small>
                            <div class="fs-4 fw-medium">1720</div>
                        </div>
                        <div class="col text-center">
                            <small class="opacity-50">Reports Generated</small>
                            <div class="fs-4 fw-medium">172</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="mt-5">
        <div class="my-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-3">
                    <button class="btn btn-lg btn-dark">Baku community ranking</button>
                    <button class="btn btn-lg btn-light">Baku community ranking</button>
                </div>
                <div class="d-flex gap-1">
                    <button class="btn btn-dark">1d</button>
                    <button class="btn btn-light">7d</button>
                    <button class="btn btn-light">30d</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
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
                <tr>
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
