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
    </style>

</head>
<body id="landing-page">

{{--top banner--}}
<div class="container-fluid bg-primary text-center text-white p-3">
    <p>@lang('landing.tips') <a href="https://x.com/Baku_agent" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="icon icon-tabler icons-tabler-outline icon-tabler-brand-x">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 4l11.733 16h4.267l-11.733 -16z"/>
                <path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"/>
            </svg>
        </a></p>

</div>

<div class="container pt-2">
    <div class="d-flex align-items-end gap-2 justify-content-end">
        <a class="btn btn-dark" href="{{$news_link}}" target="_blank">News</a>
        <a class="btn btn-dark" href="https://docs.baku.builders" target="_blank">Docs</a>
        <a href="{{$telegram_bot_link}}" class="btn btn-dark" target="_blank">
            <svg width="24" height="24" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="40" height="40" rx="10" fill="#292521"></rect>
                <path
                    d="M31.7059 9.54155C31.2525 9.07192 30.6334 8.79826 29.9809 8.77905C29.6458 8.7797 29.3142 8.84771 29.0059 8.97905L8.2559 17.9666C8.01118 18.0662 7.80434 18.2409 7.6651 18.4655C7.52586 18.6901 7.46137 18.953 7.4809 19.2166V20.0291C7.46748 20.3032 7.54467 20.5741 7.70055 20.8001C7.85643 21.026 8.08234 21.1943 8.3434 21.2791L13.7309 23.0791L15.3559 28.5666C15.4651 28.9512 15.676 29.2994 15.9664 29.5743C16.2567 29.8493 16.6158 30.0409 17.0059 30.1291C17.1553 30.1475 17.3065 30.1475 17.4559 30.1291C18.0103 30.1271 18.5428 29.9123 18.9434 29.5291L20.9309 27.6541L24.7809 30.6916C25.1505 30.9801 25.5938 31.1589 26.0602 31.2076C26.5266 31.2562 26.9972 31.1727 27.4184 30.9666L27.8309 30.7541C28.1837 30.5727 28.49 30.3127 28.7262 29.994C28.9625 29.6754 29.1223 29.3068 29.1934 28.9166L32.4809 11.9166C32.5581 11.4875 32.5277 11.0459 32.3924 10.6315C32.2572 10.217 32.0213 9.84254 31.7059 9.54155ZM27.3559 28.6166C27.3355 28.7245 27.2913 28.8266 27.2264 28.9152C27.1615 29.0039 27.0776 29.077 26.9809 29.1291L26.5684 29.3416C26.4871 29.3829 26.3971 29.4043 26.3059 29.4041C26.1728 29.4016 26.0447 29.353 25.9434 29.2666L21.2434 25.5166C21.1314 25.4173 20.9868 25.3624 20.8371 25.3624C20.6874 25.3624 20.5429 25.4173 20.4309 25.5166L17.6559 28.1291C17.6049 28.1665 17.5441 28.1882 17.4809 28.1916V23.7791C17.4811 23.6924 17.4989 23.6067 17.5333 23.5272C17.5676 23.4477 17.6179 23.376 17.6809 23.3166C21.6684 19.5666 24.0559 17.4666 25.4809 16.2666C25.5262 16.2251 25.5629 16.175 25.5886 16.1192C25.6144 16.0634 25.6287 16.003 25.6309 15.9416C25.6359 15.8815 25.6273 15.821 25.6056 15.7647C25.584 15.7085 25.5499 15.6578 25.5059 15.6166C25.445 15.54 25.3601 15.4861 25.265 15.4635C25.1698 15.4408 25.0698 15.4507 24.9809 15.4916L15.5309 21.4541C15.4469 21.494 15.3551 21.5148 15.2621 21.5148C15.1692 21.5148 15.0773 21.494 14.9934 21.4541L9.3559 19.5291L29.6934 10.7041C29.7674 10.6865 29.8444 10.6865 29.9184 10.7041C30.0089 10.7064 30.0979 10.7277 30.1797 10.7664C30.2614 10.8051 30.3343 10.8605 30.3934 10.9291C30.4787 11.0197 30.5413 11.1293 30.5761 11.2488C30.6108 11.3683 30.6168 11.4943 30.5934 11.6166L27.3559 28.6166Z"
                    fill="#FFF1E3"></path>
            </svg>
        </a>
    </div>
