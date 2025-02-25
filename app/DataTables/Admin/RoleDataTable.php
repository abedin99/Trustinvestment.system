<?php

namespace App\DataTables\Admin;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Request;
use Yajra\DataTables\Services\DataTable;

class RoleDataTable extends DataTable
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
                $html = '<a href="'. route('admin.roles.show', $query->slug) .'" class="btn btn-sm btn-primary m-1"><i class="fa fa-lock"></i> Permissions</a>';
                $html .= '<a href="'. route('admin.roles.edit', $query->slug) .'" class="btn btn-sm btn-info m-1"><i class="fa fa-edit"></i> Edit</a>';
                $html .= '<button class="btn btn-danger btn-sm remove-data" data-id="'. $id .'" data-action="'. route('admin.roles.destroy', $id) .'" onclick="deleteConfirmation(this)"><i class="fa fa-trash"></i> Delete</button>';
                return $html;
            })
            ->editColumn('users', function ($query) {
                if ($query->users_count == true) {
                    return $query->users_count;
                }
                return 0;
            })
            ->editColumn('permissions', function ($query) {
                if ($query->permissions_count == true) {
                    return $query->permissions_count;
                }
                return 0;
            })
            ->editColumn('status', function ($query) {
                if ($query->status == true) {
                    return "<span class=\"badge badge-success\">Active</span>";
                }else{
                    return "<span class=\"badge badge-danger\">Inactive</span>";
                }
            })
            ->editColumn('updated_at', function ($query) {
                if($query->updated_at != null){
                    return $query->updated_at->format('F d, Y h:s A');
                }
                return null;
            })
            ->setRowId('id')
            ->rawColumns(['id', 'status','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Spatie\Permission\Models\Role $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
    {
        activity()
            ->performedOn($model)
            ->causedBy(Auth::user())
            ->event('visited')
            ->withProperties(['url' => URL::current()])
            ->log('Role page '. Request::input('draw') .' visited.');

        return $model->orderBy('name', 'ASC')->withCount(['users', 'permissions'])->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('role-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->parameters([
                        'responsive' => true,
                        'autoWidth' => false,
                        'lengthMenu' => [
                                [ 10, 25, 50, -1 ],
                                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
                        ],                      
                        'dom'     => 'Bfrtip',
                        'buttons' => [
                            'pageLength',
                            [
                                'extend' => 'copyHtml5',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('copy', 'Role');
                                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                                }"
                            ],
                            [
                                'extend' => 'excel',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('excel', 'Role');
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                                }"
                            ],
                            [
                                'extend' => 'csv',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('csv', 'Role');
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                }"
                            ],
                            [
                                'extend' => 'pdfHtml5',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('pdf', 'Role');
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                }"
                            ],
                            [
                                'extend' => 'print',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('print', 'Role');
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
            Column::make('users'),
            Column::make('permissions'),
            Column::make('status'),
            Column::make('updated_at'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(250)
                  ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    public function filename() :string
    {
        return 'Roles_' . date('YmdHis');
    }
}