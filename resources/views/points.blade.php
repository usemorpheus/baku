@extends('layouts.activity')

@section('tab_content')
    <div class="table-responsive">
        <table class="table table-borderless">
            <thead class="text-center">
            <tr>
                <th>Name</th>
                <th>Total baku shares</th>
                <th>Directly introduced total baku</th>
                <th>Community overrall level</th>
                <th class="text-end">Earn points</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <div class="d-flex gap-2">
                        <img class="rounded-circle" width="30" height="30"
                             src="{{asset('images/baku/avatar.png')}}"
                             alt="">
                        <div class="d-flex flex-column">
                            <div style="font-size: 14px; font-weight: 500;">Alex</div>
                            <div style="font-size: 11px; color: #888888;">
                                @alex
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center">10</td>
                <td class="text-center">33</td>
                <td class="text-center">V2</td>
                <td class="text-end">31,345</td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
