@extends('layouts.app')

@section('title', 'Edit anggota')

@section('content')
    <x-page-header title="Edit anggota" :subtitle="$member->name" />

    <x-card class="max-w-xl">
        <form action="{{ route('members.update', $member) }}" method="POST">
            @csrf
            @method('PUT')
            @include('members._form')
        </form>
    </x-card>
@endsection
