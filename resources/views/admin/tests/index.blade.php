@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1>Testlar</h1>
        <a href="{{ route('admin.tests.create') }}" class="btn btn-primary mb-3">Yangi Test Qo‘shish</a>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table">
        <thead>
    <tr>
        <th>ID</th>
        <th>Nomi</th>
        <th>Kategoriya</th>
        <th>Savollar Soni</th>
        <th>Vaqt Chegarasi (daqiqa)</th>
        <th>Harakatlar</th>
    </tr>
</thead>
<tbody>
    @foreach ($tests as $test)
        <tr>
            <td>{{ $test->id }}</td>
            <td>{{ $test->title }}</td>
            <td>{{ $test->category ? $test->category->name : 'Kategoriyasiz' }}</td>
            <td>{{ $test->question_count }}</td>
            <td>{{ $test->time_limit }}</td>
            <td>
                <a href="{{ route('admin.tests.edit', $test) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                <a href="{{ route('admin.questions.index', $test) }}" class="btn btn-sm btn-info">Savollar</a>
                <form action="{{ route('admin.tests.destroy', $test) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('O‘chirishni tasdiqlaysizmi?')">O‘chirish</button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>
        </table>
    </div>
@endsection


