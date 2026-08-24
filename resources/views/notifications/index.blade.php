@extends('partials.layouts.master')

@section('title', 'Notifications')

@section('content')
    <div id="layout-wrapper">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-semibold">Notifications</h6>
                <form action="{{ route('notifications.marquer-tout-lu') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light-primary btn-sm">Tout marquer comme lu</button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse ($notifications as $notification)
                        @php
                            $c = in_array($notification->data['couleur'] ?? '', ['primary','success','danger','warning','info','secondary']) ? $notification->data['couleur'] : 'secondary';
                        @endphp
                        <form action="{{ route('notifications.marquer-lu', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action d-flex gap-3 py-3 border-0 w-100 text-start {{ $notification->read_at ? '' : 'bg-light-subtle' }}">
                                <div class="avatar-md d-flex align-items-center justify-content-center bg-{{ $c }}-subtle text-{{ $c }} fs-16 flex-shrink-0 rounded-circle">
                                    <i class="bi {{ $notification->data['icone'] ?? 'bi-bell' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fs-14">
                                        {{ $notification->data['titre'] ?? '' }}
                                        @unless ($notification->read_at)
                                            <span class="badge bg-danger rounded-circle p-1 ms-1" style="font-size:6px;vertical-align:middle;"></span>
                                        @endunless
                                    </h6>
                                    <p class="text-muted mb-1 fs-13">{{ $notification->data['message'] ?? '' }}</p>
                                    <p class="text-muted mb-0 fs-11">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </button>
                        </form>
                    @empty
                        <p class="text-muted text-center py-6 mb-0">Aucune notification pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
    </main>
@endsection

@section('js')
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
