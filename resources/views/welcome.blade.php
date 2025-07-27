@php
    $messages = [
        ["title" => "自助新闻",
    "desc" => "——你可以@baku，将撰写的稿件投稿给它，Baku将自动帮你生成新闻海报"],
     ["title" => "跟踪采访",
    "desc" => "——你可以把Baku加入到社区，Baku将作为社区的跟踪记者，自动整理社区故事，进行定期的新闻报道"],

     ["title" => "特约采访",
    "desc" => "——如果你想进行深度报道，可以和Baku进行一次深度对话，Baku将与你进行多轮对话，并整理成长文章进行发布"],

     ["title" => "通过Baku扩展你的社区影响力 ",
    "desc" => "将Baku加入tele社区，让Baku对你的社区进行持续报道，更多KOL将通过你的报道发现社区价值 "],

     ["title" => "news to earn （Comming soon）",
    "desc" => "通过和Baku的交流，评论，投稿，预约采访获得奖励"],
    ];
@endphp
    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Baku | Web3 Community Reporte</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/line-awesome@1.3.0/dist/line-awesome/css/line-awesome.min.css">
    <style @cspNonce>

        :root {
            --bs-primary-rgb: 233, 125, 0;
            --bs-primary-bg-subtle: #e97d00;
            --bs-secondary-bg-subtle: #e97d00;
            --bs-primary-border-subtle: #e97d00;
            --bs-alert-link-color: #fff;
            --bs-primary-text-emphasis: #fff;
            --bs-border-radius: .75rem;
            --bs-border-color-translucent: #e97d001c;
            --bs-btn-border-radius: 8px;
        }

        p {
            margin: 0;
        }

        .baku-face {
            background: #fff url('{{asset('images/baku/face.png')}}') no-repeat center center;
            min-height: 200px;
        }

        .w-150px {
            width: 150px;
        }

        .flex-grow-10 {
            flex-grow: 10;
        }

        .flex-grow-5 {
            flex-grow: 5;
        }

        .bg-purple {
            background-color: #a78bfa;
        }

        .bg-telegram {
            background-color: rgb(52, 170, 233);
            color: white;
        }

        .fs-3rem {
            font-size: 3rem;
        }

        .w-100px {
            width: 100px;
        }

        .btn-sh {
            --bs-btn-color: #51585e;
            --bs-btn-bg: #d1d5db;
            --bs-btn-border-color: #d1d5db;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #FF5D38;
            --bs-btn-hover-border-color: #f5b482;
            --bs-btn-focus-shadow-rgb: 130, 138, 145;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #bd3919;
            --bs-btn-active-border-color: #f5b482;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: #d1d5db;
            --bs-btn-disabled-border-color: #d1d5db;
            --bs-btn-font-size: 14px;
            --bs-btn-border-radius: 8px;
            --bs-btn-border-width: 2px;
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #FF5D38;
            --bs-btn-border-color: #FF5D38;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #bd3919;
            --bs-btn-hover-border-color: #bd3919;
            --bs-btn-focus-shadow-rgb: 49, 132, 253;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #bd3919;
            --bs-btn-active-border-color: #bd3919;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: #FF5D38;
            --bs-btn-disabled-border-color: #FF5D38;
            --bs-btn-border-radius: 8px;
        }

        .large-t {
            font-size: 4.875rem;
            line-height: 4.875rem;
        }

        .bg-gray-100 {
            background-color: oklch(96.7% .003 264.542);
        }

        @media (min-width: 0px) {
            .fit-img {
                max-height: none;
                width: 100%;
            }

            .w-40 {
                width: 40%;
            }

            .w-60 {
                width: 40%;
            }

            .w-100 {
                width: 100%;
            }
        }

        @media (min-width: 576px) {
            .w-sm-40 {
                width: 40%;
            }

            .w-sm-60 {
                width: 40%;
            }
        }

        @media (min-width: 768px) {
            .fit-img {
                max-height: 50vh;
                width: 100%;
            }

            .w-md-40 {
                width: 40%;
            }

            .w-md-60 {
                width: 40%;
            }
        }

        @media (min-width: 992px) {
            .large-t {
                font-size: 4.7rem;
                line-height: 4.7rem;
            }
        }
    </style>
