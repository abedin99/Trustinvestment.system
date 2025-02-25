<?php

namespace App\DataTables\Admin;

use App\Models\Admin;
use Yajra\DataTables\DataTables;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class AdminUsersDataTable extends DataTable
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
                $html = '<a href="' . route('admin.admins.show', $id) . '" class="btn btn-sm btn-info m-1"><i class="fa fa-history"></i> Activity Logs</a>';
                $html .= '<a href="' . route('admin.admins.edit', $id) . '" class="btn btn-sm btn-primary m-1"><i class="fa fa-edit"></i> Edit</a>';
                $html .= '<button class="btn btn-danger btn-sm remove-data" data-id="' . $id . '" data-action="' . route('admin.admins.destroy', $id) . '" onclick="deleteConfirmation(this)"><i class="fa fa-trash"></i> Delete</button>';
                return $html;
            })
            ->editColumn('name', function ($query) {
                $html = '';
                if ($query->name != null) {
                    $html .= $query->name;
                }
                if($query->isOnline()){
                    $html .= "<i class=\"fas fa-solid fa-circle text-success\" title=\"Available in online\"></i>";
                }
                if ($query->banned_at != null) {
                    $html .= "<br><span class=\"badge badge-danger\">Banned</span>";
                }
                if($query->disabled_at != null){
                    $html .= "<br><span class=\"badge badge-warning\">disabled</span>";
                }
                return $html;
            })
            ->editColumn('roles', function ($query) {
                $html = "";
                if ($query->roles != null) {
                    $role = $query->roles->first();
                    if ($role) {
                        $html .= "<span class=\"badge badge-secondary\">" . $role->name . "</span>";
                    }
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
            ->rawColumns(['name', 'roles', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Admin $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Admin $model)
    {
        activity()
            ->performedOn($model)
            ->causedBy(Auth::user())
            ->event('visited')
            ->withProperties(['url' => URL::current()])
            ->log('Admin User page ' . Request::input('draw')  . ' visited.');

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
            ->setTableId('admins-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters([
                'responsive' => true,
                'autoWidth' => false,
                'lengthMenu' => [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'dom'     => 'Bfrtip',
                'buttons' => [
                    'pageLength',
                    [
                        'extend' => 'copyHtml5',
                        'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('copy', 'Admin');
                                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                                }"
                    ],
                    [
                        'extend' => 'excel',
                        'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('excel', 'Admin');
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                                }"
                    ],
                    [
                        'extend' => 'csv',
                        'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('csv', 'Admin');
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                }"
                    ],
                    [
                        'extend' => 'pdfHtml5',
                        'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('pdf', 'Admin');
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                }"
                    ],
                    [
                        'extend' => 'print',
                        'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('print', 'Admin');
                                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                                }"
                    ],
                ],
            ])
            ->orderBy(0, 'desc')
            ->paging(true)
            ->ordering(true);
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
            Column::make('username'),
            Column::make('email'),
            Column::make('roles'),
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
        return 'Admins_' . date('YmdHis');
    }
}