</div>

{{--header--}}
<div class="logo-container">
    <img src="{{asset('images/baku/logo.png')}}" height="40px">
</div>


{{--section terminal--}}
<div class="container text-center">
    <div
        class="d-flex flex-lg-row flex-column align-items-lg-end align-items-center justify-content-center fw-bold mt-5 large-t">
        <p>@lang('landing.title')</p>
        <a href="{{$twitter_link}}" target="_blank"><img src="{{asset('images/baku/hero.png')}}" class="mx-3"
                                                         style="width: 100px"/></a>
        <p class="text-capitalize">@lang('landing.baku')</p>
    </div>

    <p class="mt-5 text-muted fs-3">@lang('landing.shadow_tips')</p>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-sm-12 col-md-12 align-self-center">
            <div class="rounded-4 border border-1" style="background: #F6F4F3; padding: 40px">
                <div class="bg-white rounded d-flex align-items-center">
                    <input rows="1"
                           placeholder="{{$chat_links[0]['label']}}"
                           class="form-control px-3 fs-6 text-muted border-0 shadow-none sh-input"
                           style="padding: 37px"/>
                    <a href="{{$chat_links[0]['link']}}" target="_blank"
                       class="btn btn-dark end-0 top-0 me-3">
                        <i class="las la-arrow-right"></i>
                    </a>
                </div>

                <div class="d-flex flex-wrap gap-3 mt-4 justify-content-center">
                    @foreach($chat_links as $chat_link)
                        @if($loop->first)
                            @continue
                        @endif
                        <div class="btn btn-sh w-lg-40 w-100 py-2"
                             onclick="window.open('{{$chat_links[$loop->index]['link'] ?? ''}}')">
                            {{$chat_links[$loop->index]['label'] ?: '-'}}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{--chat / message--}}
<div class="container-fluid mt-5 py-5 chat-section" style="background: #292521;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="d-flex flex-column flex-xl-row justify-content-between">
                    <div class="chat-left p-40 d-flex flex-column" role="button"
                         onclick="window.open('{{$news_link}}')">
                        <div class="d-flex align-items-center">
                            <svg width="24" height="27" viewBox="0 0 24 27" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.375L5.14286 27L7.42857 14.625H0L6.28571 0H21.7143L15.4286 12.375H24Z"
                                      fill="#E67300"/>
                            </svg>
                            <p class="m-0 fw-bold fs-5 ps-2 text-primary">@lang('landing.buzz_news_tag')</p>
                        </div>
                        <p class="m-0 fw-bolder fs-4 mt-4" style="line-height: 32px; color: #191B1E">
                            @lang('landing.buzz_news_title')
                        </p>

                        <p class="m-0 fs-16 mt-4" style="line-height: 28px; color: #191B1E">
                            @lang('landing.buzz_news_desc')
                        </p>

                        <img class="baku-long" src="{{asset('images/baku/baku-long.png')}}">
                    </div>

                    <div class="chat-right">
                        <div class="chat-right-box1">
                            <img src="{{asset('images/baku/chat-2.png')}}">
                        </div>
                        <div class="chat-right-box2">
                            <img src="{{asset('images/baku/chat.png')}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--section What can Baku do?--}}
