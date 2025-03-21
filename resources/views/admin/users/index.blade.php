@extends('layouts.admin')

@section('title', 'Foydalanuvchilar')

@section('content')
<a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Yangi Foydalanuvchi Qo‘shish</a>

<div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Foydalanuvchilar Ro‘yxati</h3>
        </div>



        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ism</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Manzil</th>
                        <th>Harakatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>{{ $user->address ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('admin.profile.show', $user) }}" class="btn btn-info btn-sm">Ko‘rish</a>
                                <a href="{{ route('admin.profile.edit', $user) }}" class="btn btn-warning btn-sm">Tahrirlash</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('O‘chirishni tasdiqlaysizmi?')">O‘chirish</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection