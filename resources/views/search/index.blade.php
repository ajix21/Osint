@extends('layouts.app')
@section('title', 'Search — LeakOSINT')

@section('content')

<div class="panel">
  <div class="section-label">Parameter Pencarian</div>

  @if(!auth()->user()->canSearch())
    <div class="alert alert-error" style="margin-bottom:0;">
      Role <strong>viewer</strong> tidak dapat melakukan pencarian. Hubungi administrator untuk upgrade akses.
    </div>
  @else

  <div class="field" style="margin-bottom:16px;">
    <label>Query / Request <span class="text-muted" style="font-size:10px;">(pisahkan dengan baris baru untuk multi-query)</span></label>
    <textarea id="query" placeholder="Contoh:&#10;example@gmail.com&#10;Elon Reeve Musk&#10;&quot;exact phrase search&quot;"></textarea>
  </div>
  <div class="grid-4">
    <div class="field">
      <label>Limit</label>
      <select id="limit">
        <option value="10">10</option>
        <option value="50">50</option>
        <option value="100" selected>100</option>
        <option value="250">250</option>
        <option value="500">500</option>
        <option value="1000">1000</option>
        <option value="5000">5000</option>
        <option value="10000">10000</option>
      </select>
    </div>
    <div class="field">
      <label>Bahasa</label>
      <select id="lang">
        <option value="en">English</option>
        <option value="ru">Russian</option>
        <option value="de">German</option>
        <option value="fr">French</option>
        <option value="es">Spanish</option>
        <option value="it">Italian</option>
        <option value="pt">Portuguese</option>
        <option value="zh">Chinese</option>
        <option value="ar">Arabic</option>
      </select>
    </div>
    <div class="field">
      <label>Bot Name (opsional)</label>
      <input type="text" id="bot-name" placeholder="@botname">
    </div>
    <div class="field">
      <label>Page Size</label>
      <select id="page-size">
        <option value="25">25</option>
        <option value="50" selected>50</option>
        <option value="100">100</option>
      </select>
    </div>
  </div>
  <div class="btn-row">
    <button class="btn btn-primary" id="btn-search" onclick="doSearch()">
      <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><circle cx="7" cy="7" r="5" stroke="currentColor" stroke-width="1.8"/><line x1="11" y1="11" x2="15" y2="15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      Cari Sekarang
    </button>
    <button class="btn btn-ghost" onclick="clearAll()">Reset</button>
  </div>
  @endif
</div>

<div class="status-bar" id="status-bar">
  <div class="dot" id="status-dot"></div>
  <span id="status-text">Siap. Masukkan query untuk memulai pencarian.</span>
</div>

<div id="results-panel" style="background:var(--bg2);border:1px solid var(--border);display:none;">
  <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
    <div style="font-family:var(--display);font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#fff;">Hasil Pencarian</div>
    <div class="badge badge-amber" id="badge-total">0 records</div>
    <div class="badge badge-teal" id="badge-sources">0 sources</div>
    <div style="margin-left:auto;display:flex;gap:8px;">
      <button class="btn btn-teal" id="btn-excel" onclick="exportExcel()" disabled>
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="12" height="12" rx="1" stroke="currentColor" stroke-width="1.5"/><line x1="5" y1="6" x2="11" y2="6" stroke="currentColor" stroke-width="1.2"/><line x1="5" y1="9" x2="11" y2="9" stroke="currentColor" stroke-width="1.2"/><line x1="5" y1="12" x2="8" y2="12" stroke="currentColor" stroke-width="1.2"/></svg>
        Export Excel
      </button>
      <button class="btn btn-teal" id="btn-pdf" onclick="exportPDF()" disabled>
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M3 2h7l3 3v9H3V2z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><line x1="6" y1="8" x2="10" y2="8" stroke="currentColor" stroke-width="1.2"/><line x1="6" y1="11" x2="10" y2="11" stroke="currentColor" stroke-width="1.2"/></svg>
        Export PDF
      </button>
    </div>
  </div>

  <div style="display:flex;gap:20px;flex-wrap:wrap;padding:14px 20px;background:var(--bg3);border-bottom:1px solid var(--border);font-size:11px;color:var(--text-muted);" id="info-row"></div>

  <div id="table-container"></div>

  <div style="padding:10px 20px;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px;">
    <button onclick="toggleRaw()" style="font-family:var(--mono);font-size:10px;letter-spacing:.1em;text-transform:uppercase;background:none;border:1px solid var(--border2);color:var(--text-muted);padding:4px 12px;cursor:pointer;">{ } Raw JSON</button>
    <span style="font-size:10px;color:var(--text-muted);">Lihat response mentah dari API</span>
  </div>
  <div id="raw-block" style="display:none;padding:16px 20px;background:var(--bg);border-top:1px solid var(--border);overflow-x:auto;">
    <pre id="raw-content" style="font-family:var(--mono);font-size:11px;color:#7a9ccc;white-space:pre-wrap;word-break:break-all;line-height:1.7;"></pre>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let allData = [], allColumns = [], rawResponse = null, dbMeta = {};

