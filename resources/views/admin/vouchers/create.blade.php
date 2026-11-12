@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card glow-card p-4 mx-auto" style="max-width: 800px;">
        <h2 class="mb-4">Tambah Voucher</h2>
        <form method="POST" action="{{ route('admin.vouchers.store') }}">
            @csrf
            @include('admin.vouchers._form')
        </form>
    </div>
</div>
@endsection