</head>
<body>

{{--top banner--}}
<div class="container-fluid bg-primary text-center text-white p-3">
    <p>你可以@baku，将撰写的稿件投稿给它，Baku将自动帮你生成新闻海报 🤝</p>
</div>

{{--header--}}
<div class="container">
    <div class="bg-dark d-inline-block text-white fw-bold fs-3 px-4 py-1 mt-4">
        baku
    </div>
</div>

{{--section terminal--}}
<div class="container text-center">
    <div
        class="d-flex flex-lg-row flex-column align-items-lg-end align-items-center justify-content-center fw-bold mt-5 large-t">
        <p>Interview with</p>
        <img src="{{asset('images/baku/hero.png')}}" class="mx-3 rotate-hero w-100px"/>
        <p>baku</p>
    </div>

    <p class="mt-5 text-muted fs-3">A shadow reporter for everyone</p>
</div>


<div class="container mt-5">
    <div class="row">
        <div class="col-sm-12 col-md-12 align-self-center">
            <div class="rounded-4 p-4 border border-2 bg-gray-100">
                <div class="bg-white rounded d-flex align-items-center">
                    <input rows="1" class="form-control py-4 px-3 fs-4 text-muted border-0 shadow-none"/>

                    <a target="_blank" href="https://play.baku.builders/chat/share?shareId=wtaEds5ZlNP0r6wSHwRVTnat"
                       class="btn btn-dark end-0 top-0 me-3">
                        <i class="las la-arrow-right"></i>
                    </a>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <div class="btn btn-sh w-50 py-2">
                        我需要个人职业专访
                    </div>
                    <div class="btn btn-sh flex w-50 py-2">
                        我要评论一个项目
                    </div>
                </div>
                <div class="d-flex gap-2 mt-2">
                    <div class="btn btn-sh w-50 py-2">
                        我有一个重要观点要发布
                    </div>
                    <div class="btn btn-sh w-50 py-2">
                        我需要个人职业专访
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--chat / message--}}
<div class="container-fluid bg-primary mt-5 py-5">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="d-flex flex-column flex-md-row gap-3">
                    <div class="text-center flex-grow-10">
                        <img src="{{asset("images/baku/chat.png")}}"
                             class="img-fluid object-fit-cover rounded w-100" alt="">
                    </div>
                    <div class="text-start flex-grow-5">
                        <img src="{{asset("images/baku/message.png")}}"
                             class="img-fluid object-fit-cover rounded w-100" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--section message--}}
<div class="container-fluid text-center py-5 bg-gray-100">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">

                <div class="d-flex align-items-end justify-content-center fw-bold mt-5 fs-3rem">
                    <p>What can Baku do?</p>
                </div>

                <p class="mt-2 text-muted fs-3">baku can adapt to various interview scenarios. Come and experience it
                    now</p>

                <div class="d-flex gap-3 text-start mt-5">
                    <div class="border border-2 rounded w-50 pt-4 pb-3 px-3">
                        <h4>自助新闻</h4>
                        <p>——你可以在twitter@baku，将撰写的稿件投稿给它，baku将自动帮你生成新闻海报</p>
                    </div>
                    <div class="border border-2 rounded w-50 pt-4 pb-3 px-3">
                        <h4>特约采访</h4>
                        <p>
                            ——如果你想进行深度报道，可以和baku进行一次深度对话，baku将与你进行多轮对话，并整理成长文章进行发布</p>
                        <div class="text-end mt-1 ">
                            <i class="lab la-telegram p-1 rounded-1 fs-5 bg-telegram"></i>
                        </div>
                    </div>
                </div>
                <div class="border border-2 rounded w-100 pt-5 pb-4 px-3 text-start mt-3 mb-5">
                    <h4>跟踪采访</h4>
                    <p>——你可以把baku加入到社区，baku将作为社区的跟踪记者，自动整理社区故事，进行定期的新闻报道</p>
                    <div class="text-end mt-1">
                        <i class="lab la-telegram p-1 rounded-1 fs-5 bg-telegram"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--section join--}}
