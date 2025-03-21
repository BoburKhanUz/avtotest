@extends('layouts.admin')

@section('content')
    <h1>{{ $test->title }} uchun Savolni Tahrirlash</h1>
    <form action="{{ route('admin.questions.update', [$test, $question]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="content_uz">Savol (UZ)</label>
            <textarea name="content_uz" class="form-control" required>{{ $question->content_uz }}</textarea>
        </div>
        <div class="form-group">
            <label for="content_ru">Savol (RU)</label>
            <textarea name="content_ru" class="form-control" required>{{ $question->content_ru }}</textarea>
        </div>
        <div class="form-group">
            <label for="options_uz">Variantlar (UZ, JSON formatida)</label>
            <textarea name="options_uz" class="form-control" required>{{ json_encode($question->options_uz) }}</textarea>
        </div>
        <div class="form-group">
            <label for="options_ru">Variantlar (RU, JSON formatida)</label>
            <textarea name="options_ru" class="form-control" required>{{ json_encode($question->options_ru) }}</textarea>
        </div>
        <div class="form-group">
            <label for="correct_option">To‘g‘ri Javob</label>
            <input type="text" name="correct_option" class="form-control" value="{{ $question->correct_option }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Yangilash</button>
    </form>
@endsection