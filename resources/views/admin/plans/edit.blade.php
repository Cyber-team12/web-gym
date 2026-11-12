@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card glow-card p-4 mx-auto" style="max-width: 800px;">
        <h2 class="mb-4">Ubah Paket Membership: {{ $plan->name }}</h2>
        <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
            @csrf
            @method('PUT')
            @include('admin.plans._form')
        </form>
    </div>
</div>
@endsection