<div class="container-fluid text-center py-5 bg-gray-100">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">

                <div class="d-flex align-items-end justify-content-center fw-bold mt-5"
                     style="font-size: 3rem;">
                    <p>@lang('landing.can_do_title')</p>
                </div>

                <p class="mt-2 text-muted fs-5 mb-3">@lang('landing.can_do_sub_title')</p>

                <div class="hover-card d-flex flex-column flex-md-row flex-wrap gap-3 mt-5">

                    <div class="server-box server-1" onclick="window.open('{{$twitter_link}}')">
                        <div class="img-box"><img src="{{asset('images/baku/server.png')}}"></div>
                        <div class="d-flex flex-column justify-content-center p-5" style="height: 210px">
                            <h3 class="fs-2 fw-bolder mb-3">
                                <span>@lang('landing.server_1_title')</span>
                                <svg width="38" height="38" viewBox="0 0 38 38" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect width="38" height="38" rx="10" fill="#292521"/>
                                    <path
                                        d="M15.1818 9.45459H8.5L16.3854 19.9684L8.9295 28.5454H11.4591L17.557 21.5305L22.8182 28.5455H29.5L21.283 17.5895L28.3546 9.45459H25.8251L20.1114 16.0274L15.1818 9.45459ZM23.7727 26.6364L12.3182 11.3637H14.2273L25.6818 26.6364H23.7727Z"
                                        fill="white"/>
                                </svg>
                            </h3>
                            <p class="fs-6 mb-0">@lang('landing.server_1_desc')</p>
                        </div>
                    </div>

                    <div class="server-box server-2 d-flex align-items-center flex-column flex-md-row"
                         onclick="window.open('{{$telegram_bot_link}}')">
                        <div class="d-flex flex-column justify-content-center w-100 w-md-50"
                             style="min-height: 210px;">
                            <h3 class="fs-2 fw-bolder mb-3">
                                <span>@lang('landing.server_2_title')</span>
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect width="40" height="40" rx="10" fill="#292521"/>
                                    <path
                                        d="M31.7059 9.54155C31.2525 9.07192 30.6334 8.79826 29.9809 8.77905C29.6458 8.7797 29.3142 8.84771 29.0059 8.97905L8.2559 17.9666C8.01118 18.0662 7.80434 18.2409 7.6651 18.4655C7.52586 18.6901 7.46137 18.953 7.4809 19.2166V20.0291C7.46748 20.3032 7.54467 20.5741 7.70055 20.8001C7.85643 21.026 8.08234 21.1943 8.3434 21.2791L13.7309 23.0791L15.3559 28.5666C15.4651 28.9512 15.676 29.2994 15.9664 29.5743C16.2567 29.8493 16.6158 30.0409 17.0059 30.1291C17.1553 30.1475 17.3065 30.1475 17.4559 30.1291C18.0103 30.1271 18.5428 29.9123 18.9434 29.5291L20.9309 27.6541L24.7809 30.6916C25.1505 30.9801 25.5938 31.1589 26.0602 31.2076C26.5266 31.2562 26.9972 31.1727 27.4184 30.9666L27.8309 30.7541C28.1837 30.5727 28.49 30.3127 28.7262 29.994C28.9625 29.6754 29.1223 29.3068 29.1934 28.9166L32.4809 11.9166C32.5581 11.4875 32.5277 11.0459 32.3924 10.6315C32.2572 10.217 32.0213 9.84254 31.7059 9.54155ZM27.3559 28.6166C27.3355 28.7245 27.2913 28.8266 27.2264 28.9152C27.1615 29.0039 27.0776 29.077 26.9809 29.1291L26.5684 29.3416C26.4871 29.3829 26.3971 29.4043 26.3059 29.4041C26.1728 29.4016 26.0447 29.353 25.9434 29.2666L21.2434 25.5166C21.1314 25.4173 20.9868 25.3624 20.8371 25.3624C20.6874 25.3624 20.5429 25.4173 20.4309 25.5166L17.6559 28.1291C17.6049 28.1665 17.5441 28.1882 17.4809 28.1916V23.7791C17.4811 23.6924 17.4989 23.6067 17.5333 23.5272C17.5676 23.4477 17.6179 23.376 17.6809 23.3166C21.6684 19.5666 24.0559 17.4666 25.4809 16.2666C25.5262 16.2251 25.5629 16.175 25.5886 16.1192C25.6144 16.0634 25.6287 16.003 25.6309 15.9416C25.6359 15.8815 25.6273 15.821 25.6056 15.7647C25.584 15.7085 25.5499 15.6578 25.5059 15.6166C25.445 15.54 25.3601 15.4861 25.265 15.4635C25.1698 15.4408 25.0698 15.4507 24.9809 15.4916L15.5309 21.4541C15.4469 21.494 15.3551 21.5148 15.2621 21.5148C15.1692 21.5148 15.0773 21.494 14.9934 21.4541L9.3559 19.5291L29.6934 10.7041C29.7674 10.6865 29.8444 10.6865 29.9184 10.7041C30.0089 10.7064 30.0979 10.7277 30.1797 10.7664C30.2614 10.8051 30.3343 10.8605 30.3934 10.9291C30.4787 11.0197 30.5413 11.1293 30.5761 11.2488C30.6108 11.3683 30.6168 11.4943 30.5934 11.6166L27.3559 28.6166Z"
                                        fill="#FFF1E3"/>
                                </svg>

                            </h3>
                            <p class="fs-6 mb-0">@lang('landing.server_2_desc')</p>
                        </div>
                        <div class="img-box">
                            <img src="{{asset('images/baku/baku-3.png')}}">
                        </div>
                    </div>
                    <div class="server-box server-3 d-flex justify-content-start"
                         onclick="window.open('{{$telegram_bot_link}}')">
                        <div class="img-box d-none d-xl-block">
                            <img src="{{asset('images/baku/press.png')}}">
                        </div>
                        <div class="d-flex flex-column justify-content-center text-white w-100 w-md-50"
                             style="padding-left: 8%">
                            <h3 class="fs-2 fw-bolder mb-3">
                                <span>@lang('landing.server_3_title')</span>
                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect width="40" height="40" rx="10" fill="#FFF1E3" fill-opacity="0.2"/>
                                    <path
                                        d="M31.7059 9.54155C31.2525 9.07192 30.6334 8.79826 29.9809 8.77905C29.6458 8.7797 29.3142 8.84771 29.0059 8.97905L8.2559 17.9666C8.01118 18.0662 7.80434 18.2409 7.6651 18.4655C7.52586 18.6901 7.46137 18.953 7.4809 19.2166V20.0291C7.46748 20.3032 7.54467 20.5741 7.70055 20.8001C7.85643 21.026 8.08234 21.1943 8.3434 21.2791L13.7309 23.0791L15.3559 28.5666C15.4651 28.9512 15.676 29.2994 15.9664 29.5743C16.2567 29.8493 16.6158 30.0409 17.0059 30.1291C17.1553 30.1475 17.3065 30.1475 17.4559 30.1291C18.0103 30.1271 18.5428 29.9123 18.9434 29.5291L20.9309 27.6541L24.7809 30.6916C25.1505 30.9801 25.5938 31.1589 26.0602 31.2076C26.5266 31.2562 26.9972 31.1727 27.4184 30.9666L27.8309 30.7541C28.1837 30.5727 28.49 30.3127 28.7262 29.994C28.9625 29.6754 29.1223 29.3068 29.1934 28.9166L32.4809 11.9166C32.5581 11.4875 32.5277 11.0459 32.3924 10.6315C32.2572 10.217 32.0213 9.84254 31.7059 9.54155ZM27.3559 28.6166C27.3355 28.7245 27.2913 28.8266 27.2264 28.9152C27.1615 29.0039 27.0776 29.077 26.9809 29.1291L26.5684 29.3416C26.4871 29.3829 26.3971 29.4043 26.3059 29.4041C26.1728 29.4016 26.0447 29.353 25.9434 29.2666L21.2434 25.5166C21.1314 25.4173 20.9868 25.3624 20.8371 25.3624C20.6874 25.3624 20.5429 25.4173 20.4309 25.5166L17.6559 28.1291C17.6049 28.1665 17.5441 28.1882 17.4809 28.1916V23.7791C17.4811 23.6924 17.4989 23.6067 17.5333 23.5272C17.5676 23.4477 17.6179 23.376 17.6809 23.3166C21.6684 19.5666 24.0559 17.4666 25.4809 16.2666C25.5262 16.2251 25.5629 16.175 25.5886 16.1192C25.6144 16.0634 25.6287 16.003 25.6309 15.9416C25.6359 15.8815 25.6273 15.821 25.6056 15.7647C25.584 15.7085 25.5499 15.6578 25.5059 15.6166C25.445 15.54 25.3601 15.4861 25.265 15.4635C25.1698 15.4408 25.0698 15.4507 24.9809 15.4916L15.5309 21.4541C15.4469 21.494 15.3551 21.5148 15.2621 21.5148C15.1692 21.5148 15.0773 21.494 14.9934 21.4541L9.3559 19.5291L29.6934 10.7041C29.7674 10.6865 29.8444 10.6865 29.9184 10.7041C30.0089 10.7064 30.0979 10.7277 30.1797 10.7664C30.2614 10.8051 30.3343 10.8605 30.3934 10.9291C30.4787 11.0197 30.5413 11.1293 30.5761 11.2488C30.6108 11.3683 30.6168 11.4943 30.5934 11.6166L27.3559 28.6166Z"
                                        fill="white"/>
                                </svg>
                            </h3>
                            <p class="fs-6 mb-0">@lang('landing.server_3_desc')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--section join--}}
<div class="container-fluid text-center py-5 text-white" style="background-color: #E67300">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="d-flex" style="padding: 0 6%">
                    <div class="text-start flex-grow-1">
                        <div class="d-flex align-items-end fw-bold mt-5 mb-0" style="font-size: 3rem;">
                            <p>@lang('landing.join_us_title')</p>
                        </div>

                        <p class="my-4 fs-5">
                            @lang('landing.join_us_desc')
                        </p>

                        <div class="btn btn-dark rounded-1 fs-6 mt-3" style="width: 200px; padding: 11px">
                            @lang('landing.join_us')
                        </div>
                    </div>

                    <img class="d-none d-md-block" src="{{asset('images/baku/baku-2.png')}}"
                         style="width: 304px; object-fit: contain"/>
                </div>
            </div>
        </div>
    </div>
</div>

{{--section news--}}
<div class="container-fluid text-center pt-5" style="background-color: #F6F4F3">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="d-flex align-items-end justify-content-center fw-bold mt-5"
                 style="font-size: 3rem;">
                <p style="color: #292521; opacity: 50%">@lang('landing.news_to_earn_title')</p>
            </div>

            <p class="mt-2 fs-5 mb-4" style="color: #292521; opacity: 50%">
                @lang('landing.news_to_earn_desc')
            </p>

            <img src="{{asset('images/baku/bg-1.png')}}" style="width: 60%; opacity: 20%"/>

        </div>
    </div>
