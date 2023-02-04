@extends('layouts.datatable')

@section('content')
<div class="mt-2">
    <h4>Admin Employee Data</h4>
    <div class="card-body">
        <div class="col-md-12">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="payroll_number">Payroll No.</label>
                    <input type="number" class="form-control" id="payroll_no1" placeholder="####">
                </div>
                <div class="form-group col-md-2">
                    <label for="employee_name">Name</label>
                    <input type="text" class="form-control" id="employee_name1" placeholder="Harvester Name">
                </div>
                <div class="form-group col-md-2">
                    <label for="department">Department</label>
                    <input type="text" class="form-control" id="department1" placeholder="Department">
                </div>
                <div class="form-group col-md-2">
                    <label for="designation">Designation</label>
                    <input type="text" class="form-control" id="designation1" placeholder="Designation">
                </div>
            </div>

            <div class="form-row inline-flex space-x-6">
                <button type="button" onclick="reload_table()" class="mb-4 btn btn-primary">Filter</button>
                <a href="{{ route('admin.employees.alpha') }}" class="mb-4 btn btn-secondary">Refresh</a>

                <button type="button" name="bulk_delete" id="bulk_delete" class="mb-4 btn btn-danger btn-sm">Delete
                    Checked</button>

                <button type="button" name="create_employee" id="create_employee" class="mb-4 btn btn-success">Add
                    Employee
                </button>

                <form class="px-12" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <input type="file" name="employee_import_file">
                    <button class="px-2 py-1 bg-green-500 hover:bg-green-700 rounded-md" type="submit">Import
                        Employees</button>
                </form>

                <a href="{{ route('admin.employees.deleted') }}" class="mb-4 btn btn-outline-secondary">Deleted
                    Employees
                </a>
            </div>
        </div>

    </div>

    <div class="table-responsive">

        <table class="display nowrap" id="data-list" style="width: 100%">
            <thead>
                <tr>
                    <th></th>
                    <th>No.</th>
                    <th>Payroll No.</th>
                    <th>Employee Name</th>
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Create/Update Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="create_form" class="form-horizontal">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Add Employee Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <div class="form-group">
                        <label>Payroll : </label>
                        <input type="number" name="payroll_no" id="payroll_no" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Employee Name : </label>
                        <input type="text" name="employee_name" id="employee_name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Department : </label>
                        <input type="text" name="department" id="department" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Designation : </label>
                        <input type="text" name="designation" id="designation" class="form-control" />
                    </div>
                    <input type="hidden" name="action" id="action" value="Add" />
                    <input type="hidden" name="hidden_id" id="hidden_id" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" name="action_button" id="action_button" value="Add" class="btn btn-info" />
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
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete selected data?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="ok_button" name="ok_button">Yes Please</button>
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
                "lengthMenu" : [ [25, 50, 75, 100, -1], [25, 50, 75, 100, "All"] ],
                responsive: true,
                ajax: { 
                    "type" : "POST",
                    "url" : "{!! route('admin.employee.list') !!}",
                    "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "data" : function ( d ){
                        d.payroll_no1 = document.getElementById( 'payroll_no1' ).value;
                        d.employee_name1 = document.getElementById( 'employee_name1' ).value;
                        d.department1 = document.getElementById( 'department1' ).value;
                        d.designation1 = document.getElementById( 'designation1' ).value;
                    },
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
                    {data: 'payroll_no', name: 'payroll_no', orderable: true, searchable: true},
                    {data: 'employee_name', name: 'employee_name', orderable: true, searchable: true},
                    {data: 'department', name: 'department', orderable: true, searchable: true},
                    {data: 'designation', name: 'designation', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false},                    
               ],               
            });           
        });

        $(document).on('click', '#create_employee', function(){
            $('.modal-title').text('Add New Employee');
            $('#action_button').val('Add');
            $('#action').val('Add');
            $('#form_result').html('');

            $('#createModal').modal('show');
        });

        $('#create_form').on('submit', function(event){
            event.preventDefault();
            var save_url = '';

            if($('#action').val() == 'Add'){
                save_url = "{{ route('admin.employee.store') }}";
            }

            if($('#action').val() == 'Edit'){
                save_url = "{{ route('admin.employee.update') }}";
            }

            $.ajax({
                type: 'post',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: save_url,
                data:$(this).serialize(),
                dataType: 'json',
                success: function(data){
                    var html = '';
                    if(data.errors){
                        html = '<div class="alert alert-danger">';
                        for(var count = 0; count < data.errors.length; count++){
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div';
                        $('#form_result').html(html);
                    }
                    if(data.success){
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#create_form')[0].reset();
                        $('#data-list').DataTable().ajax.reload();
                        $('#form_result').html(html);
                        setTimeout(function(){                
                            $('#form_result').html('');
                        }, 2000);
                    }
                    if(data.update_success){
                        html = '<div class="alert alert-success">' + data.update_success + '</div>';                        
                        $('#data-list').DataTable().ajax.reload();
                        $('#form_result').html(html);                         
                    }                                   
                },
                error: function(data){
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        $('#createModal').on('hidden.bs.modal', function(){
            $('#create_form')[0].reset();
        });

        $(document).on('click', '.edit', function(event){
            event.preventDefault();
            var id = $(this).attr('id');
            $('#form_result').html('');

            $.ajax({
                url: "/admin/employee/"+id+"/edit",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType:"json",
                success:function(data){
                    console.log('success: '+data);
                    $('#payroll_no').val(data.result.payroll_no);
                    $('#employee_name').val(data.result.employee_name);
                    $('#department').val(data.result.department);
                    $('#designation').val(data.result.designation);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Record');
                    $('#action_button').val('Update');
                    $('#action').val('Edit');
                    $('#createModal').modal('show');
                },
                error: function(data){
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });        

        $(document).on('click', '#bulk_delete', function(){               

            var id = [];
            $('.employees_checkbox:checked').each(function(){
                $('.modal-title').text('Delete Selected Employees');
                id.push($(this).val());
                $('#confirmModal').modal('show');
            });
            if(id.length > 0){
                $('#ok_button').click(function(){
                    $.ajax({
                        url: "{{ route('admin.delete.employees') }}",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        method: "get",
                        data:{id:id},
                        beforeSend:function(){
                            $('#ok_button').text('Deleting...');
                        },
                        success: function(data){
                            var html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#delete_result').html(html);
                            setTimeout(function(){
                                $('#data-list').DataTable().ajax.reload();                        
                                $('#ok_button').text('Yes Please');
                                $('#confirmModal').modal('hide');
                                $('#delete_result').html('');
                            }, 2000);
                        },
                        error: function(data){
                            var errors = data.responseJSON;
                            var html = '<div class="alert alert-danger">' + data.errors + '</div>';
                            $('#delete_result').html(html);
                        }
                    });
                }); 
            }else{
                alert("Please select at least one record");
            }
        });

        $('#confirmModal').on('hidden.bs.modal', function(){
            $('#ok_button').text('Yes Please');
        });

        var employee_id;

        $(document).on('click', '.delete', function(){
            $('.modal-title').text('Delete Employee');
            employee_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function(){
            $.ajax({
                url:"/admin/employee/destroy/"+employee_id,
                beforeSend:function(){
                    $('#ok_button').text('Deleting...');
                },
                success:function(data){
                    var html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#delete_result').html(html);
                    setTimeout(function(){
                        $('#data-list').DataTable().ajax.reload();                        
                        $('#ok_button').text('Yes Please');
                        $('#confirmModal').modal('hide');
                        $('#delete_result').html('');
                    }, 2000);                    
                }                
            });
        });     
        
        function reload_table(){
            $('#data-list').DataTable().ajax.reload();
        }        
</script>
<script></script>
@endpush