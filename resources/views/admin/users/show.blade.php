@extends('layouts.admin')

@section('title', 'Profil')

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $user->name }} Profil Ma‘lumotlari</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Ism:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Telefon:</strong> {{ $user->phone ?? 'N/A' }}</p>
                    <p><strong>Manzil:</strong> {{ $user->address ?? 'N/A' }}</p>
                    <p><strong>Qo‘shilgan sana:</strong> {{ $user->created_at->format('d.m.Y') }}</p>
                    @if ($user->is_admin)
                        <p><strong>Status:</strong> Admin</p>
                    @else
                        <p><strong>Status:</strong> Oddiy foydalanuvchi</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.profile.edit', $user) }}" class="btn btn-primary">Tahrirlash</a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Orqaga</a>
            <!-- Parol generatsiyalash uchun forma -->
            <form action="{{ route('admin.profile.generatePassword', $user) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary" onclick="return confirm('Yangi parol generatsiyalansinmi?')">Parol Generatsiyalash</button>
            </form>
        </div>
    </div>
@endsection