function setStatus(type, msg) {
  document.getElementById('status-dot').className = 'dot ' + type;
  document.getElementById('status-text').textContent = msg;
}

function clearAll() {
  document.getElementById('query').value = '';
  allData = []; allColumns = []; rawResponse = null; dbMeta = {};
  document.getElementById('results-panel').style.display = 'none';
  document.getElementById('btn-excel').disabled = true;
  document.getElementById('btn-pdf').disabled = true;
  setStatus('', 'Siap. Masukkan query untuk memulai pencarian.');
}

async function doSearch() {
  const query   = document.getElementById('query').value.trim();
  const limit   = parseInt(document.getElementById('limit').value);
  const lang    = document.getElementById('lang').value;
  const botName = document.getElementById('bot-name').value.trim();

  if (!query) { setStatus('error', 'Error: Query pencarian wajib diisi.'); return; }

  const btn = document.getElementById('btn-search');
  btn.disabled = true;
  setStatus('loading', 'Mengirim permintaan ke server...');
  document.getElementById('results-panel').style.display = 'block';
  document.getElementById('table-container').innerHTML = `
    <div class="loading-state">
      <div class="spinner"></div>
      <div style="font-size:12px;letter-spacing:.08em;">Memproses pencarian melalui LeakOSINT API...</div>
    </div>`;

  const payload = { request: query, limit, lang };
  if (botName) payload.bot_name = botName;

  try {
    const resp = await fetch('{{ route("search.query") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload)
    });

    const data = await resp.json();

    if (!resp.ok) {
      throw new Error(data.error || `HTTP ${resp.status}`);
    }

    rawResponse = data;
    document.getElementById('raw-content').textContent = JSON.stringify(data, null, 2);
    processResults(data);
    setStatus('ready', `Selesai — ${allData.length} record dari ${Object.keys(dbMeta).length} sumber.`);
  } catch (err) {
    document.getElementById('table-container').innerHTML = `
      <div class="empty-state">
        <div class="big">!</div>
        <p>Terjadi kesalahan saat menghubungi API.<br>${escHtml(err.message)}</p>
      </div>`;
    setStatus('error', 'Error: ' + err.message);
  } finally {
    btn.disabled = false;
  }
}