<div class="container-fluid text-center py-5 text-white bg-dark">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">

                <div class="d-flex align-items-end justify-content-center fw-bold mt-5 fs-3rem">
                    <p>Join Baku Community</p>
                </div>

                <p class="mt-2 fs-3 mb-4">
                    Join the baku community and exchange the latest industry<br>news with other friends
                </p>

                <div class="text-end mt-1 me-4">
                    <i class="lab la-telegram p-1 rounded-1 fs-5 bg-telegram"></i>
                </div>

            </div>
        </div>
    </div>
</div>

{{--section news--}}
<div class="container-fluid text-center py-5 bg-gray-100">
    <div class="row">
        <div class="col-sm-12 col-md-12">

            <div class="d-flex align-items-end justify-content-center fw-bold mt-5 fs-3rem">
                <p>News To Earn</p>
            </div>

            <p class="mt-2 fs-3 mb-4">
                Earn rewards by completing high-quality<br>interviews or news content<br>(comming soon)
            </p>
        </div>
    </div>
</div>

{{--section bnkr--}}
<div class="container-fluid text-start py-5">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="d-flex align-items-end justify-content-center mt-5">
                    <div class="d-flex flex-column flex-md-row gap-3 w-100">
                        <div class="bg-gray-100 rounded p-4 w-100 w-sm-40">
                            <h5 class="fs-3">$baku</h5>
                            <p class="fs-5">$Baku是baku的原生代币，我们还在研究它的使用场景</p>
                            <div class="bg-dark d-inline-block text-white fs-5 px-4 py-1 pb-2 my-3 rounded-1">
                                coingecko
                            </div>
                            <div class="bg-white rounded p-3 text-center">
                                <div class="btn btn-primary text-white w-100">
                                    baku contract address on bonk
                                </div>
                                <p class="text-muted opacity-50 fw-bold pt-1">
                                    <small>0x22aF33FE49fD1Fa80c7149773dDe5890D3c76F3b</small>
                                    <i class="lar la-copy"></i>
                                </p>
                            </div>
                        </div>
                        <div class="bg-gray-100 rounded p-4 w-100 w-sm-60 baku-face">
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 d-flex gap-3 ">
                    <img src="{{asset('images/baku/hero-2.png')}}" class="rounded w-150px"
                         alt=""/>

                    <div class="rounded p-3 w-50 bg-purple">
                        <h5>Try banker free</h5>
                        <p>Get up to 10 messages per day for free!</p>
                        <div class="bg-dark d-inline-block text-white fs-5 px-4 py-1 pb-2 my-3 rounded-1">
                            TRY FOR FREE <i class="las la-share ps-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{--section footer--}}
<div class="container-fluid bg-gray-100 py-5">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12 d-flex align-items-center gap-3">
                <div class="bg-dark d-inline-block text-white fs-5 px-4 py-2 my-3 rounded-1">
                    baku
                </div>
                <div>
                    <h4 class="fs-3 m-0">baku agent</h4>
                    <p>A shadow reporter for everyone</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="{{asset('js/gsap.min.js')}}"></script>
<script src="{{asset('js/ScrollTrigger.min.js')}}"></script>
<script @cspNonce>
    $(function () {
        gsap.registerPlugin(ScrollTrigger);

        gsap.to($(".rotate-hero"), {
            rotationY: 360,
            duration: 2,
            ease: "none",
            repeat: -1,
            force3D: true
        });

        const animatedElements = $(".gsap-slide");
        gsap.set(animatedElements, {opacity: 0, y: 100});
        ScrollTrigger.batch(animatedElements, {
            start: 'top bottom',
            once: false,
            onEnter: elements => {
                gsap.to(elements, {
                    opacity: 1,
                    y: 0,
                    duration: 1,
                    ease: "power1.out",
                    stagger: {
                        each: 0.2,
                        ease: "power1.out"
                    },
                    overwrite: "auto"
                });
            }, // 滚动到进入
        });
    })
</script>
</body>
</html>
