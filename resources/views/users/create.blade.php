@extends('layouts.app')

@section('content')
    <h2>Tambah User</h2>

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        @include('users._form', ['user' => null])
    </form>
@endsection
