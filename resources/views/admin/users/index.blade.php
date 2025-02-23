<x-admin-app-layout>
    {{-- meta_title --}}
    <x-slot name="meta_title">
        Users
    </x-slot>

    <x-slot name="header_components">
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    </x-slot>

    <x-slot name="footer_components">
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    </x-slot>

    <x-slot name="js">
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    </x-slot>

    <div class="container-fluid">
        <h4 class="c-grey-900 mT-10 mB-30">User List</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="bgc-white bd bdrs-3 p-20 mB-20">
                    {{ $dataTable->table(['class' => 'table table-bordered table-striped table-hover']) }}
                </div>
            </div>
        </div>
    </div>
</x-admin-app-layout>