function processResults(data) {
  allData = []; dbMeta = {};

  if (data.List && typeof data.List === 'object' && !Array.isArray(data.List)) {
    for (const [dbName, dbContent] of Object.entries(data.List)) {
      if (dbContent.InfoLeak) dbMeta[dbName] = dbContent.InfoLeak;
      if (dbContent.Data && Array.isArray(dbContent.Data)) {
        dbContent.Data.forEach(row => {
          allData.push({ _source: dbName, ...flattenObj(row) });
        });
      }
    }
  }

  if (allData.length === 0) {
    const summary = {};
    if (data.NumOfResults !== undefined) summary['Jumlah Hasil'] = data.NumOfResults;
    if (data.message) summary['Pesan'] = data.message;
    if (data.error) summary['Error'] = data.error;
    if (Object.keys(summary).length > 0) allData.push(summary);
  }

  const colSet = new Set(['_source']);
  for (const row of allData) for (const k of Object.keys(row)) if (k !== '_source') colSet.add(k);
  allData.forEach(r => { if (!r._source) r._source = ''; });
  allColumns = [...colSet].filter(c => allData.some(r => r[c] !== undefined && r[c] !== ''));

  document.getElementById('badge-total').textContent   = (data.NumOfResults || allData.length).toLocaleString() + ' records';
  document.getElementById('badge-sources').textContent = Object.keys(dbMeta).length + ' sources';

  const infoItems = [];
  if (data.price !== undefined)            infoItems.push(`<div style="display:flex;gap:6px;"><span>Harga Search:</span><span style="color:var(--text);">${data.price} credit</span></div>`);
  if (data.free_requests_left !== undefined) infoItems.push(`<div style="display:flex;gap:6px;"><span>Sisa Kuota:</span><span style="color:var(--teal);">${data.free_requests_left}</span></div>`);
  if (data["search time"])                 infoItems.push(`<div style="display:flex;gap:6px;"><span>Waktu:</span><span style="color:var(--text);">${data["search time"]} dtk</span></div>`);
  document.getElementById('info-row').innerHTML = infoItems.join('');

  renderTableCards();
  document.getElementById('btn-excel').disabled = false;
  document.getElementById('btn-pdf').disabled   = false;
}

function flattenObj(obj, prefix) {
  prefix = prefix || '';
  const result = {};
  for (const [k, v] of Object.entries(obj)) {
    const key = prefix ? prefix + '.' + k : k;
    if (v !== null && typeof v === 'object' && !Array.isArray(v)) Object.assign(result, flattenObj(v, key));
    else if (Array.isArray(v)) result[key] = v.join(', ');
    else result[key] = v;
  }
  return result;
}

function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function renderTableCards() {
  const container = document.getElementById('table-container');
  if (allData.length === 0) {
    container.innerHTML = `<div class="empty-state"><div class="big">—</div><p>Tidak ada data ditemukan.</p></div>`;
    return;
  }
  const grouped = {};
  allData.forEach(row => { if (!grouped[row._source]) grouped[row._source] = []; grouped[row._source].push(row); });

  let html = '';
  for (const [source, rows] of Object.entries(grouped)) {
    let cols = new Set();
    rows.forEach(r => Object.keys(r).forEach(k => { if (k !== '_source') cols.add(k); }));
    cols = [...cols];

    html += `<div style="margin-bottom:24px;border:1px solid var(--border);background:var(--bg2);border-radius:4px;overflow:hidden;">`;
    html += `<div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;background:var(--bg3);">
               <div style="font-family:var(--display);font-weight:700;color:var(--amber);font-size:14px;text-transform:uppercase;">${escHtml(source)}</div>
               <div class="badge badge-teal">${rows.length} records</div>
             </div>`;
    if (dbMeta[source]) {
      html += `<div style="padding:12px 20px;border-bottom:1px solid var(--border);font-size:11px;color:var(--text-muted);line-height:1.6;background:var(--bg);border-left:3px solid var(--amber-dim);">${escHtml(dbMeta[source])}</div>`;
    }
    html += `<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:12px;table-layout:auto;">
               <thead><tr style="background:var(--bg3);border-bottom:1px solid var(--border2);">
                 <th style="padding:10px 14px;text-align:center;font-size:10px;color:var(--text-muted);text-transform:uppercase;width:40px;">#</th>`;
    cols.forEach(col => {
      html += `<th style="padding:10px 14px;text-align:left;font-size:10px;color:var(--text-muted);text-transform:uppercase;">${escHtml(col)}</th>`;
    });
    html += `</tr></thead><tbody>`;
    rows.forEach((row, i) => {
      html += `<tr style="border-bottom:1px solid var(--border);" onmouseover="this.style.backgroundColor='var(--bg3)'" onmouseout="this.style.backgroundColor='transparent'">
                 <td style="padding:9px 14px;color:var(--text-muted);text-align:center;vertical-align:top;">${i+1}</td>`;
      cols.forEach(col => {
        const val = row[col] !== undefined ? String(row[col]) : '-';
        let style = 'color:var(--text);';
        if (/email|phone|password|hash/i.test(col)) style = 'color:var(--teal);font-weight:bold;';
        html += `<td style="padding:9px 14px;white-space:normal;word-break:break-word;min-width:150px;vertical-align:top;${style}">${escHtml(val)}</td>`;
      });
      html += `</tr>`;
    });
    html += `</tbody></table></div></div>`;
  }
  container.innerHTML = html;
}

