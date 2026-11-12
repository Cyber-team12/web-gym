@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="card glow-card p-4 mx-auto" style="max-width: 800px;">
        <h2 class="mb-4">Tambah Pengguna Baru</h2>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users._form')
        </form>
    </div>
</div>
@endsection
