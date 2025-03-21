@extends('layouts.admin')

@section('title', 'API Hujjatlari')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">API Hujjatlari</h3>
        </div>
        <div class="card-body p-0">
            <iframe src="{{ url('/api/documentation') }}" style="width: 100%; height: 800px; border: none;"></iframe>
        </div>
    </div>
@endsection