</div>

{{--section bnku--}}
<div class="container-fluid text-start py-5">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center mt-5 gap-4">
                    <div class="rounded-4 border border-1 flex-grow-1"
                         style="background: #F6F4F3; padding: 40px; min-height: 400px;">
                        <div class="d-flex align-items-center gap-4">
                            <h3 class="mb-0 text-primary fw-bold">@lang('landing.$baku')</h3>
                            <div class="btn btn-dark rounded-1 px-3">
                                @lang('landing.coingecko')
                                <svg width="16" height="15" viewBox="0 0 16 15" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.3242 0.800781L9.42578 1.69922L13.3516 5.625H4.875C4.08073 5.625 3.34831 5.82031 2.67773 6.21094C2.00716 6.60156 1.47656 7.13216 1.08594 7.80273C0.695312 8.47331 0.5 9.20573 0.5 10C0.5 10.7943 0.695312 11.5267 1.08594 12.1973C1.47656 12.8678 2.00716 13.3984 2.67773 13.7891C3.34831 14.1797 4.08073 14.375 4.875 14.375V13.125C4.30208 13.125 3.77799 12.985 3.30273 12.7051C2.82747 12.4251 2.44987 12.0475 2.16992 11.5723C1.88997 11.097 1.75 10.5729 1.75 10C1.75 9.42708 1.88997 8.90299 2.16992 8.42773C2.44987 7.95247 2.82747 7.57487 3.30273 7.29492C3.77799 7.01497 4.30208 6.875 4.875 6.875H13.3516L9.42578 10.8008L10.3242 11.6992L15.3242 6.69922L15.7539 6.25L15.3242 5.80078L10.3242 0.800781Z"
                                        fill="#F6F4F3"/>
                                </svg>
                            </div>
                        </div>

                        <p style="color: #6D6561;" class="mt-2">@lang('landing.$baku_desc')</p>

                        <div class="bg-white rounded-3 border border-1 p-4" style="margin-top: 35px">
                            <div
                                class="rounded-3 d-flex flex-column align-items-center justify-content-center text-center px-3"
                                style="background-color: #292521; min-height: 160px">
                                <p class="text-white fs-6 mb-3">@lang('landing.baku_contract_address')</p>
                                <p style="filter: blur(2px);-webkit-filter: blur(2px);color: #F6F4F3; font-size: 14px; opacity: 50%; overflow-x: auto; word-break: break-all">
                                    0x22aF33FE49fD1Fa80c7149773dDe5890D3c76F3b
                                </p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <img src="{{asset('images/baku/baku-bg.png')}}"
                             style="height: 400px; width: 100%; object-fit: contain"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--section baku coingecko--}}
