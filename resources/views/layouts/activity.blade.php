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
                        <div class="mt-3 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary rounded-2" style="padding: 10px 25px" onclick="checkAndNavigateToTasks()">
                                Complete Tasks & Earn Points
                                <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.3242 0.800781L9.42578 1.69922L13.3516 5.625H4.875C4.08073 5.625 3.34831 5.82031 2.67773 6.21094C2.00716 6.60156 1.47656 7.13216 1.08594 7.80273C0.695312 8.47331 0.5 9.20573 0.5 10C0.5 10.7943 0.695312 11.5267 1.08594 12.1973C1.47656 12.8678 2.00716 13.3984 2.67773 13.7891C3.34831 14.1797 4.08073 14.375 4.875 14.375V13.125C4.30208 13.125 3.77799 12.985 3.30273 12.7051C2.82747 12.4251 2.44987 12.0475 2.16992 11.5723C1.88997 11.097 1.75 10.5729 1.75 10C1.75 9.42708 1.88997 8.90299 2.16992 8.42773C2.44987 7.95247 2.82747 7.57487 3.30273 7.29492C3.77799 7.01497 4.30208 6.875 4.875 6.875H13.3516L9.42578 10.8008L10.3242 11.6992L15.3242 6.69922L15.7539 6.25L15.3242 5.80078L10.3242 0.800781Z" fill="#F6F4F3"/>
                                </svg>
                            </button>
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

@push('scripts')
<style>
.custom-modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; }
.modal-content { background: white; border-radius: 12px; max-width: 500px; width: 90%; padding: 30px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); position: relative; }
.modal-header { margin-bottom: 20px; }
.modal-title { font-size: 1.5rem; font-weight: bold; color: #292521; margin: 0; }
.modal-body { margin-bottom: 25px; color: #6D6561; line-height: 1.6; }
.modal-footer { display: flex; gap: 10px; justify-content: flex-end; }
.btn-modal { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; transition: all 0.2s ease; }
.btn-primary-modal { background-color: #E67300; color: white; }
.btn-primary-modal:hover { background-color: #cc6400; }
.btn-secondary-modal { background-color: #f8f9fa; color: #292521; border: 1px solid #dee2e6; }
.btn-secondary-modal:hover { background-color: #e9ecef; }
.modal-close { position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6D6561; }
.modal-close:hover { color: #292521; }
</style>
<div id="customModal" class="custom-modal" style="display: none;">
    <div class="modal-content">
        <button class="modal-close">&times;</button>
        <div class="modal-header"><h4 class="modal-title">Connect to Telegram Bot</h4></div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer" id="modalFooter"></div>
    </div>
</div>
<script>
function showModal(title, body, buttons) {
    var modal = document.getElementById('customModal');
    var modalTitle = modal.querySelector('.modal-title');
    var modalBody = document.getElementById('modalBody');
    var modalFooter = document.getElementById('modalFooter');
    modalTitle.textContent = title;
    modalBody.innerHTML = body;
    modalFooter.innerHTML = '';
    buttons.forEach(function(button) {
        var btn = document.createElement('button');
        btn.className = 'btn-modal ' + button.style;
        btn.textContent = button.text;
        btn.onclick = button.onClick;
        modalFooter.appendChild(btn);
    });
    modal.style.display = 'flex';
}
function hideModal() { document.getElementById('customModal').style.display = 'none'; }
document.querySelector('#customModal .modal-close').addEventListener('click', hideModal);
document.getElementById('customModal').addEventListener('click', function(e) { if (e.target === this) hideModal(); });
function checkAndNavigateToTasks() {
    fetch('/tasks/verify-auth', { method: 'GET', headers: { 'Content-Type': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.authenticated) { window.location.href = '/tasks'; }
            else {
                showModal('Connect to Telegram Bot', 'To access tasks, you need to connect with our Telegram bot first. Simply send any message (like "hi" or "/start") to the bot.', [
                    { text: 'Cancel', style: 'btn-secondary-modal', onClick: hideModal },
                    { text: 'Open Bot', style: 'btn-primary-modal', onClick: function() {
                        window.open('https://t.me/baku_news_bot', '_blank');
                        showModal('Next Steps', '1. Send any message to the bot (e.g. "hi" or "/start")<br>2. Return to this page<br>3. Click the task button again to access your personalized tasks', [
                            { text: 'OK', style: 'btn-primary-modal', onClick: hideModal }
                        ]);
                    }}
                ]);
            }
        })
        .catch(function() {
            showModal('Connection Required', 'Please connect with Telegram bot first. Start a conversation with @baku_news_bot.', [
                { text: 'Cancel', style: 'btn-secondary-modal', onClick: hideModal },
                { text: 'Open Bot', style: 'btn-primary-modal', onClick: function() { window.open('https://t.me/baku_news_bot', '_blank'); hideModal(); } }
            ]);
        });
}
</script>
@endpush
