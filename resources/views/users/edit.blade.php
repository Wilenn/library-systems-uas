@extends('layouts.app')

@section('title', 'Edit pengguna')

@section('content')
    <x-page-header title="Edit pengguna" :subtitle="$user->email" />

    <x-card class="max-w-xl">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            @include('users._form')
        </form>
    </x-card>
@endsection
