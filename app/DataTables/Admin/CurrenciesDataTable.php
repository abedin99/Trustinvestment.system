<?php

namespace App\DataTables\Admin;

use App\Http\Helpers\Permission;
use App\Models\Currency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CurrenciesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($query) {
                $id = Crypt::encryptString($query->id);
                $html  = '';
                if (Permission::permit('currency_edit')) {
                    $html .= '<a href="' . route('admin.currencies.edit', $id) . '" class="btn btn-sm btn-info m-1"><i class="fa fa-edit"></i> Edit</a>';
                }
                if (Permission::permit('currency_delete')) {
                    $html .= '<button class="btn btn-danger btn-sm remove-data" data-id="' . $id . '" data-action="' . route('admin.currencies.destroy', $id) . '" onclick="deleteConfirmation(this)"><i class="fa fa-trash"></i> Delete</button>';
                }
                return $html;
            })
            ->editColumn('name', function ($query) {
                $html = '';
                if ($query->name != null) {
                    $html .= $query->name;
                }
                return $html;
            })
            ->editColumn('updated_at', function ($query) {
                if ($query->updated_at != null) {
                    return $query->updated_at->format('F d, Y h:s A');
                }
                return null;
            })
            ->setRowId('id')
            ->rawColumns(['name', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Currency $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Currency $model)
    {
        activity()
            ->performedOn($model)
            ->causedBy(Auth::user())
            ->event('Page Visit')
            ->withProperties(['url' => URL::current()])
            ->log('Currency page ' . Request::input('draw') . ' visited.');

        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('currencies-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'desc')
            ->paging(true)
            ->ordering(true)
            ->parameters([
                'dom' => 'Bfrtip',
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'buttons' => [
                    'pageLength',
                    [
                        'extend' => 'copyHtml5',
                        'action' => "function(e, dt, button, config) {
                                    logExportActivity('copy', 'Currency');
                                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                                }"
                    ],
                    [
                        'extend' => 'excel',
                        'action' => "function(e, dt, button, config) {
                                    logExportActivity('excel', 'Currency');
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                                }"
                    ],
                    [
                        'extend' => 'csv',
                        'action' => "function(e, dt, button, config) {
                                    logExportActivity('csv', 'Currency');
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                }"
                    ],
                    [
                        'extend' => 'pdfHtml5',
                        'action' => "function(e, dt, button, config) {
                                    logExportActivity('pdf', 'Currency');
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                }"
                    ],
                    [
                        'extend' => 'print',
                        'action' => "function(e, dt, button, config) {
                                    logExportActivity('print', 'Currency');
                                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                                }"
                    ],
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('symbol')
                ->addClass('text-center'),
            Column::make('currency')
                ->title('Currency Code'),
            Column::make('updated_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(160)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    public function filename(): string
    {
        return 'Payment_types' . date('YmdHis');
    }
}
