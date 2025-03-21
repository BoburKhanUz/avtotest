@extends('layouts.admin')

@section('content')
    <h1>Obuna Rejasini Tahrirlash</h1>
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nomi</label>
            <input type="text" name="name" class="form-control" value="{{ $plan->name }}" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug (Unikal identifikator)</label>
            <input type="text" name="slug" class="form-control" value="{{ $plan->slug }}" required>
        </div>
        <div class="form-group">
            <label for="price">Narx ($)</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ $plan->price }}" required>
        </div>
        <div class="form-group">
            <label for="duration_days">Davomiylik (kun)</label>
            <input type="number" name="duration_days" class="form-control" min="1" value="{{ $plan->duration_days }}" required>
        </div>
        <div class="form-group">
            <label for="description">Tavsif</label>
            <textarea name="description" class="form-control">{{ $plan->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="is_active">Faol</label>
            <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}>
        </div>
        <button type="submit" class="btn btn-primary">Yangilash</button>
    </form>
@endsection