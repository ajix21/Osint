@extends('layouts.app')
@section('title', 'Manajemen User — Admin')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
  <div class="page-title" style="margin-bottom:0;">Manajemen User</div>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
    <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><line x1="8" y1="2" x2="8" y2="14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="2" y1="8" x2="14" y2="8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    Tambah User
  </a>
</div>

<div style="background:var(--bg2);border:1px solid var(--border);">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nama</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Searches</th>
          <th>Last Login</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $i => $user)
        <tr>
          <td class="text-muted">{{ $i + 1 }}</td>
          <td>{{ $user->name }}</td>
          <td class="text-amber">{{ $user->username }}</td>
          <td class="text-muted" style="font-size:11px;">{{ $user->email }}</td>
          <td><span class="role-chip role-{{ $user->role }}">{{ $user->role }}</span></td>
          <td>
            @if($user->is_active)
              <span class="badge badge-teal">Aktif</span>
            @else
              <span class="badge badge-red">Nonaktif</span>
            @endif
          </td>
          <td class="text-muted">{{ $user->search_logs_count }}</td>
          <td class="text-muted" style="font-size:11px;white-space:nowrap;">
            {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Belum pernah' }}
          </td>
          <td>
            <div style="display:flex;gap:6px;">
              <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-ghost btn-sm">Edit</a>
              @if($user->id !== auth()->id())
              <form method="POST"
                    action="{{ route('admin.users.destroy', $user->id) }}"
                    class="delete-user-form"
                    data-username="{{ $user->username }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-user-form').forEach(function(form) {
  form.addEventListener('submit', function(e) {
    if (!confirm('Hapus user ' + form.dataset.username + '?')) {
      e.preventDefault();
    }
  });
});
</script>
@endpush
