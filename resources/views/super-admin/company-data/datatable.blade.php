@extends('layouts.master-datatable')

@section('content')
<div class="mt-2">
    <h4>Employee Register Management</h4>
    <div class="card-body">
        <div class="col-md-12">
            <div class="form-row inline-flex space-x-8">
                <div class="form-group col-md-4">
                    <label for="">Date</label>
                    <input type="date" id="date" name="date" max="<?= date('Y-m-d'); ?>">
                </div>
                <div>
                    <button type="button" name="present" id="present" class="btn btn-outline-primary">Mark Present</button>
                </div>
                <div><button type="button" name="bulk_delete" id="bulk_delete" class="btn btn-outline-danger btn-sm">Delete
                    Checked</button></div>
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
                    <th>Daily Rate</th>
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
                        <label>Staff No. : </label>
                        <input type="text" name="staff_no" id="staff_no" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Employee Name : </label>
                        <input type="text" name="employee_name" id="employee_name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Company : </label>
                        <select id="company_id" name="company_id" class="form-control">
                            <option value="" selected>Select Company...</option>
                            @foreach($companies as $company)
                            <option id="company_id" value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department : </label>
                        <select id="department_id" name="department_id" class="form-control">
                            <option value="" selected>Select Department...</option>
                            @foreach($departments as $department)
                            <option id="department_id" value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rate : </label>
                        <select id="rate_id" name="rate_id" class="form-control">
                            <option value="" selected>Select Rate...</option>
                            @foreach($rates as $rate)
                            <option id="rate_id" value="{{ $rate->id }}">{{ $rate->rate }}</option>
                            @endforeach
                        </select>
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

<!-- Present Modal -->
<div class="modal fade" id="presentModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="present_form" class="form-horizontal">
                <span id="present_result"></span>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin: 0;">Are you sure you want to mark selected employee(s) present on
                        selected date?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="present_button" name="present_button">Yes</button>
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
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete selected employee(s) data?
                    </h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="ok_button" name="ok_button">Yes</button>
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
                    "url" : "{!! route('super-admin.company.data.list') !!}",
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
                    {data: 'rate', name: 'rate', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false},                    
               ],               
            });           
        });

        // $(document).on('click', '#create_employee', function(){
        //     $('.modal-title').text('Add New Employee');
        //     $('#action_button').val('Add');
        //     $('#action').val('Add');
        //     $('#form_result').html('');

        //     $('#createModal').modal('show');
        // });

        $('#create_form').on('submit', function(event){
            event.preventDefault();
            var save_url = '';

            // if($('#action').val() == 'Add'){
            //     save_url = "{{ route('super-admin.employee.store') }}";
            // }

            if($('#action').val() == 'Edit'){
                save_url = "{{ route('super-admin.employee.update') }}";
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
                        setTimeout(function(){
                            $('#createModal').modal('hide');
                        }, 2000);                        
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
                url: "/super-admin/employee/"+id+"/edit",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType:"json",
                success:function(data){
                    $('#staff_no').val(data.result.staff_no);
                    $('#employee_name').val(data.result.employee_name);
                    $('#company_id').val(data.result.company_id);
                    $('#department_id').val(data.result.department_id);
                    $('#rate_id').val(data.result.rate_id);
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
            $('.edit').unbind('click');
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

        $(document).on('click', '#present', function(event){
            event.preventDefault();   
            var id = [];
            $('.employees_checkbox:checked').each(function(){
                $('.modal-title').text('Mark Selected Employees Present');
                id.push($(this).val());                
            });
            if(id.length > 0){
                var date = '';
                date = $('#date').val();
                if(date != ''){
                    console.log(id);
                    $('#presentModal').modal('show');
                    $('#present_button').click(function(){
                        $.ajax({
                            url: "{{ route('super-admin.mark.employees.present') }}",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            // method: "get",
                            data:{id:id, date:date},
                            beforeSend:function(){
                                $('#present_button').text('Processing...');
                            },
                            success: function(data){
                                var html = '';
                                if(data.success){
                                    html = '<div class="alert alert-success">' + data.success + '</div>';
                                    $('#present_result').html(html);
                                    $('#data-list').DataTable().ajax.reload();
                                    setTimeout(function(){
                                        $('#select_all').prop('checked', false);                       
                                        $('#present_button').text('Yes');
                                        $('#presentModal').modal('hide');
                                        $('#present_result').html('');
                                        $('#date').val('');
                                        date = '';
                                        id = [];
                                    }, 2000); 
                                } 
                                if(data.marking_error){
                                    html = '<div class="alert alert-warning">' + data.marking_error + '</div>';
                                    $('#present_result').html(html);
                                    $('#data-list').DataTable().ajax.reload();
                                    setTimeout(function(){
                                        $('#select_all').prop('checked', false);
                                        $('#present_result').html('');
                                        $('#date').val('');
                                        date = '';
                                        id = [];
                                    }, 2000); 
                                }                               
                            },
                            error: function(data){
                                var errors = data.responseJSON;
                                var html = '<div class="alert alert-danger">' + data.errors + '</div>';
                                $('#delete_result').html(html);
                            }
                        });
                        $('#present_button').unbind('click');
                    }); 
                }else{
                    alert("Please select a date");
                }              
            }else{
                alert("Please select at least one record");
            }
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
                        url: "{{ route('super-admin.delete.employees') }}",
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
                                $('#select_all').prop('checked', false);
                                $('#data-list').DataTable().ajax.reload();                        
                                $('#ok_button').text('Yes');
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
                    $('#ok_button').unbind('click');
                }); 
            }else{
                alert("Please select at least one record");
            }
        });

        $('#confirmModal').on('hidden.bs.modal', function(){
            $('#ok_button').text('Yes');
        });

        $('#presentModal').on('hidden.bs.modal', function(){
            $('#present_button').text('Yes');
        });

        var employee_id;

        $(document).on('click', '.delete', function(){
            $('.modal-title').text('Delete Employee');
            employee_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function(event){
            event.preventDefault();
            $.ajax({
                url:"/super-admin/employee/destroy/"+employee_id,
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
            $('#ok_button').unbind('click');
        });     
      
</script>
<script></script>
@endpush