@extends('layouts.master-datatable')

@section('content')
<div class="mt-2">
    <h4>Users Data</h4>
    <div class="card-body">
        <div class="col-md-12">

            <div class="form-row inline-flex space-x-6">
                <button type="button" name="bulk_delete" id="bulk_delete" class="mb-4 btn btn-danger btn-sm">Delete
                    Selected</button>

                <button type="button" name="create_user" id="create_user" class="mb-4 btn btn-success">Add
                    User
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">

        <table class="display nowrap" id="data-list" style="width: 100%">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select_all" /></th>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Email</th>
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
                    <h5 class="modal-title" id="modalLabel">Add User Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <div class="form-group">
                        <label>First Name : </label>
                        <input type="text" name="firstname" id="firstname" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Last Name : </label>
                        <input type="text" name="lastname" id="lastname" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Email : </label>
                        <input type="email" name="email" id="email" class="form-control" required />
                    </div>
                    <span class="password_prompt"></span>
                    <div class="form-group">
                        <label>Password : </label>
                        <input type="password" name="password" id="password" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Password Confirmation : </label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" />
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
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete the user?</h4>
                </div>
                <div style="text-align: center;">
                    <span class="delete_prompt" style="border:1px solid red;"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="ok_button" name="ok_button">Yes Please</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Multi Delete Modal -->
<div class="modal fade" id="confirmMultiModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="multi_sample_form" class="form-horizontal">
                <span id="multi_delete_result"></span>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>                
                <div class="modal-body">
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete selected user(s)?</h4>
                </div>
                <div style="text-align: center;">
                    <span class="delete_prompt" style="border:1px solid red;"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="multi_delete" name="multi_delete">Yes Please</button>
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
                        title: 'Users Data',
                        sheetName: 'Users',
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
                    "url" : "{!! route('super-admin.all.users') !!}",
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
                    {data: 'username', name: 'username', orderable: true, searchable: true},
                    {data: 'email', name: 'email', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false},                    
               ],               
            });           
        });

        $(document).on('click', '#create_user', function(){
            $('.modal-title').text('Add New User');
            $('#action_button').val('Add');
            $('#action').val('Add');
            $('#form_result').html('');

            $('#createModal').modal('show');
        });

        $('#create_form').on('submit', function(event){
            event.preventDefault();
            var save_url = '';

            if($('#action').val() == 'Add'){
                save_url = "{{ route('super-admin.users.store') }}";
            }

            if($('#action').val() == 'Edit'){
                save_url = "{{ route('super-admin.user.update') }}";
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
                        $('#form_result').html(html);
                        setTimeout(function(){
                            $('#data-list').DataTable().ajax.reload();
                            $('#createModal').modal('hide');
                            $('.modal-title').text('Edit Record');
                            $('#action_button').val('Update');
                            $('.password_prompt').text('');
                        }, 1500);                                               
                    }                                   
                },
                error: function(data){
                    var errors = data.responseJSON;
                    alert(errors);
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
                url: "/super-admin/user/"+id+"/edit",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType:"json",
                success:function(data){
                    $('#firstname').val(data.result.firstname);
                    $('#lastname').val(data.result.lastname);
                    $('#email').val(data.result.email);
                    $('.password_prompt').text(`Kindly skip password fields.
                     Password reset for individual account can only be done by individual users. 
                     In case they have forgoten their password, they should click on \'Forgot Password\' on login page.`);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Edit Record');
                    $('#action_button').val('Update');
                    $('#action').val('Edit');
                    $('#createModal').modal('show');
                },
                error: function(data){
                    var errors = data.responseJSON;
                    alert(errors);
                }
            });
        });
        
        $(document).ready(function(){
            $('#select_all').change(function(){
                $('.users_checkbox').prop('checked', this.checked);
            });

            $('.users_checkbox').change(function(){
                if($('.users_checkbox:checked').length == $('.users_checkbox').length){
                    $('#select_all').prop('checked', true);
                }else{
                    $('#select_all').prop('checked', false);
                }
            });
        });

        $(document).on('click', '#bulk_delete', function(){               

            var id = [];
            $('.users_checkbox:checked').each(function(){
                $('.modal-title').text('Delete Selected Users');
                id.push($(this).val());
                $('#confirmMultiModal').modal('show');
                $('.delete_prompt').text(`Please note that this action is irreversible.
                The users and all their associated data will be permanently deleted.`);
            });
            if(id.length > 0){
                $('#multi_delete').click(function(){
                    $.ajax({
                        url: "{{ route('super-admin.delete.users') }}",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        method: "get",
                        data:{id:id},
                        beforeSend:function(){
                            $('#multi_delete').text('Deleting...');
                        },
                        success: function(data){
                            var html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#multi_delete_result').html(html);
                            $('#multi_delete').text('Yes Please');
                            setTimeout(function(){
                                $('#select_all').prop('checked', false);
                                $('#data-list').DataTable().ajax.reload();                  
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

        $('#confirmMultiModal').on('hidden.bs.modal', function(){
            $('#multi_delete').text('Yes Please');
            $('#multi_delete_result').html('');
        });

        $('#confirmModal').on('hidden.bs.modal', function(){
            $('#ok_button').text('Yes Please');
            $('#delete_result').html('');
        });

        var user_id;

        $(document).on('click', '.delete', function(){
            $('.modal-title').text('Delete User');
            user_id = $(this).attr('id');
            $('#confirmModal').modal('show');
            $('.delete_prompt').text(`Please note that this action is irreversible.
            The user and all their associated data will be permanently deleted.`);
        });

        $('#ok_button').click(function(){
            $.ajax({
                url:"/super-admin/user/destroy/"+user_id,
                beforeSend:function(){
                    $('#ok_button').text('Deleting...');
                },
                success:function(data){
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
                        $('#delete_result').html(html);
                        $('#data-list').DataTable().ajax.reload();
                        $('#ok_button').text('Yes Please');
                        setTimeout(function(){                                         
                            $('#confirmModal').modal('hide');
                            $('#delete_result').html('');
                        }, 1500); 
                    }                   
                    if(data.deletion_error){
                        html = '<div class="alert alert-error">' + data.deletion_error + '</div>';
                        $('#delete_result').html(html);
                        $('#data-list').DataTable().ajax.reload();
                        $('#ok_button').text('Yes Please');
                        setTimeout(function(){
                            $('#delete_result').html('');
                        }, 1500);
                    }               
                },
                error: function(data){
                    var errors = data.responseJSON;
                    var html = '<div class="alert alert-danger">' + data.errors + '</div>';
                    $('#delete_result').html(html);
                    $('#data-list').DataTable().ajax.reload();
                    $('#ok_button').text('Yes Please');
                    setTimeout(function(){
                        $('#delete_result').html('');
                    }, 1500);
                }               
            });
        });     
              
</script>
<script></script>
@endpush