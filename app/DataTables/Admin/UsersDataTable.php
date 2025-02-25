<?php

namespace App\DataTables\Admin;

use App\Models\User;
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

class UsersDataTable extends DataTable
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
                $html = '<a href="'. route('admin.users.edit', $id) .'" class="btn btn-sm btn-primary m-1"><i class="fa fa-edit"></i> Edit</a>';
                $html .= '<a href="'. route('admin.users.activity-logs', $id) .'" class="btn btn-sm btn-info m-1"><i class="fa fa-history"></i> Activity Logs</a>';
                $html .= '<button class="btn btn-danger btn-sm remove-data" data-id="'. $id .'" data-action="'. route('admin.users.destroy', $id) .'" onclick="deleteConfirmation(this)"><i class="fa fa-trash"></i> Delete</button>';
                return $html;
            })
            ->editColumn('name', function ($query) {
                $html = '';
                if($query->name != null){
                    $html .= $query->name;
                }
                if($query->isOnline()){
                    $html .= "<i class=\"fas fa-solid fa-circle text-success\" title=\"Available in online\"></i>";
                }
                if($query->banned_at != null){
                    $html .= "<br><span class=\"badge badge-danger\">Banned</span>";
                }
                if($query->disabled_at != null){
                    $html .= "<br><span class=\"badge badge-warning\">disabled</span>";
                }
                return $html;
            })
            ->editColumn('updated_at', function ($query) {
                if($query->updated_at != null){
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
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        activity()
            ->performedOn($model)
            ->causedBy(Auth::user())
            ->event('visited')
            ->withProperties(['url' => URL::current()])
            ->log('User page '. Request::input('draw') .' visited.');

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
                    ->setTableId('users-table')
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
                                    adminLogExportActivity('copy', 'User');
                                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                                }"
                            ],
                            [
                                'extend' => 'excel',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('excel', 'User');
                                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                                }"
                            ],
                            [
                                'extend' => 'csv',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('csv', 'User');
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                }"
                            ],
                            [
                                'extend' => 'pdfHtml5',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('pdf', 'User');
                                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                }"
                            ],
                            [
                                'extend' => 'print',
                                'action' => "function(e, dt, button, config) {
                                    adminLogExportActivity('print', 'User');
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
            Column::make('updated_at'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(240)
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
        return 'Users_' . date('YmdHis');
    }
}