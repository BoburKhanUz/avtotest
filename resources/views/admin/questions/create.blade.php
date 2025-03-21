@extends('layouts.admin')

@section('content')
    <h1>{{ $test->title }} uchun Yangi Savol Qo‘shish</h1>
    <form action="{{ route('admin.questions.store', $test) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="content_uz">Savol (UZ)</label>
            <textarea name="content_uz" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="content_ru">Savol (RU)</label>
            <textarea name="content_ru" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="options_uz">Variantlar (UZ, JSON formatida)</label>
            <textarea name="options_uz" class="form-control" placeholder='{"A": "Birinchi", "B": "Ikkinchi", "C": "Uchinchi"}' required></textarea>
        </div>
        <div class="form-group">
            <label for="options_ru">Variantlar (RU, JSON formatida)</label>
            <textarea name="options_ru" class="form-control" placeholder='{"A": "Первое", "B": "Второе", "C": "Третье"}' required></textarea>
        </div>
        <div class="form-group">
            <label for="correct_option">To‘g‘ri Javob</label>
            <input type="text" name="correct_option" class="form-control" placeholder="A" required>
        </div>
        <button type="submit" class="btn btn-primary">Saqlash</button>
    </form>
@endsection