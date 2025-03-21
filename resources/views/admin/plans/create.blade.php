@extends('layouts.admin')

@section('content')
    <h1>Yangi Obuna Rejasi Qo‘shish</h1>
    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nomi</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug (Unikal identifikator)</label>
            <input type="text" name="slug" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="price">Narx ($)</label>
            <input type="number" name="price" class="form-control" step="0.01" min="0" required>
        </div>
        <div class="form-group">
            <label for="duration_days">Davomiylik (kun)</label>
            <input type="number" name="duration_days" class="form-control" min="1" required>
        </div>
        <div class="form-group">
            <label for="description">Tavsif</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="is_active">Faol</label>
            <input type="checkbox" name="is_active" value="1" checked>
        </div>
        <button type="submit" class="btn btn-primary">Saqlash</button>
    </form>
@endsection