@php
    $agent = $self->getModel();
@endphp
<div class="card card-height-100 minw-288px maxw-384px">
    <div class="card-body">
        <div class="d-flex flex-column h-100">
            <div class="d-flex">
                <div class="flex-grow-1">
                    <p class="text-muted mb-4">{{\Carbon\Carbon::parse($agent->last_active_at)->diffForHumans()}}</p>
                </div>
                <div class="flex-shrink-0">
                    <div class="d-flex gap-1 align-items-center">
                        <div class="dropdown">
                            <button
                                class="btn btn-link text-muted p-1 mt-n2 py-0 text-decoration-none fs-15 material-shadow-none"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" class="feather feather-more-horizontal icon-sm">
                                    <circle cx="12" cy="12" r="1"></circle>
                                    <circle cx="19" cy="12" r="1"></circle>
                                    <circle cx="5" cy="12" r="1"></circle>
                                </svg>
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{admin()->route('agents.edit', $agent)}}"><i
                                        class="ri-pencil-fill align-bottom me-2 text-muted"></i> {{__('merlion::base.edit')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-sm">
                        <span class="avatar-title bg-primary-subtle rounded">
                            <img src="{{$agent->image}}" alt="" class="img-fluid">
                        </span>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1 fs-15"><a href="#" class="text-body">{{$agent->name}}</a></h5>
                    <p class="text-muted text-truncate-two-lines minh-48px">{{$agent->description}}</p>
                </div>
            </div>
        </div>
    </div>
</div>