<div class="container-fluid bg-gray-100 py-5">
    <div class="container">
        <div class="row">
            <div
                class="col-sm-12 col-md-12 d-flex flex-column flex-lg-row align-items-center justify-content-center gap-5">
                <img src="{{asset('images/baku/baku-1.png')}}" style="height: 160px;"/>

                <div class="bg-primary rounded-4 w-100 w-md-60" style="padding: 40px; color: #F6F4F3">
                    <div class="fw-bold mb-0 fs-5">
                        <p>@lang('landing.baku_try_title')</p>
                    </div>

                    <p class="my-3">
                        @lang('landing.baku_try_desc')
                    </p>

                    <div class="btn btn-dark rounded-2 mt-3" style="padding: 10px 25px"
                         onclick="window.open('{{$telegram_bot_link}}')">
                        <a href="{{$telegram_bot_link}}" target="_blank"
                           class="pe-1 pt-1">@lang('landing.try_for_free')</a>
                        <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.3242 0.800781L9.42578 1.69922L13.3516 5.625H4.875C4.08073 5.625 3.34831 5.82031 2.67773 6.21094C2.00716 6.60156 1.47656 7.13216 1.08594 7.80273C0.695312 8.47331 0.5 9.20573 0.5 10C0.5 10.7943 0.695312 11.5267 1.08594 12.1973C1.47656 12.8678 2.00716 13.3984 2.67773 13.7891C3.34831 14.1797 4.08073 14.375 4.875 14.375V13.125C4.30208 13.125 3.77799 12.985 3.30273 12.7051C2.82747 12.4251 2.44987 12.0475 2.16992 11.5723C1.88997 11.097 1.75 10.5729 1.75 10C1.75 9.42708 1.88997 8.90299 2.16992 8.42773C2.44987 7.95247 2.82747 7.57487 3.30273 7.29492C3.77799 7.01497 4.30208 6.875 4.875 6.875H13.3516L9.42578 10.8008L10.3242 11.6992L15.3242 6.69922L15.7539 6.25L15.3242 5.80078L10.3242 0.800781Z"
                                fill="#F6F4F3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--section footer--}}
<div class="container-fluid py-5" style="background-color: #292521">
    <div class="container">
        <div class="row">
            <div
                class="col-sm-12 col-md-12 d-flex flex-column flex-md-row align-items-center justify-content-center gap-5">
                <img src="{{asset('images/baku/logo-white.png')}}" style="height: 40px;"/>
                <div class="text-white" style="color: #F6F4F3;">
                    <h3 class="m-0 fs-5">@lang('landing.section_footer_title')</h3>
                    <p>@lang('landing.section_footer_sub_title')</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/jquery-3.7.1.min.js')}}"></script>
</body>
</html>
