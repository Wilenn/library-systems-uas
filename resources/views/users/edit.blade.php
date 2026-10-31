@extends('layouts.app')

@section('content')
    <h2>Edit User</h2>

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        @include('users._form')
    </form>
@endsection
