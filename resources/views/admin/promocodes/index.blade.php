@extends('layouts.admin')

@section('content')
    <h1>Promokodlar</h1>
    <a href="{{ route('admin.promocodes.create') }}" class="btn btn-primary mb-3">Yangi Promokod Qo‘shish</a>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kod</th>
                <th>Chegirma (%)</th>
                <th>Amal qilish muddati</th>
                <th>Foydalanish Chegarasi</th>
                <th>Ishlatilgan</th>
                <th>Faol</th>
                <th>Harakatlar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($promocodes as $promocode)
                <tr>
                    <td>{{ $promocode->id }}</td>
                    <td>{{ $promocode->code }}</td>
                    <td>{{ $promocode->discount_percentage }}</td>
                    <td>{{ $promocode->expires_at ?? 'Cheksiz' }}</td>
                    <td>{{ $promocode->usage_limit ?? 'Cheksiz' }}</td>
                    <td>{{ $promocode->used_count }}</td>
                    <td>{{ $promocode->is_active ? 'Ha' : 'Yo‘q' }}</td>
                    <td>
                        <a href="{{ route('admin.promocodes.edit', $promocode) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                        <form action="{{ route('admin.promocodes.destroy', $promocode) }}" method="POST" style="display:inline;">
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