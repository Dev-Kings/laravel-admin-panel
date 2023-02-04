@extends('layouts.master-datatable')

@section('content')
<div class="mt-2">
    <h4>Rates/Formula Data</h4>
    <div class="card-body">
        <div class="col-md-12">
            <div class="form-row inline-flex space-x-6">
                <button type="button" name="create_rate" id="create_rate" class="mb-4 btn btn-success">Add
                    Rate
                </button>
                <button type="button" name="bulk_delete" id="bulk_delete" class="mb-4 btn btn-danger btn-sm">Delete
                    Checked</button>
            </div>
            
            <div class="text-right">
                <form action="{{ route('super-admin.rates.store') }}" enctype="multipart/form-data" method="POST">
                    @csrf
                    <x-bladewind.filepicker name="rate_import_file" placeholder="Upload rates data" max_file_size="1"
                        accepted_file_types=".csv" />
                    <button class="px-4 py-2 bg-blue-300 hover:bg-green-700 rounded-md" type="submit">Import
                        Rates</button>
                </form>
            </div>
        </div>
    </div>

    @if(Session::has('import-success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ Session::get('import-success') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="table-responsive">

        <table class="display nowrap" id="data-list" style="width: 100%">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select_all" /></th>
                    <th>No.</th>
                    <th>Rate</th>
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
                    <h5 class="modal-title" id="modalLabel">Add Rate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <div class="form-group">
                        <label>Rate Kshs.(000.00) : </label>
                        <input type="number" step="0.01" name="rate" id="rate" class="form-control" />
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
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete selected rate?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="ok_button" name="ok_button">Yes Please</button>
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
                    <h3 align="center"><strong>Delete</strong></h3>
                    <h4 align="center" style="margin: 0;">Are you sure you want to delete selected rates?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="multi_delete" name="multi_delete">Yes</button>
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
                        title: 'Departments Data',
                        sheetName: 'Departments',
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
                    "url" : "{!! route('super-admin.rates.list') !!}",
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
                    {data: 'rate', name: 'rate', orderable: true, searchable: true},
                    {data: 'action', name: 'action', orderable: false, searchable: false},                    
               ],               
            });           
        });

        $(document).on('click', '#create_rate', function(){
            $('.modal-title').text('Add New Rate');
            $('#action_button').val('Add');
            $('#action').val('Add');
            $('#form_result').html('');

            $('#createModal').modal('show');
        });

        $('#create_form').on('submit', function(event){
            event.preventDefault();
            var save_url = '';

            if($('#action').val() == 'Add'){
                save_url = "{{ route('super-admin.rate.store') }}";
            }

            if($('#action').val() == 'Edit'){
                save_url = "{{ route('super-admin.rate.update') }}";
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
                    if(data.range_error){
                        html = '<div class="alert alert-warning">' + data.range_error + '</div>';
                        $('#create_form')[0].reset();
                        $('#data-list').DataTable().ajax.reload();
                        $('#form_result').html(html);
                        setTimeout(function(){                
                            $('#form_result').html('');
                        }, 2000);
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
                url: "/super-admin/rate/"+id+"/edit",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                dataType:"json",
                success:function(data){
                    $('#rate').val(data.result.rate);
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

        $('#confirmModal').on('hidden.bs.modal', function(){
            $('#ok_button').text('Yes Please');
        });

        var rate_id;

        $(document).on('click', '.delete', function(){
            $('.modal-title').text('Delete Rate');
            rate_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function(){
            $.ajax({
                url:"/super-admin/rate/destroy/"+rate_id,
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
        
        $(document).ready(function(){
            $('#select_all').change(function(){
                $('.rates_checkbox').prop('checked', this.checked);
            });

            $('.rates_checkbox').change(function(){
                if($('.rates_checkbox:checked').length == $('.rates_checkbox').length){
                    $('#select_all').prop('checked', true);
                }else{
                    $('#select_all').prop('checked', false);
                }
            });
        });

        $(document).on('click', '#bulk_delete', function(){               

            var id = [];
            $('.rates_checkbox:checked').each(function(){
                $('.modal-title').text('Delete Selected Rates');
                id.push($(this).val());
                $('#confirmMultiModal').modal('show');
            });
            if(id.length > 0){
                $('#multi_delete').click(function(){
                    $.ajax({
                        url: "{{ route('super-admin.delete.rates') }}",
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
                    $('#multi_delete').unbind('click');
                });
            }else{
                $('#confirmMultiModal').modal('hide');
                alert("Please select at least one record");
            }

        });

        $('#confirmMultiModal').on('hidden.bs.modal', function(){
            $('#multi_delete').text('Yes');
            $('#multi_delete_result').html('');
        });
                
        function reload_table(){
            $('#data-list').DataTable().ajax.reload();
        }        
</script>
<script></script>
@endpush