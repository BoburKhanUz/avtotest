@extends('layouts.admin')

@section('content')
    <h1>Yangi Promokod Qo‘shish</h1>
    <form action="{{ route('admin.promocodes.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="code">Promokod</label>
            <input type="text" name="code" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="discount_percentage">Chegirma Foizi (%)</label>
            <input type="number" name="discount_percentage" class="form-control" min="1" max="100" required>
        </div>
        <div class="form-group">
            <label for="expires_at">Amal qilish muddati</label>
            <input type="datetime-local" name="expires_at" class="form-control">
        </div>
        <div class="form-group">
            <label for="usage_limit">Foydalanish Chegarasi</label>
            <input type="number" name="usage_limit" class="form-control" min="1">
        </div>
        <div class="form-group">
            <label for="is_active">Faol</label>
            <input type="checkbox" name="is_active" value="1" checked>
        </div>
        <button type="submit" class="btn btn-primary">Saqlash</button>
    </form>
@endsection