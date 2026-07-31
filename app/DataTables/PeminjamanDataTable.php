<?php

namespace App\DataTables;

use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PeminjamanDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $isAdmin = Auth::user()->isAdmin();

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('DT_RowIndex', '')
            ->addColumn('nama_peminjam', fn($item) => $item->user?->name ?? '-')
            ->addColumn('nama_barang',   fn($item) => $item->barang?->nama_barang ?? '-')
            ->addColumn('nama_ruangan',  fn($item) => $item->ruangan?->nama_ruangan ?? '-')
            ->addColumn('tujuan_pinjam', fn($item) => $item->pinjam?->tujuan_pinjam ?? '-')
            ->addColumn('tgl_peminjaman', fn($item) => $item->tgl_peminjaman
                ? \Carbon\Carbon::parse($item->tgl_peminjaman)->translatedFormat('d F Y') . '<br><small class="text-muted">' . $item->jam_peminjaman . '</small>'
                : '-')
            ->addColumn('tgl_pengembalian', fn($item) => $item->tgl_pengembalian
                ? \Carbon\Carbon::parse($item->tgl_pengembalian)->translatedFormat('d F Y') . '<br><small class="text-muted">' . $item->jam_pengembalian . '</small>'
                : '-')
            ->addColumn('status', function ($item) {
                $map = [
                    'pending'  => ['bg-warning',  'text-dark', 'Menunggu Verifikasi'],
                    'validasi' => ['bg-primary',  'text-white', 'Divalidasi'],
                    'ditolak'  => ['bg-danger',   'text-white', 'Ditolak'],
                    'selesai'  => ['bg-success',  'text-white', 'Selesai'],
                ];
                $s = $map[$item->status] ?? ['bg-secondary', 'text-white', $item->status];
                return '<span class="badge ' . $s[0] . ' ' . $s[1] . '">' . $s[2] . '</span>';
            })
            ->addColumn('action', function ($item) use ($isAdmin) {
                $btns = '<div class="d-flex justify-content-center gap-1 flex-wrap">';

                // Detail
                $btns .= '<a href="' . route('peminjaman.show', $item->id) . '" class="btn btn-info btn-sm text-white" title="Detail"><i class="bi bi-eye"></i></a>';

                if ($isAdmin) {
                    // Edit selalu bisa untuk admin
                    $btns .= '<a href="' . route('peminjaman.edit', $item->id) . '" class="btn btn-warning btn-sm text-white" title="Edit"><i class="bi bi-pencil-square"></i></a>';

                    // Verifikasi (hanya jika pending)
                    if ($item->status === 'pending') {
                        $btns .= '
                            <form action="' . route('peminjaman.verifikasi', $item->id) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-success btn-sm" title="Verifikasi"
                                    onclick="return confirm(\'Verifikasi peminjaman ini?\')">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>';
                    }

                    // Tolak (jika pending atau validasi)
                    if (in_array($item->status, ['pending', 'validasi'])) {
                        $btns .= '
                            <form action="' . route('peminjaman.tolak', $item->id) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-danger btn-sm" title="Tolak"
                                    onclick="return confirm(\'Tolak peminjaman ini?\')">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>';
                    }

                    // Selesai (hanya jika validasi)
                    if ($item->status === 'validasi') {
                        $btns .= '
                            <form action="' . route('peminjaman.selesai', $item->id) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . '
                                <button type="submit" class="btn btn-secondary btn-sm" title="Tandai Selesai"
                                    onclick="return confirm(\'Tandai peminjaman ini sebagai selesai?\')">
                                    <i class="bi bi-flag-fill"></i>
                                </button>
                            </form>';
                    }

                    // Hapus (Admin can delete any)
                    $btns .= '
                        <form action="' . route('peminjaman.destroy', $item->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                onclick="return confirm(\'Yakin ingin menghapus peminjaman ini?\')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>';
                } else {
                    // User: edit & hapus hanya jika pending
                    if ($item->status === 'pending') {
                        $btns .= '<a href="' . route('peminjaman.edit', $item->id) . '" class="btn btn-warning btn-sm text-white" title="Edit"><i class="bi bi-pencil-square"></i></a>';
                        $btns .= '
                            <form action="' . route('peminjaman.destroy', $item->id) . '" method="POST" style="display:inline;">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                    onclick="return confirm(\'Yakin ingin menghapus peminjaman ini?\')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>';
                    }
                }

                $btns .= '</div>';
                return $btns;
            })
            ->rawColumns(['status', 'action', 'tgl_peminjaman', 'tgl_pengembalian'])
            ->setRowId('id');
    }

    public function query(Peminjaman $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['user', 'barang', 'ruangan', 'pinjam']);

        // User biasa hanya lihat data sendiri
        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }

        return $query->latest('created_at');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('peminjaman-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
            ->selectStyleSingle()
            ->scrollX(true)
            ->scrollCollapse(true);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('No')->width('5%')->addClass('text-center'),
            Column::computed('nama_peminjam')->title('Peminjam'),
            Column::computed('nama_barang')->title('Barang'),
            Column::computed('nama_ruangan')->title('Ruangan'),
            Column::computed('tujuan_pinjam')->title('Tujuan Pinjam'),
            Column::computed('tgl_peminjaman')->title('Tgl Pinjam')->addClass('text-nowrap'),
            Column::computed('tgl_pengembalian')->title('Tgl Kembali')->addClass('text-nowrap'),
            Column::computed('status')->title('Status')->addClass('text-center'),
            Column::computed('action')->title('Aksi')->exportable(false)->printable(false)->width('18%')->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Peminjaman_' . date('YmdHis');
    }
}
