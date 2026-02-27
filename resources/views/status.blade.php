@extends('layouts.app')

@section('title', 'System Status — Hubzero')

@section('content')
<h1 class="text-2xl font-semibold mb-6">System Status</h1>

<div class="grid gap-4 sm:grid-cols-2">
    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-medium text-gray-500 mb-1">Laravel</h2>
        <p class="text-lg font-semibold">{{ $laravelVersion }}</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-medium text-gray-500 mb-1">PHP</h2>
        <p class="text-lg font-semibold">{{ $phpVersion }}</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-medium text-gray-500 mb-1">Database</h2>
        @if ($dbConnected)
            <p class="text-lg font-semibold text-green-700">Connected</p>
            <p class="text-sm text-gray-500 mt-1">{{ $dbVersion }}</p>
        @else
            <p class="text-lg font-semibold text-red-700">Not connected</p>
            <p class="text-sm text-gray-500 mt-1">{{ $dbError }}</p>
        @endif
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="text-sm font-medium text-gray-500 mb-1">Tables</h2>
        @if ($dbConnected)
            <p class="text-lg font-semibold">{{ $tableCount }}</p>
        @else
            <p class="text-lg font-semibold text-gray-400">&mdash;</p>
        @endif
    </div>
</div>
@endsection
