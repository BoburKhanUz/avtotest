@extends('layouts.admin')

@section('title', 'Foydalanuvchilar')

@section('content')
<div class="row mb-3">
    <div class="col-md-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Ism, email yoki telefon..." value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Yangi foydalanuvchi
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Rol</label>
                <select name="role" class="form-select">
                    <option value="">Barchasi</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Foydalanuvchi</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Til</label>
                <select name="language" class="form-select">
                    <option value="">Barchasi</option>
                    <option value="uz" {{ request('language') == 'uz' ? 'selected' : '' }}>O'zbek</option>
                    <option value="ru" {{ request('language') == 'ru' ? 'selected' : '' }}>Rus</option>
                    <option value="en" {{ request('language') == 'en' ? 'selected' : '' }}>Ingliz</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Obuna</label>
                <select name="subscription" class="form-select">
                    <option value="">Barchasi</option>
                    <option value="active" {{ request('subscription') == 'active' ? 'selected' : '' }}>Faol obuna</option>
                    <option value="inactive" {{ request('subscription') == 'inactive' ? 'selected' : '' }}>Obunamiz</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-people"></i> Foydalanuvchilar ro'yxati
            <span class="badge bg-primary">{{ $users->total() }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Ism</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Rol</th>
                        <th>Til</th>
                        <th>Statistika</th>
                        <th>Ro'yxatdan o'tgan</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle text-primary me-2" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->is_admin)
                                        <span class="badge bg-danger ms-1">Admin</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            @if($user->is_admin)
                                <span class="badge bg-danger">Administrator</span>
                            @else
                                <span class="badge bg-secondary">Foydalanuvchi</span>
                            @endif
                        </td>
                        <td>
                            @switch($user->language)
                                @case('uz')
                                    <span class="badge bg-info">O'zbek</span>
                                    @break
                                @case('ru')
                                    <span class="badge bg-warning">Rus</span>
                                    @break
                                @case('en')
                                    <span class="badge bg-primary">Ingliz</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <small>
                                <i class="bi bi-clipboard-check"></i> {{ $user->test_results_count }} testlar<br>
                                <i class="bi bi-star"></i> {{ $user->subscriptions_count }} obunalar
                            </small>
                        </td>
                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info" title="Ko'rish">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning" title="Tahrirlash">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirmDelete('Foydalanuvchini o\'chirmoqchimisiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="O'chirish">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">Foydalanuvchilar topilmadi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
