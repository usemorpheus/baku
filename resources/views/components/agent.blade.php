@php
    $agent = $self->getModel();
@endphp

<div class="card maxw-384px minw-144px">
    <div class="card-body">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <span class="avatar avatar-lg overflow-hidden">
                    <img src="{{$agent->image}}" alt="">
                </span>
            </div>
            <div class="col ps-2">
                <h4 class="card-title mb-1">
                    <a href="#">
                        <i class="ri-checkbox-blank-circle-fill text-{{$agent->status}}"></i>
                        {{$agent->name}}
                    </a>
                </h4>
                <div class="text-secondary maxh-36px overflow-hidden">{{$agent->description}}</div>
            </div>
            <div class="col-auto">
                <div class="dropdown">
                    <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/dots-vertical -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="icon icon-1">
                            <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                            <path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                            <path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="{{admin()->route('agents.edit', $agent)}}"
                           class="dropdown-item">{{__('merlion::base.edit')}}</a>
                        <a data-action="{{admin()->route('agents.delete', $agent->id)}}" data-method="delete"
                           data-confirm="Delete?" class="dropdown-item text-danger">{{__('merlion::base.delete')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
