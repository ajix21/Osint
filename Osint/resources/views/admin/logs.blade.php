@extends('layouts.app')
@section('title', 'Audit Logs — Admin')

@section('content')
<div class="page-title">Audit Logs</div>

<div style="margin-bottom:24px;">
  <div class="section-label" style="margin-bottom:12px;">Login Attempts</div>
  <div style="background:var(--bg2);border:1px solid var(--border);">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Waktu</th>
            <th>Username</th>
            <th>IP Address</th>
            <th>Status</th>
            <th>User Agent</th>
          </tr>
        </thead>
        <tbody>
          @forelse($loginLogs as $log)
          <tr>
            <td class="text-muted" style="white-space:nowrap;font-size:11px;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
            <td class="text-amber">{{ $log->username }}</td>
            <td class="text-muted" style="font-size:11px;">{{ $log->ip_address }}</td>
            <td>
              @if($log->success)
                <span class="badge badge-teal">Berhasil</span>
              @else
                <span class="badge badge-red">Gagal</span>
              @endif
            </td>
            <td class="text-muted" style="font-size:10px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $log->user_agent }}</td>
          </tr>
          @empty
          <tr><td colspan="5" style="padding:32px;text-align:center;color:var(--text-muted);">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($loginLogs->hasPages())
    <div style="padding:12px 20px;border-top:1px solid var(--border);">{{ $loginLogs->links() }}</div>
    @endif
  </div>
</div>

<div>
  <div class="section-label" style="margin-bottom:12px;">Search Logs</div>
  <div style="background:var(--bg2);border:1px solid var(--border);">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Waktu</th>
            <th>User</th>
            <th>Query</th>
            <th>Results</th>
            <th>Sources</th>
            <th>IP</th>
          </tr>
        </thead>
        <tbody>
          @forelse($searchLogs as $log)
          <tr>
            <td class="text-muted" style="white-space:nowrap;font-size:11px;">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
            <td><span class="badge badge-amber">{{ $log->user->username ?? '-' }}</span></td>
            <td style="max-width:250px;word-break:break-all;font-size:11px;">{{ Str::limit($log->query, 80) }}</td>
            <td class="text-teal">{{ number_format($log->num_results) }}</td>
            <td class="text-muted">{{ $log->num_sources }}</td>
            <td class="text-muted" style="font-size:10px;">{{ $log->ip_address }}</td>
          </tr>
          @empty
          <tr><td colspan="6" style="padding:32px;text-align:center;color:var(--text-muted);">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($searchLogs->hasPages())
    <div style="padding:12px 20px;border-top:1px solid var(--border);">{{ $searchLogs->links() }}</div>
    @endif
  </div>
</div>
@endsection
