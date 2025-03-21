@extends('layouts.admin')

@section('content')
    <h1>Foydalanuvchini Tahrirlash</h1>
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Ism</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label for="is_admin">Adminmi?</label>
            <select name="is_admin" class="form-control">
                <option value="0" {{ !$user->is_admin ? 'selected' : '' }}>Yo‘q</option>
                <option value="1" {{ $user->is_admin ? 'selected' : '' }}>Ha</option>
            </select>
        </div>
        <div class="form-group">
            <label for="language">Til</label>
            <select name="language" class="form-control">
                <option value="uz" {{ $user->language == 'uz' ? 'selected' : '' }}>O‘zbekcha</option>
                <option value="ru" {{ $user->language == 'ru' ? 'selected' : '' }}>Ruscha</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Yangilash</button>
    </form>
@endsection