@extends('layouts.admin')

@section('content')
    <h1>{{ $test->title }} uchun Savollar</h1>
    <div class="mb-3">
        <a href="{{ route('admin.questions.create', $test) }}" class="btn btn-primary">Yangi Savol Qo‘shish</a>
        <form action="{{ route('admin.questions.import', $test) }}" method="POST" enctype="multipart/form-data" class="d-inline">
            @csrf
            <input type="file" name="csv_file" accept=".csv" required class="d-inline">
            <button type="submit" class="btn btn-success">Import CSV</button>
        </form>
        <a href="{{ route('admin.questions.export', $test) }}" class="btn btn-info">Export CSV</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Savol (UZ)</th>
                <th>Savol (RU)</th>
                <th>Harakatlar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($questions as $question)
                <tr>
                    <td>{{ $question->id }}</td>
                    <td>{{ $question->content_uz }}</td>
                    <td>{{ $question->content_ru }}</td>
                    <td>
                        <a href="{{ route('admin.questions.edit', [$test, $question]) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                        <form action="{{ route('admin.questions.destroy', [$test, $question]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('O‘chirishni tasdiqlaysizmi?')">O‘chirish</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection