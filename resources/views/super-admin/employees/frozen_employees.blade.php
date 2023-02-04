@extends('layouts.master-datatable')

@section('content')
<div class="mt-2">
    <h4>Deleted Employee(s) Data</h4>
    <div class="card-body">
        <div class="col-md-12">
            <div class="form-row inline-flex space-x-6">
                <button type="button" name="bulk_restore" id="bulk_restore" class="mb-4 btn btn-outline-primary btn-sm">Restore
                    Checked</button>

                <button type="button" name="bulk_delete" id="bulk_delete" class="mb-4 btn btn-outline-danger btn-sm">Delete
                    Checked</button>
            </div>
        </div>
    </div>

    <div class="table-responsive">

        <table class="display nowrap" id="data-list" style="width: 100%">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select_all" /></th>
                    <th>No.</th>
                    <th>Staff No.</th>
                    <th>Employee Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="sample_form" class="form-horizontal">
                <span id="restore_result"></span>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin: 0;">Are you sure you want to restore selected employee?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="ok_restore" name="ok_restore">Yes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Multi - Restore Modal -->
<div class="modal fade" id="multiRestoreModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="sample_form" class="form-horizontal">
                <span id="multi_result"></span>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin: 0;">Are you sure you want to restore selected employee(s)?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="multi_restore" name="multi_restore">Yes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="sample_form" class="form-horizontal">
                <span id="delete_result"></span>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 align="center"><strong>Permanently delete</strong></h3>
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete selected data?</h4>
                </div>
                <div style="text-align: center;">
                    <span class="delete_prompt" style="border:1px solid red;"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="ok_delete" name="ok_delete">Yes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Multi Modal -->
<div class="modal fade" id="confirmMultiModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="sample_form" class="form-horizontal">
                <span id="multi_delete_result"></span>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h3 align="center"><strong>Permanently delete</strong></h3>
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete selected data?</h4>
                </div>
                <div style="text-align: center;">
                    <span class="bulk_delete_prompt" style="border:1px solid red;"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="multi_delete" name="multi_delete">Yes Please</button>
                </div>
            </form>
        </div>
    </div>
</div>

@stop