function toggleRaw() {
  const b = document.getElementById('raw-block');
  b.style.display = b.style.display === 'none' ? 'block' : 'none';
}

// ─── EXPORT EXCEL ───────────────────────────────
function exportExcel() {
  if (!allData.length) return;
  const headers = allColumns.map(c => c === '_source' ? 'DATABASE SOURCE' : c);
  const rows    = allData.map(row => allColumns.map(col => row[col] !== undefined ? row[col] : ''));
  const wb = XLSX.utils.book_new();
  const ws = XLSX.utils.aoa_to_sheet([headers, ...rows]);
  ws['!cols'] = allColumns.map(() => ({ wch: 22 }));
  XLSX.utils.book_append_sheet(wb, ws, 'LeakOSINT Results');
  const meta = [
    ['LeakOSINT Search Export'],
    ['Tanggal', new Date().toLocaleString('id-ID')],
    ['Query', document.getElementById('query').value.trim()],
    ['Total Record', allData.length],
    ['Sumber Database', Object.keys(dbMeta).length],
  ];
  XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(meta), 'Info');
  XLSX.writeFile(wb, 'leakosint_' + new Date().toISOString().slice(0,10) + '.xlsx');
}

// ─── EXPORT PDF ─────────────────────────────────
function exportPDF() {
  if (!allData.length) return;
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
  const query = document.getElementById('query').value.trim();
  const today = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });

  doc.setFillColor(15,19,24); doc.rect(0,0,297,22,'F');
  doc.setFont('helvetica','bold'); doc.setFontSize(14); doc.setTextColor(232,162,37);
  doc.text('LeakOSINT Search Report', 14, 13);
  doc.setFontSize(8); doc.setTextColor(100,116,128);
  doc.text(`Generated: ${today}  |  Query: ${query.replace(/\n/g,' | ')}  |  Total: ${allData.length} records`, 14, 20);

  const headers = allColumns.map(c => c === '_source' ? 'DATABASE SOURCE' : c.toUpperCase().replace(/[._]/g,' '));
  const rows    = allData.map(row => allColumns.map(col => {
    const v = row[col] !== undefined ? String(row[col]) : '';
    return v.length > 60 ? v.slice(0,57) + '...' : v;
  }));

  doc.autoTable({
    head: [headers], body: rows, startY: 26,
    styles: { font:'courier', fontSize:7, cellPadding:2.5, textColor:[180,175,170], fillColor:[10,12,16], lineColor:[30,40,64], lineWidth:0.2 },
    headStyles: { fillColor:[20,25,32], textColor:[232,162,37], fontSize:7, fontStyle:'bold', halign:'left' },
    alternateRowStyles: { fillColor:[15,19,24] },
    margin: { left:14, right:14 },
    didDrawPage: (d) => {
      doc.setFontSize(7); doc.setTextColor(80,90,110);
      doc.text(`LeakOSINT Export  |  Halaman ${d.pageNumber}`, 14, doc.internal.pageSize.height - 6);
      doc.text(`Confidential — ${today}`, 297-14, doc.internal.pageSize.height - 6, { align:'right' });
    }
  });

  doc.save('leakosint_' + new Date().toISOString().slice(0,10) + '.pdf');
}
</script>
@endpush
