<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Peminjaman</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 10pt; line-height: 1.4; color: #000; margin: 0; padding: 20px 30px; }

        /* KOP SURAT */
        .kop-surat { width: 100%; margin-bottom: 0; border: none !important; border-collapse: collapse; }
        .kop-surat td { vertical-align: middle; padding: 0; border: none !important; }
        .kop-logo { width: 100px; text-align: center; }
        .kop-logo img { width: 90px; height: auto; }
        .kop-spacer { width: 100px; }
        .kop-text { text-align: center; padding: 0 6px; }
        .kop-text .instansi { font-size: 11pt; font-weight: normal; margin: 0; letter-spacing: 0.5px; }
        .kop-text .dinas { font-size: 16pt; font-weight: bold; margin: 2px 0; letter-spacing: 1px; }
        .kop-text .alamat { font-size: 8.5pt; margin: 2px 0 0; }
        .kop-text .kontak { font-size: 8.5pt; margin: 1px 0; }
        .kop-text .website { font-size: 8pt; margin: 1px 0; }
        .garis-kop { border: none; border-top: 4px solid #000; margin: 6px 0 2px; }
        .garis-kop-tipis { border: none; border-top: 1px solid #000; margin: 0 0 10px; }

        /* JUDUL */
        .judul-laporan { text-align: center; margin: 12px 0 6px; }
        .judul-laporan h1 { font-size: 12pt; font-weight: bold; margin: 0; text-decoration: underline; text-transform: uppercase; }
        .judul-laporan .sub { font-size: 10pt; margin: 2px 0 0; font-weight: normal; }

        /* FILTER INFO */
        .filter-info { margin-bottom: 10px; font-size: 9pt; }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 9pt; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f2f2f2; padding: 5px 7px; font-weight: bold; text-align: center; }
        td { padding: 5px 7px; vertical-align: top; }
        .text-center { text-align: center; }

        /* FOOTER */
        .footer {
            position: absolute;
            bottom: 40px;
            left: 30px;
            right: 30px;
        }
        .ttd-block { float: right; width: 240px; text-align: left; font-size: 10pt; }
        .ttd-block .ttd-jabatan { margin: 0 0 50px 0; line-height: 1.5; }
        .ttd-block .ttd-nama { font-weight: bold; margin: 0; text-decoration: underline; }
        .ttd-block .ttd-pangkat { margin: 0; }
        .ttd-block .ttd-nip { margin: 0; }
        .clearfix::after { content: ""; display: table; clear: both; }

        /* STATUS BADGE */
        .badge-pending  { background: #ffc107; padding: 1px 6px; border-radius: 4px; font-size: 8pt; }
        .badge-validasi { background: #0d6efd; color: #fff; padding: 1px 6px; border-radius: 4px; font-size: 8pt; }
        .badge-ditolak  { background: #dc3545; color: #fff; padding: 1px 6px; border-radius: 4px; font-size: 8pt; }
        .badge-selesai  { background: #198754; color: #fff; padding: 1px 6px; border-radius: 4px; font-size: 8pt; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <table class="kop-surat" cellpadding="0" cellspacing="0">
        <tr>
            <td class="kop-logo">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/logo.png'))) }}" alt="Logo Kota Batam">
            </td>
            <td class="kop-text">
                <p class="instansi">PEMERINTAH KOTA BATAM</p>
                <p class="dinas">DINAS KESEHATAN</p>
                <p class="alamat">Jl. Raja Haji No. 7 Sekupang</p>
                <p class="kontak">Telp. (0778) 322060 Fax. (0778) 321856</p>
                <p class="website">https://dinkes.batam.go.id, Pos-el dinkes.batam@gmail.com,</p>
            </td>
            <td class="kop-spacer"></td>
        </tr>
    </table>
    <hr class="garis-kop">
    <hr class="garis-kop-tipis">

    {{-- JUDUL --}}
    <div class="judul-laporan">
        <h1>Rekapitulasi Peminjaman
            @if($jenis === 'barang') Barang
            @elseif($jenis === 'ruangan') Ruangan
            @else Barang dan Ruangan
            @endif
        </h1>
        <p class="sub">Dinas Kesehatan Kota Batam</p>
    </div>

    <div class="filter-info">
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}<br>
        Periode: 
        @if($tgl_mulai && $tgl_selesai)
            {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('d F Y') }}
        @elseif($tgl_mulai)
            Mulai {{ \Carbon\Carbon::parse($tgl_mulai)->translatedFormat('d F Y') }}
        @elseif($tgl_selesai)
            Sampai {{ \Carbon\Carbon::parse($tgl_selesai)->translatedFormat('d F Y') }}
        @else
            Semua Data (Seluruh Periode)
        @endif
    </div>

    {{-- TABEL DATA --}}
    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Peminjam</th>
                <th>Jenis</th>
                <th>Barang / Ruangan</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Tujuan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjamen as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $item->user?->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->barang_id ? 'Barang' : 'Ruangan' }}</td>
                    <td>
                        @if($item->barang_id)
                            <strong>{{ $item->barang?->nama_barang ?? '-' }}</strong><br>
                            <small>{{ $item->barang?->kode_barang }}</small>
                        @else
                            <strong>{{ $item->ruangan?->nama_ruangan ?? '-' }}</strong><br>
                            <small>{{ $item->ruangan?->kode_ruangan }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->tgl_peminjaman)->format('d/m/Y') }}<br>
                        <small>{{ $item->jam_peminjaman }}</small>
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->tgl_pengembalian)->format('d/m/Y') }}<br>
                        <small>{{ $item->jam_pengembalian }}</small>
                    </td>
                    <td>{{ $item->pinjam?->tujuan_pinjam ?? '-' }}</td>
                    <td class="text-center">
                        @php $sMap = ['pending'=>'Menunggu','validasi'=>'Divalidasi','ditolak'=>'Ditolak','selesai'=>'Selesai']; @endphp
                        <span class="badge-{{ $item->status }}">{{ $sMap[$item->status] ?? $item->status }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Tidak ada data untuk periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="footer clearfix">
        <div class="ttd-block">
            <p class="ttd-jabatan">
                @if(!empty($penandatangan->jabatan_nama))
                    {!! nl2br(e($penandatangan->jabatan_nama)) !!}
                @else
                    Kepala Sub Bagian Umum<br>dan Kepegawaian
                @endif
            </p>
            <p class="ttd-nama">{{ $penandatangan->name ?? '..........................................' }}</p>
            @if(!empty($penandatangan->pangkat_golongan) && $penandatangan->pangkat_golongan !== '-')
                <p class="ttd-pangkat">{{ $penandatangan->pangkat_golongan }}</p>
            @endif
            <p class="ttd-nip">NIP. {{ $penandatangan->nip ?? '-' }}</p>
        </div>
    </div>

</body>
</html>
