@extends('layouts.admin')

@section('content')
    <h1>Obuna Rejalari</h1>
    <div class="mb-3">
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">Yangi Reja Qo‘shish</a>
    </div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nomi</th>
                <th>Narx</th>
                <th>Davomiylik (kun)</th>
                <th>Faol</th>
                <th>Harakatlar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($plans as $plan)
                <tr>
                    <td>{{ $plan->id }}</td>
                    <td>{{ $plan->name }}</td>
                    <td>${{ number_format($plan->price, 2) }}</td>
                    <td>{{ $plan->duration_days }}</td>
                    <td>{{ $plan->is_active ? 'Ha' : 'Yo‘q' }}</td>
                    <td>
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-warning">Tahrirlash</a>
                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" style="display:inline;">
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