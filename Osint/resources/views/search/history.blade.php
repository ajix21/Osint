@extends('layouts.app')
@section('title', 'History — LeakOSINT')

@section('content')
<div class="page-title">Search History</div>

<div style="background:var(--bg2);border:1px solid var(--border);">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          @if(auth()->user()->isAdmin()) <th>User</th> @endif
          <th>Query</th>
          <th>Limit</th>
          <th>Lang</th>
          <th>Results</th>
          <th>Sources</th>
          <th>Time (s)</th>
          <th>IP</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td class="text-muted">{{ $logs->firstItem() + $loop->index }}</td>
          @if(auth()->user()->isAdmin())
          <td><span class="badge badge-amber" style="white-space:nowrap;">{{ $log->user->username ?? '-' }}</span></td>
          @endif
          <td style="max-width:300px;word-break:break-all;font-size:11px;">{{ $log->query }}</td>
          <td class="text-muted">{{ $log->limit_count }}</td>
          <td class="text-muted">{{ $log->lang }}</td>
          <td class="text-teal">{{ number_format($log->num_results) }}</td>
          <td class="text-muted">{{ $log->num_sources }}</td>
          <td class="text-muted">{{ $log->search_time ?? '-' }}</td>
          <td class="text-muted" style="font-size:10px;">{{ $log->ip_address }}</td>
          <td class="text-muted" style="white-space:nowrap;font-size:11px;">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="10" style="padding:40px;text-align:center;color:var(--text-muted);">Belum ada riwayat pencarian.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($logs->hasPages())
  <div style="padding:12px 20px;border-top:1px solid var(--border);font-size:11px;color:var(--text-muted);">
    {{ $logs->links() }}
  </div>
  @endif
</div>
@endsection
