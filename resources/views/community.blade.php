@extends('layouts.activity')

@section('tab_content')
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
                        <strong>${{Number::forHumans(is_null($metric->market_cap)?0:$metric->market_cap, abbreviate:true)}}</strong>
                    </td>
                    <td class="text-nowrap">
                        @if($metric->change > 0)
                            <span class="text-success">+{{$metric->change}} %</span>
                        @else
                            <span class="text-danger">0 %</span>
                        @endif
                    </td>
                    <td class="text-center">{{$metric->group_messages}}</td>
                    <td class="text-center">{{$metric->active_members}}</td>
                    <td class="text-center"><img class="rounded-circle" width="30" height="30"
                                                 src="{{asset('images/baku/avatar.png')}}"
                                                 alt=""></td>
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
