@extends('admin.layout')
@section('title', 'Jam Operasional')
@section('content')
    <div class="page-heading">
        <div>
            <h2>Jam Operasional &amp; Hari Libur</h2>
            <p>Pengaturan ini dipakai untuk menampilkan jam toko dan menghitung jadwal pengiriman pesanan baru.</p>
        </div>
    </div>

    <section class="panel operational-settings-panel">
        <form method="POST" action="{{ route('admin.operational-settings.update') }}">
            @csrf
            @method('PUT')

            <div class="settings-grid">
                <label>
                    Jam buka
                    <input type="time" name="jam_buka" value="{{ old('jam_buka', $settings['jam_buka']) }}" required>
                    @error('jam_buka')<span class="field-error">{{ $message }}</span>@enderror
                </label>
                <label>
                    Jam tutup
                    <input type="time" name="jam_tutup" value="{{ old('jam_tutup', $settings['jam_tutup']) }}" required>
                    @error('jam_tutup')<span class="field-error">{{ $message }}</span>@enderror
                </label>
            </div>

            <fieldset class="day-settings">
                <legend>Hari operasional</legend>
                <div class="day-options">
                    @foreach ($dayLabels as $number => $label)
                        <label class="day-option">
                            <input
                                type="checkbox"
                                name="hari_operasional[]"
                                value="{{ $number }}"
                                {{ in_array($number, array_map('intval', old('hari_operasional', $settings['hari_operasional'])), true) ? 'checked' : '' }}
                            >
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
                @error('hari_operasional')<span class="field-error">{{ $message }}</span>@enderror
                @error('hari_operasional.*')<span class="field-error">{{ $message }}</span>@enderror
            </fieldset>

            <fieldset class="holiday-settings">
                <legend>Tanggal libur khusus</legend>
                <p class="settings-note">Tambahkan tanggal libur satu per satu. Pesanan pada tanggal ini dijadwalkan ke hari operasional berikutnya.</p>
                <div class="holiday-list" id="holiday-list">
                    @php($holidayInputs = old('tanggal_libur', $settings['tanggal_libur']))
                    @forelse ($holidayInputs as $holiday)
                        <div class="holiday-row">
                            <input type="date" name="tanggal_libur[]" value="{{ $holiday }}" aria-label="Tanggal libur">
                            <button class="remove-holiday" type="button" aria-label="Hapus tanggal libur">Hapus</button>
                        </div>
                    @empty
                        <div class="holiday-row">
                            <input type="date" name="tanggal_libur[]" aria-label="Tanggal libur">
                            <button class="remove-holiday" type="button" aria-label="Hapus tanggal libur">Hapus</button>
                        </div>
                    @endforelse
                </div>
                @error('tanggal_libur')<span class="field-error">{{ $message }}</span>@enderror
                @error('tanggal_libur.*')<span class="field-error">{{ $message }}</span>@enderror
                <button class="button button-outline button-small add-holiday" id="add-holiday" type="button">Tambah tanggal libur</button>
            </fieldset>

            <div class="settings-footer">
                <p>Senin–Jumat sebelum pukul 12.00: hari operasional berikutnya; pukul 12.00 atau setelahnya: hari operasional kedua. Pesanan Sabtu atau hari tutup/libur: hari operasional berikutnya. Ini jadwal barang berangkat dari toko, bukan estimasi tanggal tiba.</p>
                <button class="button" type="submit">Simpan Pengaturan</button>
            </div>
        </form>
    </section>

    <style>
        .operational-settings-panel { max-width:860px; padding:24px; }
        .settings-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
        .settings-grid label { display:grid; gap:7px; color:var(--ink); font-size:11px; font-weight:700; }
        .settings-grid input,.holiday-row input { min-width:0; padding:10px 11px; border:1px solid var(--line); border-radius:7px; background:#fff; }
        .day-settings,.holiday-settings { margin:22px 0 0; padding:0; border:0; }
        .day-settings legend,.holiday-settings legend { margin-bottom:10px; color:var(--forest); font-size:12px; font-weight:700; }
        .day-options { display:flex; flex-wrap:wrap; gap:8px; }
        .day-option { display:flex; align-items:center; gap:6px; padding:9px 11px; border:1px solid var(--line); border-radius:7px; background:var(--soft); font-size:10px; cursor:pointer; }
        .day-option input { accent-color:var(--forest); }
        .settings-note,.settings-footer p { color:var(--muted); font-size:10px; line-height:1.5; }
        .holiday-list { display:grid; gap:8px; }
        .holiday-row { display:flex; align-items:center; gap:8px; }
        .holiday-row input { width:220px; max-width:100%; }
        .remove-holiday { border:0; background:transparent; color:var(--berry); font-size:10px; cursor:pointer; }
        .add-holiday { margin-top:10px; }
        .settings-footer { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-top:24px; padding-top:16px; border-top:1px solid var(--line); }
        .settings-footer p { margin:0; }
        .field-error { color:var(--berry); font-size:10px; font-weight:400; }
        @media(max-width:620px) {
            .operational-settings-panel { padding:16px; }
            .settings-grid { grid-template-columns:1fr; }
            .settings-footer { align-items:flex-start; flex-direction:column; }
        }
    </style>
    <script>
        const holidayList = document.getElementById('holiday-list');
        const addHoliday = document.getElementById('add-holiday');

        addHoliday.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'holiday-row';
            row.innerHTML = '<input type="date" name="tanggal_libur[]" aria-label="Tanggal libur"><button class="remove-holiday" type="button" aria-label="Hapus tanggal libur">Hapus</button>';
            holidayList.appendChild(row);
        });

        holidayList.addEventListener('click', (event) => {
            if (event.target.closest('.remove-holiday')) {
                event.target.closest('.holiday-row').remove();
            }
        });
    </script>
@endsection
