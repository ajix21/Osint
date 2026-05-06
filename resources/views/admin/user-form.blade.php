@extends('layouts.app')
@section('title', ($user ? 'Edit' : 'Tambah') . ' User — Admin')

@section('content')
<div class="page-title">{{ $user ? 'Edit User: ' . $user->username : 'Tambah User Baru' }}</div>

<div class="panel" style="max-width:560px;">
  <div class="section-label">Detail Akun</div>

  <form method="POST" action="{{ $user ? route('admin.users.update', $user->id) : route('admin.users.store') }}">
    @csrf
    @if($user) @method('PUT') @endif

    <div class="grid-2" style="margin-bottom:16px;">
      <div class="field">
        <label>Nama Lengkap *</label>
        <input type="text" name="name" value="{{ old('name', $user?->name) }}" placeholder="Nama Lengkap" required>
        @error('name') <span class="field-error">{{ $message }}</span> @enderror
      </div>
      <div class="field">
        <label>Username *</label>
        <input type="text" name="username" value="{{ old('username', $user?->username) }}" placeholder="username" required>
        @error('username') <span class="field-error">{{ $message }}</span> @enderror
      </div>
    </div>

    <div class="field" style="margin-bottom:16px;">
      <label>Email *</label>
      <input type="email" name="email" value="{{ old('email', $user?->email) }}" placeholder="email@domain.com" required>
      @error('email') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="grid-2" style="margin-bottom:16px;">
      <div class="field">
        <label>Password {{ $user ? '(kosongkan jika tidak diganti)' : '*' }}</label>
        <input type="password" name="password"
               placeholder="Min. 8 karakter, besar, angka, simbol"
               autocomplete="new-password"
               {{ $user ? '' : 'required' }}>
        @error('password') <span class="field-error">{{ $message }}</span> @enderror
      </div>
      <div class="field">
        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation"
               placeholder="Ulangi password"
               autocomplete="new-password"
               {{ $user ? '' : 'required' }}>
      </div>
    </div>

    <div class="grid-2" style="margin-bottom:16px;">
      <div class="field">
        <label>Role *</label>
        <select name="role" required>
          <option value="viewer"   {{ old('role', $user?->role) === 'viewer'   ? 'selected' : '' }}>Viewer</option>
          <option value="operator" {{ old('role', $user?->role) === 'operator' ? 'selected' : '' }}>Operator</option>
          <option value="admin"    {{ old('role', $user?->role) === 'admin'    ? 'selected' : '' }}>Admin</option>
        </select>
        @error('role') <span class="field-error">{{ $message }}</span> @enderror
      </div>
      <div class="field">
        <label>Status Akun</label>
        <select name="is_active">
          <option value="1" {{ old('is_active', $user?->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
          <option value="0" {{ !old('is_active', $user?->is_active ?? true) ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>
    </div>

    <div class="field" style="margin-bottom:20px;">
      <label>API Token LeakOSINT (opsional — override token global)</label>
      <input type="password"
             name="api_token"
             placeholder="{{ $user?->api_token ? '••••••••  (token tersimpan, kosongkan untuk tidak mengubah)' : 'token:key' }}"
             autocomplete="off">
      @error('api_token') <span class="field-error">{{ $message }}</span> @enderror
    </div>

    <div class="btn-row">
      <button type="submit" class="btn btn-primary">{{ $user ? 'Simpan Perubahan' : 'Buat User' }}</button>
      <a href="{{ route('admin.users') }}" class="btn btn-ghost">Batal</a>
    </div>
  </form>
</div>
@endsection
