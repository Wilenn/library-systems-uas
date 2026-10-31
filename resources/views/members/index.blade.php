@extends('layouts.app')

@section('content')

    <h2>Daftar Member</h2>

    <ul>
        @forelse($members as $member)
            <li>{{ $member }}</li>
        @empty
            <li>Belum ada member.</li>
        @endforelse
    </ul>

@endsection