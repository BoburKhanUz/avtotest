@extends('layouts.admin')

@section('title', 'Yangi Test Qo‘shish')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Yangi Test Qo‘shish</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.tests.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name_uz">Nomi (O‘zbekcha)</label>
                    <input type="text" name="name_uz" class="form-control @error('name_uz') is-invalid @enderror" value="{{ old('name_uz') }}" required>
                    @error('name_uz')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="name_ru">Nomi (Ruscha)</label>
                    <input type="text" name="name_ru" class="form-control @error('name_ru') is-invalid @enderror" value="{{ old('name_ru') }}" required>
                    @error('name_ru')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="category_id">Kategoriya</label>
                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">Kategoriyani tanlang</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Saqlash</button>
                <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary">Orqaga</a>
            </form>
        </div>
    </div>
@endsection