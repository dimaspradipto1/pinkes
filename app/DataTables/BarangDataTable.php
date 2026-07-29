<?php

namespace App\DataTables;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class BarangDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('DT_RowIndex', '')
            ->addColumn('foto', function ($item) {
                $noPhoto = 'https://ui-avatars.com/api/?name=' . urlencode($item->nama_barang) . '&background=e2e8f0&color=64748b&size=60&bold=true';

                if (!$item->foto_barang || $item->foto_barang === '-') {
                    $src = $noPhoto;
                } else {
                    $src = asset('storage/' . $item->foto_barang);
                }

                return '<img src="' . $src . '" alt="' . e($item->nama_barang) . '"
                    style="width:55px;height:55px;object-fit:cover;border-radius:10px;border:1px solid #e2e8f0;"
                    onerror="this.src=\'' . $noPhoto . '\'">';
            })
            ->addColumn('action', function ($item) {
                return '
                    <div class="d-flex justify-content-center gap-1">
                        <a href="' . route('barang.show', $item->id) . '" class="btn btn-info btn-sm text-white" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="' . route('barang.edit', $item->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="' . route('barang.destroy', $item->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm(\'Yakin ingin menghapus barang ini?\')" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['foto', 'action']);
    }

    public function query(Barang $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('barang-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->scrollX(true)
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')
                ->title('No')
                ->width('5%')
                ->addClass('text-center'),
            Column::computed('foto')
                ->title('Foto')
                ->width('80px')
                ->addClass('text-center')
                ->exportable(false)
                ->printable(false),
            Column::make('kode_barang')
                ->title('Kode Barang'),
            Column::make('nama_barang')
                ->title('Nama Barang'),
            Column::make('jumlah')
                ->title('Jumlah')
                ->addClass('text-center'),
            Column::make('keterangan')
                ->title('Keterangan'),
            Column::computed('action')
                ->title('Aksi')
                ->exportable(false)
                ->printable(false)
                ->width('15%')
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Barang_' . date('YmdHis');
    }
}
