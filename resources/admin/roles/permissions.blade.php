<x-app-layout>
    <x-slot name="meta_title">
        Role Permissions
    </x-slot>

    <x-slot name="css">

    </x-slot>

    <x-slot name="js">
        <script>
            $(document).ready(function() {
                // Select all "General" checkboxes
                $('#check_all_general').on('change', function() {
                    $('.general-checkbox').prop('checked', this.checked);
                });

                // Select all "Others" checkboxes
                $('#check_all_others').on('change', function() {
                    $('.others-checkbox').prop('checked', this.checked);
                });

                $('.check_all_module').on('change', function() {
                    var $tr = $(this).closest('tr'); // Get the closest <tr> element
                    var isChecked = $(this).is(':checked'); // Check if the current checkbox is checked

                    // Find all checkboxes within this <tr> and set their checked state
                    $tr.find('input[type="checkbox"]').prop('checked', isChecked);
                });
            });
        </script>
    </x-slot>

    <x-slot name="header_components">
        {{--  --}}
    </x-slot>

    <x-slot name="footer_components">
        {{--  --}}
    </x-slot>


    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Role Permissions</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Index</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Role Permissions</div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.roles.permissions', $role->slug) }}" method="POST"
                                class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th>Module Name</th>
                                            <th>
                                                <label class="form-check-label ml-3 cursor-pointer">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="check_all_general" name="checked_all_general"
                                                        value="checked">
                                                    General
                                                </label>
                                            </th>
                                            <th>
                                                <label class="form-check-label ml-3 cursor-pointer">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="check_all_others" name="checked_all_others" value="checked">
                                                    Others
                                                </label>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @csrf
                                        @foreach ($permissions->groupBy('group') as $permission)
                                            <tr>
                                                <td>
                                                    <label class="form-check-label ml-3 cursor-pointer">
                                                        <input type="checkbox" class="form-check-input check_all_module"
                                                            data-general-target="general-{{ $permission[0]['slug'] }}"
                                                            data-others-target="others-{{ $permission[0]['slug'] }}"
                                                            name="checked_all_{{ $permission[0]['slug'] }}"
                                                            value="checked">
                                                        {{ Str::headline(str_replace('.', ' ', $permission[0]['group'])) }}
                                                    </label>
                                                </td>
                                                <td id="general-{{ $permission[0]['slug'] }}">
                                                    @foreach ($permission->where('type', 'general') as $group)
                                                        <div class="form-check">
                                                            <label class="form-check-label cursor-pointer">
                                                                <input type="checkbox"
                                                                    class="form-check-input general-checkbox"
                                                                    name="permission[]" value="{{ $group->name }}"
                                                                    @if ($role->hasPermissionTo($group->name)) checked @endif>
                                                                {{ Str::headline($group->display_name) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td id="others-{{ $permission[0]['slug'] }}">
                                                    @foreach ($permission->where('type', 'others') as $group)
                                                        <div class="form-check">
                                                            <label class="form-check-label cursor-pointer">
                                                                <input type="checkbox"
                                                                    class="form-check-input others-checkbox"
                                                                    name="permission[]" value="{{ $group->name }}"
                                                                    @if ($role->hasPermissionTo($group->name)) checked @endif>
                                                                {{ Str::headline($group->display_name) }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="text-right pb-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</x-app-layout>
