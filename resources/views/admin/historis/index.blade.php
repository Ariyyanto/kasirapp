@extends('layouts.app')

@section('title', 'Historis Penjualan')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Historis Penjualan</h5>
                <div>
                    <button class="btn btn-light" id="printBtn">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                    <button class="btn btn-light" id="exportBtn">
                        <i class="bi bi-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="start_date">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="end_date">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary" id="filterBtn">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <button class="btn btn-secondary ms-2" id="resetFilterBtn">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="historisTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jumlah Jual</th>
                            <th>Stok Tersisa</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historis as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ date('d/m/Y', strtotime($item->tanggal_penjualan)) }}</td>
                            <td>{{ $item->produk->nama_produk }}</td>
                            <td>{{ $item->jumlah_jual }}</td>
                            <td>{{ $item->jumlah_stok }}</td>
                            <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                            <td>
                                <button class="btn btn-sm btn-info detail-btn" 
                                    data-id="{{ $item->id }}"
                                    data-tanggal="{{ date('d/m/Y', strtotime($item->tanggal_penjualan)) }}"
                                    data-produk="{{ $item->produk->nama_produk }}"
                                    data-jumlah="{{ $item->jumlah_jual }}"
                                    data-stok="{{ $item->jumlah_stok }}"
                                    data-harga="{{ number_format($item->total_harga, 0, ',', '.') }}"
                                    data-deskripsi="{{ $item->deskripsi ?? '-' }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align:right">Total:</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailModalLabel">Detail Penjualan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Tanggal Penjualan:</strong>
                        <p id="detail-tanggal"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Nama Produk:</strong>
                        <p id="detail-produk"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Jumlah Jual:</strong>
                        <p id="detail-jumlah"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Stok Tersisa:</strong>
                        <p id="detail-stok"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Total Harga:</strong>
                        <p id="detail-harga"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Deskripsi/Keterangan:</strong>
                        <p id="detail-deskripsi"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
<style>
    .dataTables_wrapper .dataTables_info {
        padding-top: 1em !important;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 0.5em !important;
    }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable with buttons and footer callback
        var table = $('#historisTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-excel"></i> Excel',
                    className: 'btn btn-success',
                    title: 'Historis Penjualan',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-pdf"></i> PDF',
                    className: 'btn btn-danger',
                    title: 'Historis Penjualan',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-info',
                    title: 'Historis Penjualan',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5]
                    }
                }
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                
                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[^\d]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                // Total over all pages
                var total = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Update footer
                $(api.column(5).footer()).html(
                    'Rp ' + total.toLocaleString('id-ID')
                );
            }
        });

        // Filter by date range
        $('#filterBtn').click(function() {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            
            if (startDate && endDate) {
                // Convert dates to the format used in the table (dd/mm/yyyy)
                var startParts = startDate.split('-');
                var endParts = endDate.split('-');
                var startFormatted = startParts[2] + '/' + startParts[1] + '/' + startParts[0];
                var endFormatted = endParts[2] + '/' + endParts[1] + '/' + endParts[0];
                
                // Filter the table
                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        var date = data[1]; // Tanggal is in column 1
                        date = date.split('/');
                        date = date[2] + '-' + date[1] + '-' + date[0];
                        
                        if ((startDate === '' && endDate === '') || 
                            (startDate === '' && date <= endDate) || 
                            (startDate <= date && endDate === '') || 
                            (startDate <= date && date <= endDate)) {
                            return true;
                        }
                        return false;
                    }
                );
                table.draw();
                $.fn.dataTable.ext.search.pop();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Harap pilih tanggal awal dan tanggal akhir',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });

        // Reset filter
        $('#resetFilterBtn').click(function() {
            $('#start_date').val('');
            $('#end_date').val('');
            table.search('').columns().search('').draw();
        });

        // Detail button click
        $(document).on('click', '.detail-btn', function() {
            $('#detail-tanggal').text($(this).data('tanggal'));
            $('#detail-produk').text($(this).data('produk'));
            $('#detail-jumlah').text($(this).data('jumlah'));
            $('#detail-stok').text($(this).data('stok'));
            $('#detail-harga').text('Rp ' + $(this).data('harga'));
            $('#detail-deskripsi').text($(this).data('deskripsi'));
            $('#detailModal').modal('show');
        });

        // Print button
        $('#printBtn').click(function() {
            table.button('.buttons-print').trigger();
        });

        // Export button
        $('#exportBtn').click(function() {
            // Show options for export
            Swal.fire({
                title: 'Export Data',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Excel',
                denyButtonText: 'PDF',
                cancelButtonText: 'Batal',
                customClass: {
                    actions: 'my-actions',
                    confirmButton: 'order-2',
                    denyButton: 'order-3',
                    cancelButton: 'order-1',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    table.button('.buttons-excel').trigger();
                } else if (result.isDenied) {
                    table.button('.buttons-pdf').trigger();
                }
            });
        });

        // Success message
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endsection