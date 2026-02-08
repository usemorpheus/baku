@extends('layouts.activity')

@section('tab_content')
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-primary rounded-2" style="padding: 10px 25px" onclick="checkAndNavigateToTasks()">
            Complete Tasks & Earn Points
            <svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.3242 0.800781L9.42578 1.69922L13.3516 5.625H4.875C4.08073 5.625 3.34831 5.82031 2.67773 6.21094C2.00716 6.60156 1.47656 7.13216 1.08594 7.80273C0.695312 8.47331 0.5 9.20573 0.5 10C0.5 10.7943 0.695312 11.5267 1.08594 12.1973C1.47656 12.8678 2.00716 13.3984 2.67773 13.7891C3.34831 14.1797 4.08073 14.375 4.875 14.375V13.125C4.30208 13.125 3.77799 12.985 3.30273 12.7051C2.82747 12.4251 2.44987 12.0475 2.16992 11.5723C1.88997 11.097 1.75 10.5729 1.75 10C1.75 9.42708 1.88997 8.90299 2.16992 8.42773C2.44987 7.95247 2.82747 7.57487 3.30273 7.29492C3.77799 7.01497 4.30208 6.875 4.875 6.875H13.3516L9.42578 10.8008L10.3242 11.6992L15.3242 6.69922L15.7539 6.25L15.3242 5.80078L10.3242 0.800781Z" fill="#F6F4F3"/>
            </svg>
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-borderless">
            <thead class="text-center">
            <tr>
                <th>Community</th>
                <th>Market Cap</th>
                <th>Price Change</th>
                <th>Community Messages</th>
                <th>Active Members</th>
                <th>Key Builders</th>
                <th>Build Level</th>
                <th>BAKU Interactions</th>
                <th>Community Activities</th>
                <th>Voice Sessions</th>
                <th>Community Sentiment</th>
                <th>Rank Momentum</th>
                <th class="text-end">BAKU Index</th>
            </tr>
            </thead>
            <tbody>
            @foreach($metrics as $metric)
                <tr class="bg-gray">
                    <td>
                        <div class="d-flex gap-2">
                            <img class="rounded-circle" width="30" height="30"
                                 src="{{$metric->chat?->photo ?: asset('images/baku/avatar.png')}}"
                                 alt="">
                            <div class="d-flex flex-column">
                                <div style="font-size: 14px; font-weight: 500;">{{$metric->chat?->title}}</div>
                                <div style="font-size: 11px; color: #888888;">
                                    <a href="#">{{$metric->chat?->id}}</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <strong>{{$metric->market_cap}}</strong>
                    </td>
                    <td class="text-nowrap">
                        @if($metric->change > 0)
                            <span class="text-success">
                                @if($metric->price >= $metric->last_price)
                                +
                                @else
                                -
                                @endif
                                {{$metric->change}} %</span>
                        @else
                            <span class="text-danger">0 %</span>
                        @endif
                    </td>
                    <td class="text-center">{{$metric->group_messages}}</td>
                    <td class="text-center">{{$metric->active_members}}</td>
                    <td class="text-center">{{$metric->key_builders}}</td>
                    <td class="text-center">V{{$metric->builder_level}}</td>
                    <td class="text-center">{{$metric->baku_interactions}}</td>
                    <td class="text-center">{{$metric->community_activities}}</td>
                    <td class="text-center">{{$metric->voice_communications}}</td>
                    <td class="text-center">{{$metric->community_sentiment}}</td>
                    <td class="text-center">{{$metric->ranking_growth_rate}}</td>
                    <td class="text-end">{{$metric->baku_index}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{$metrics->links()}}
    </div>
@endsection
