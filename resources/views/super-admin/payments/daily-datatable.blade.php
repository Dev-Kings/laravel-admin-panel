@extends('layouts.master-datatable')

@section('content')
<div class="mt-2">
    <h4>Daily Payments Data</h4>
    <div class="card-body">
        <div class="col-md-12">
            <div class="form-row inline-flex space-x-4">
                <div class="form-group col-md-2">
                    <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>"
                        max="<?= date('Y-m-d'); ?>">
                </div>
                <div>
                    <button type="button" name="date_present" id="date_present" onclick="reload_table()"
                        class="btn btn-outline-primary">Filter</button>
                </div>
                <div><a href="{{ route('super-admin.payments.index') }}" class="btn btn-outline-info">View Monthly
                        Data</a></div>
                <div><button type="button" name="mark_absent" id="mark_absent"
                        class="btn btn-outline-danger btn-sm">Mark
                        Absent</button></div>
            </div>
            <hr>
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
                    <th>Days Present</th>
                    <th>Daily Rate</th>
                    <th>Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align: right">Total:</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="sample_form" class="form-horizontal">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <span id="absent_response"></span>
                <div class="modal-body">
                    <h4 align="center" style="margin: 0;">Are you sure you want to mark selected employee(s) absent on
                        date shown?</h4>
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
                    "url" : "{!! route('super-admin.daily.data') !!}",
                    "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "data" : function ( d ){
                        d.date_request = document.getElementById( 'date' ).value;
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
                    {data: 'staff_no', name: 'staff_no', orderable: true, searchable: true},
                    {data: 'employee_name', name: 'employee_name', orderable: true, searchable: true},
                    {data: 'days_present', name: 'days_present', orderable: true, searchable: true},
                    {data: 'rate', name: 'rate', orderable: true, searchable: true},
                    {data: 'total', name: 'total', orderable: true, searchable: true},
                    {data: 'date', name: 'date', orderable: true, searchable: true},                   
               ],
               footerCallback: function (  row, data, start, end, display ) {
                    var api = this.api();

                    var intVal = function (i) {
                        return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                    };

                    total = api
                        .column(6)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    pageTotal = api
                        .column(6, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                    $(api.column(6).footer()).html(
                        'Kshs.' + pageTotal
                    );
               },               
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

        $(document).on('click', '#mark_absent', function(){               

            var id = [];
            $('.employees_checkbox:checked').each(function(){
                $('.modal-title').text('Mark Selected Employees Absent');
                id.push($(this).val());
                $('#confirmModal').modal('show');
            });
            if(id.length > 0){
                var date = '';
                date = $('#date').val();
                $('#ok_button').click(function(){
                    console.log(id);
                    console.log(date);
                    $.ajax({
                        url: "{{ route('super-admin.mark.employees.absent') }}",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        method: "get",
                        data:{id:id, date:date},
                        beforeSend:function(){
                            $('#ok_button').text('Marking Absent...');
                        },
                        success: function(data){
                            var html = '';
                            if(data.success){
                                html = '<div class="alert alert-success">' + data.success + '</div>';
                                $('#absent_response').html(html);
                                setTimeout(function(){
                                    $('#select_all').prop('checked', false);
                                    $('#data-list').DataTable().ajax.reload();                        
                                    $('#ok_button').text('Yes');
                                    $('#confirmModal').modal('hide');
                                    $('#absent_response').html('');
                                }, 2000);
                            }
                        },
                        error: function(data){
                            var errors = data.responseJSON;
                            var html = '<div class="alert alert-danger">' + data.errors + '</div>';
                            $('#absent_response').html(html);
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
            $('#absent_response').html('');
        });
        
        function reload_table(){
            $('#data-list').DataTable().ajax.reload();
        }
      
</script>
<script></script>
@endpush