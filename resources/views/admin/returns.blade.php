@extends('admin.layout')
@section('title', 'Return Barang')
@section('content')
    <div class="page-heading">
        <div>
            <h2>Return Barang</h2>
            <p>Periksa pengajuan, penerimaan barang, kelayakan stok, dan pengembalian dana pelanggan.</p>
        </div>
    </div>
    <section class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pesanan / Pelanggan</th>
                        <th>Produk</th>
                        <th>Pengajuan</th>
                        <th>Bukti / Rekening</th>
                        <th>Keputusan / Status Barang</th>
                        <th>Pengembalian Dana</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($returns as $item)
                        <tr>
                            <td>
                                <strong>#SP{{ str_pad((string) $item->id_pesanan, 6, '0', STR_PAD_LEFT) }}</strong>
                                <div>{{ $item->nama_pelanggan }}</div>
                            </td>
                            <td>
                                {{ $item->nama_produk ?: 'Produk tidak tersedia' }}
                                <div>Jumlah: {{ $item->jumlah }}</div>
                                <div>Nilai: Rp{{ number_format($item->jumlah_pengembalian, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <div>{{ $item->status_return }}</div>
                                <div>{{ $item->alasan }}</div>
                                <div><a href="{{ route('admin.returns.proof', [$item->id_pesanan, $item->id_return]) }}">Lihat foto bukti</a></div>
                                @if ($item->catatan_admin)
                                    <div>Catatan: {{ $item->catatan_admin }}</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $item->bank_pengembalian }}</div>
                                <div>{{ $item->nama_pemilik_rekening }}</div>
                                <div>{{ $item->nomor_rekening }}</div>
                            </td>
                            <td>
                                @if ($item->status_return === 'Diajukan')
                                    <form method="POST" action="{{ route('admin.returns.review', [$item->id_pesanan, $item->id_return]) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="field">
                                            <label for="catatan-{{ $item->id_return }}">Catatan</label>
                                            <textarea id="catatan-{{ $item->id_return }}" name="catatan_admin" rows="2"></textarea>
                                        </div>
                                        <div class="actions">
                                            <button class="button button-small" name="keputusan" value="setujui" type="submit">Setujui</button>
                                            <button class="button button-small button-outline" name="keputusan" value="tolak" type="submit">Tolak</button>
                                        </div>
                                    </form>
                                @elseif ($item->status_return === 'Disetujui')
                                    <form method="POST" action="{{ route('admin.returns.status', [$item->id_pesanan, $item->id_return]) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status_return" value="Barang Diterima">
                                        <button class="button button-small" type="submit">Catat Barang Diterima</button>
                                    </form>
                                @elseif ($item->status_return === 'Barang Diterima')
                                    <form method="POST" action="{{ route('admin.returns.status', [$item->id_pesanan, $item->id_return]) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="field">
                                            <label for="catatan-status-{{ $item->id_return }}">Catatan pemeriksaan</label>
                                            <textarea id="catatan-status-{{ $item->id_return }}" name="catatan_admin" rows="2"></textarea>
                                        </div>
                                        <div class="actions">
                                            <button class="button button-small" name="status_return" value="Layak Dijual" type="submit">Layak Dijual</button>
                                            <button class="button button-small button-outline" name="status_return" value="Tidak Layak" type="submit">Tidak Layak</button>
                                        </div>
                                    </form>
                                @elseif ($item->stok_dikembalikan_at)
                                    <span>Stok diperbarui {{ \Illuminate\Support\Carbon::parse($item->stok_dikembalikan_at)->format('d/m/Y H:i') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->status_pengembalian_dana }}</strong>
                                @if (in_array($item->status_return, ['Layak Dijual', 'Tidak Layak'], true))
                                    @if ($item->status_pengembalian_dana === 'Menunggu')
                                        <form method="POST" action="{{ route('admin.returns.refund', [$item->id_pesanan, $item->id_return]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="aksi" value="mulai_transfer">
                                            <button class="button button-small" type="submit">Mulai Transfer</button>
                                        </form>
                                    @elseif ($item->status_pengembalian_dana === 'Diproses')
                                        <form method="POST" action="{{ route('admin.returns.refund', [$item->id_pesanan, $item->id_return]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="aksi" value="selesai_transfer">
                                            <label for="referensi-{{ $item->id_return }}">Referensi transfer</label>
                                            <input id="referensi-{{ $item->id_return }}" name="referensi_transfer" maxlength="100">
                                            <button class="button button-small" type="submit">Tandai Transfer Selesai</button>
                                        </form>
                                    @else
                                        <div>{{ $item->referensi_transfer ?: 'Transfer tercatat selesai' }}</div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Belum ada pengajuan return barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $returns->links() }}</div>
    </section>
@endsection
