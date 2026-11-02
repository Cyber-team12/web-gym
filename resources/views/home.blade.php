@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-8">
            <div class="card glow-card">
                <span class="card-star">✦</span>
                <div class="card-header border-bottom border-secondary border-opacity-25 bg-transparent py-3 fw-bold">
                    <span class="text-warning me-1">✦</span> {{ __('Dashboard') }}
                </div>

                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="mb-0">{{ __('You are logged in!') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection