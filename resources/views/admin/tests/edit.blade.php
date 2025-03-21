@extends('layouts.admin')

@section('content')
    <h1>Testni Tahrirlash</h1>
    <form action="{{ route('admin.tests.update', $test) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Nomi</label>
            <input type="text" name="title" class="form-control" value="{{ $test->title }}" required>
        </div>
        <div class="form-group">
            <label for="description">Tavsif</label>
            <textarea name="description" class="form-control">{{ $test->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="category_id">Kategoriya</label>
            <select name="category_id" class="form-control">
                <option value="">Kategoriyasiz</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $test->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="question_count">Savollar Soni</label>
            <input type="number" name="question_count" class="form-control" min="1" max="20" value="{{ $test->question_count }}" required>
        </div>
        <div class="form-group">
            <label for="time_limit">Vaqt Chegarasi (daqiqa)</label>
            <input type="number" name="time_limit" class="form-control" min="1" value="{{ $test->time_limit }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Yangilash</button>
    </form>
@endsection