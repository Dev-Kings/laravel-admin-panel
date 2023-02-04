@extends('layouts.master-datatable')

@section('content')
<div class="mt-2">
    <h4>Monthly Payments Data</h4>
    <div class="card-body">
        <div class="col-md-12">
            <div class="form-row inline-flex space-x-8">
                <div class="form-group col-md-2">
                    <label for="">Year</label>
                    <select id="year">
                        @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <select id="month">
                        @foreach(Carbon\CarbonPeriod::create(now()->startOfMonth(), '1 month',
                        now()->addMonths(11)->startOfMonth()) as $date)
                        <option value="{{ $date->format('m') }}">
                            {{ $date->format('F') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="button" name="date_present" id="date_present" onclick="reload_table()"
                        class="btn btn-outline-primary">Filter</button>
                </div>
                <div>
                    <a href="{{ route('super-admin.daily.payments.data') }}" class="btn btn-info">View Daily Data</a>
                </div>
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
                        title: 'Monthly Payments Data',
                        sheetName: 'Monthly Payments',
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
                    "url" : "{!! route('super-admin.monthly.data') !!}",
                    "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "data" : function ( d ){
                        d.year_request = document.getElementById( 'year' ).value;
                        d.month_request = document.getElementById( 'month' ).value;
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
       
        function reload_table(){
            $('#data-list').DataTable().ajax.reload();
        }
      
</script>
<script></script>
@endpush