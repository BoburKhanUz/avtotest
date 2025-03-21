@extends('layouts.admin')

@section('content')
    <h1>Promokodni Tahrirlash</h1>
    <form action="{{ route('admin.promocodes.update', $promocode) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="code">Promokod</label>
            <input type="text" name="code" class="form-control" value="{{ $promocode->code }}" required>
        </div>
        <div class="form-group">
            <label for="discount_percentage">Chegirma Foizi (%)</label>
            <input type="number" name="discount_percentage" class="form-control" min="1" max="100" value="{{ $promocode->discount_percentage }}" required>
        </div>
        <div class="form-group">
            <label for="expires_at">Amal qilish muddati</label>
            <input type="datetime-local" name="expires_at" class="form-control" value="{{ $promocode->expires_at ? $promocode->expires_at->format('Y-m-d\TH:i') : '' }}">
        </div>
        <div class="form-group">
            <label for="usage_limit">Foydalanish Chegarasi</label>
            <input type="number" name="usage_limit" class="form-control" min="1" value="{{ $promocode->usage_limit }}">
        </div>
        <div class="form-group">
            <label for="is_active">Faol</label>
            <input type="checkbox" name="is_active" value="1" {{ $promocode->is_active ? 'checked' : '' }}>
        </div>
        <button type="submit" class="btn btn-primary">Yangilash</button>
    </form>
@endsection