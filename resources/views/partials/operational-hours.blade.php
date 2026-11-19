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
    .operational-hours {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.05fr);
        align-items: center;
        gap: 24px;
        margin: 20px 0 24px;
        padding: 20px 22px;
        border: 1px solid #dcebe0;
        border-radius: 20px;
        background: linear-gradient(115deg, #eff7f0, #fff 78%);
        box-shadow: 0 9px 24px rgba(36, 91, 59, .045);
    }

    .operational-hours-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .operational-hours h2 { margin: 0; color: #245b3b; font-size: 18px; }
    .operational-hours p { margin: 6px 0 0; color: #777; font-size: 12px; }
    .operational-status { flex: none; padding: 7px 11px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .operational-status.is-open { background: #dff1e3; color: #245b3b; }
    .operational-status.is-closed { background: #fff0f2; color: #b42342; }
    .operational-hours-details { display: grid; grid-template-columns: minmax(0, 1.2fr) minmax(0, .8fr); gap: 10px; }
    .operational-hours-details div { display: grid; align-content: center; gap: 6px; min-height: 67px; padding: 12px 14px; border: 1px solid #e8eee8; border-radius: 13px; background: rgba(255, 255, 255, .82); }
    .operational-hours-details span { color: #7a807a; font-size: 11px; }
    .operational-hours-details strong { color: #292929; font-size: 13px; line-height: 1.45; }
    .operational-closed-days { color: #777; font-size: 11px; }

    @media(max-width: 760px) {
        .operational-hours { grid-template-columns: 1fr; gap: 15px; }
    }

    @media(max-width: 480px) {
        .operational-hours { padding: 17px; }
        .operational-hours-heading { align-items: flex-start; flex-direction: column; }
        .operational-hours-details { grid-template-columns: 1fr; }
        .operational-hours-details div { min-height: auto; }
    }
</style>