@push('scripts')
<script>
    $(function(){
        $.fn.dataTable.ext.errMode = 'throw';
            var table = $('#data-list').DataTable({
                processing: true,
                serverSide: true,
                autoFill: true,
                dom: 'Blfritip',
                buttons: [
                    'selectAll',
                    'selectNone',
                    {
                        extend: 'excelHtml5',
                        exportOptions: {
                            columns: ':visible'
                        },
                        autoFilter: true,
                        title: 'Employees Data',
                        sheetName: 'Employees',
                    },
                    {
                        extend: 'colvis',
                        columnText: function ( dt, idx, title ) {
                            return (idx+1)+': '+title;
                        }
                    },
                ],
                "lengthMenu" : [ [50, 75, 100, -1], [50, 75, 100, "All"] ],
                responsive: true,
                ajax: { 
                    "type" : "POST",
                    "url" : "{!! route('super-admin.employee.frozen') !!}",
                    "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                },
                "search": {
                    "caseInsensitive": true
                },
                "oLanguage": {
                    "sProcessing": "<span>Please wait...</span>"
                },
                select: true,
                columns: [           
                    {data: 'checkbox', name: 'checkbox', orderable: false, searchable: false,},
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'staff_no', name: 'staff_no', orderable: true, searchable: true},
                    {data: 'employee_name', name: 'employee_name', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false},                    
               ],               
            });           
        });

        var restore_id;

        $(document).on('click', '.restore', function(event){
            event.preventDefault();
            restore_id = $(this).attr('id');
            $('#restoreModal').modal('show');
        });

        $('#ok_restore').click(function(){
            $.ajax({
                url: "/super-admin/employee/"+restore_id+"/restore",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType:"json",
                beforeSend:function(){
                    $('#ok_restore').text('Restoring...');
                },
                success: function(data){
                    var html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#restore_result').html(html);
                    setTimeout(function(){
                        $('#data-list').DataTable().ajax.reload();                        
                        $('#ok_restore').text('Yes Please');
                        $('#restoreModal').modal('hide');
                        $('#restore_result').html('');
                    }, 2000);
                },
                error: function(data){
                    var errors = data.responseJSON;
                    var html = '<div class="alert alert-danger">' + data.errors + '</div>';
                    $('#restore_result').html(html);
                }
            });
        });

        $(document).ready(function(){
            $('#select_all').change(function(){
                $('.employees_checkbox').prop('checked', this.checked);
            });

            $('.employees_checkbox').change(function(){
                if($('.employees_checkbox:checked').length == $('.employees_checkbox').length){
                    $('#select_all').prop('checked', true);
                }else{
                    $('#select_all').prop('checked', false);
                }
            });
        });        
        
        $(document).on('click', '#bulk_restore', function(){               

            var id = [];
            $('.employees_checkbox:checked').each(function(){
                $('.modal-title').text('Restore Selected Employees');
                id.push($(this).val());
                $('#multiRestoreModal').modal('show');
            });
            if(id.length > 0){
                $('#multi_restore').click(function(){
                    $.ajax({
                        url: "{{ route('super-admin.unfreeze.employees') }}",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        method: "get",
                        data:{id:id},
                        beforeSend:function(){
                            $('#multi_restore').text('Restoring...');
                        },
                        success: function(data){
                            var html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#multi_result').html(html);
                            setTimeout(function(){
                                $('#select_all').prop('checked', false);
                                $('#data-list').DataTable().ajax.reload();                        
                                $('#multi_restore').text('Yes Please');
                                $('#multiRestoreModal').modal('hide');
                                $('#multi_result').html('');
                            }, 2000);
                        },
                        error: function(data){
                            var errors = data.responseJSON;
                            var html = '<div class="alert alert-danger">' + data.errors + '</div>';
                            $('#multi_result').html(html);
                        }
                    });
                }); 
            }else{
                alert("Please select at least one record");
            }
        });

        $('#restoreModal').on('hidden.bs.modal', function(){
            $('#ok_restore').text('Yes Please');
        });

        $('#multiRestoreModal').on('hidden.bs.modal', function(){
            $('#multi_restore').text('Yes Please');
        });

        $(document).on('click', '#bulk_delete', function(){               

            var id = [];
            $('.employees_checkbox:checked').each(function(){
                $('.modal-title').text('Delete Selected Employees');
                id.push($(this).val());
                $('#confirmMultiModal').modal('show');
                $('.bulk_delete_prompt').text(`Please note that this action is irreversible.
            The employees and all their associated data will be permanently deleted.`);
            });
            if(id.length > 0){
                $('#multi_delete').click(function(){
                    $.ajax({
                        url: "{{ route('super-admin.delete.employees.forever') }}",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        method: "get",
                        data:{id:id},
                        beforeSend:function(){
                            $('#multi_delete').text('Deleting...');
                        },
                        success: function(data){
                            var html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#multi_delete_result').html(html);
                            setTimeout(function(){
                                $('#select_all').prop('checked', false);
                                $('#data-list').DataTable().ajax.reload();                        
                                $('#multi_delete').text('Yes Please');
                                $('#confirmMultiModal').modal('hide');
                                $('#multi_delete_result').html('');
                            }, 2000);
                        },
                        error: function(data){
                            var errors = data.responseJSON;
                            var html = '<div class="alert alert-danger">' + data.errors + '</div>';
                            $('#multi_delete_result').html(html);
                        }
                    });
                }); 
            }else{
                alert("Please select at least one record");
            }
        });       

        var employee_id;

        $(document).on('click', '.delete', function(){
            $('.modal-title').text('Delete Employee');
            employee_id = $(this).attr('id');
            $('#confirmModal').modal('show');
            $('.delete_prompt').text(`Please note that this action is irreversible.
            The employee and all their associated data will be permanently deleted.`);
        });

        $('#ok_delete').click(function(){
            $.ajax({
                url:"/super-admin/employee/delete/"+employee_id,
                beforeSend:function(){
                    $('#ok_delete').text('Deleting...');
                },
                success:function(data){
                    var html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#delete_result').html(html);
                    setTimeout(function(){
                        $('#data-list').DataTable().ajax.reload();                        
                        $('#ok_delete').text('Yes Please');
                        $('#confirmModal').modal('hide');
                        $('#delete_result').html('');
                    }, 2000);                    
                }                
            });
        });
        
        $('#confirmModal').on('hidden.bs.modal', function(){
            $('#ok_delete').text('Yes Please');
        });

        $('#confirmMultiModal').on('hidden.bs.modal', function(){
            $('#ok_delete').text('Yes Please');
        });
               
</script>
<script></script>
@endpush