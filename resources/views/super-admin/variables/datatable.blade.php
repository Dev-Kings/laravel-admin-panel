@extends('layouts.master-datatable')

@section('content')
<div class="mt-2">
    <h4>Variables</h4>
    <div class="card-body">
        <div class="col-md-12">
            <div class="form-row inline-flex space-x-6">
                <button type="button" name="create_variable" id="create_variable" class="mb-4 btn btn-success">Add
                    Variable
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">

        <table class="display nowrap" id="data-list" style="width: 100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Variable Name</th>
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
                    <h5 class="modal-title" id="modalLabel">Add Variable</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <div class="form-group">
                        <label>Variable Name : </label>
                        <input type="text" name="name" id="name" class="form-control" />
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
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete selected variable?</h4>
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
                        title: 'Variables Data',
                        sheetName: 'Variables',
                    },
                    {
                        extend: 'colvis',
                        columnText: function ( dt, idx, title ) {
                            return (idx+1)+': '+title;
                        }
                    },
                ],
                "lengthMenu" : [ [10, 15, -1], [10, 15, "All"] ],
                responsive: true,
                ajax: { 
                    "type" : "POST",
                    "url" : "{!! route('super-admin.variables.list') !!}",
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
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false},
                    {data: 'name', name: 'name', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false},                    
               ],               
            });           
        });

        $(document).on('click', '#create_variable', function(){
            $('.modal-title').text('Add New Variable');
            $('#action_button').val('Add');
            $('#action').val('Add');
            $('#form_result').html('');

            $('#createModal').modal('show');
        });

        $('#create_form').on('submit', function(event){
            event.preventDefault();
            var save_url = '';

            if($('#action').val() == 'Add'){
                save_url = "{{ route('super-admin.variable.store') }}";
            }

            if($('#action').val() == 'Edit'){
                save_url = "{{ route('super-admin.variable.update') }}";
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
                url: "/super-admin/variable/"+id+"/edit",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType:"json",
                success:function(data){
                    $('#name').val(data.result.name);
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

        $('#confirmModal').on('hidden.bs.modal', function(){
            $('#ok_button').text('Yes Please');
        });

        var variable_id;

        $(document).on('click', '.delete', function(){
            $('.modal-title').text('Delete Variable');
            variable_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function(){
            $.ajax({
                url:"/super-admin/variable/destroy/"+variable_id,
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