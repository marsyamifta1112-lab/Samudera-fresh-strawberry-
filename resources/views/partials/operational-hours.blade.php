<aside class="operational-hours" aria-label="Jam operasional toko">
    <div class="operational-hours-heading">
        <div>
            <h2>Jam Operasional</h2>
            <p>Jadwal toko Samudera Fresh Strawberry</p>
        </div>
        <span class="operational-status {{ $operationalSchedule['is_open_now'] ? 'is-open' : 'is-closed' }}">
            {{ $operationalSchedule['status_hari_ini'] }}
        </span>
    </div>
    <div class="operational-hours-details">
        <div>
            <span>Hari operasional</span>
            <strong>{{ $operationalSchedule['hari_label'] }}</strong>
            @if ($operationalSchedule['hari_tutup_label'])
                <small class="operational-closed-days">Tutup: {{ $operationalSchedule['hari_tutup_label'] }}</small>
            @endif
        </div>
        <div>
            <span>Jam buka - tutup</span>
            <strong>{{ str_replace(':', '.', $operationalSchedule['jam_buka']) }} - {{ str_replace(':', '.', $operationalSchedule['jam_tutup']) }} WIB</strong>
        </div>
    </div>
</aside>
<style>
    .operational-hours { margin:18px 0 22px; padding:18px 20px; border:1px solid #dfece2; border-radius:14px; background:linear-gradient(115deg,#f0f7f1,#fff); }
    .operational-hours-heading { display:flex; align-items:center; justify-content:space-between; gap:14px; }
    .operational-hours h2 { margin:0; color:#245b3b; font-size:16px; }
    .operational-hours p { margin:5px 0 0; color:#777; font-size:11px; }
    .operational-status { flex:none; padding:6px 10px; border-radius:999px; font-size:10px; font-weight:700; }
    .operational-status.is-open { background:#e2f1e5; color:#245b3b; }
    .operational-status.is-closed { background:#fff0f2; color:#b42342; }
    .operational-hours-details { display:flex; flex-wrap:wrap; gap:18px 36px; margin-top:15px; }
    .operational-hours-details div { display:grid; gap:4px; }
    .operational-hours-details span { color:#777; font-size:10px; }
    .operational-hours-details strong { color:#292929; font-size:12px; }
    .operational-closed-days { color:#777; font-size:10px; }
    @media(max-width:620px) {
        .operational-hours { padding:15px; }
        .operational-hours-heading { align-items:flex-start; flex-direction:column; }
        .operational-hours-details { gap:12px 20px; }
    }
</style>
