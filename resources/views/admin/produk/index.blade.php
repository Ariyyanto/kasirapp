@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    * {
        font-family: 'Inter', sans-serif;
    }

    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary: #64748b;
        --accent: #06b6d4;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --dark: #0f172a;
        --darker: #020617;
        --light: #f8fafc;
        --gray: #475569;
        --gray-light: #e2e8f0;
        --glass: rgba(255, 255, 255, 0.08);
        --glass-border: rgba(255, 255, 255, 0.12);
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --border-radius: 12px;
        --border-radius-sm: 8px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: linear-gradient(135deg, var(--dark) 0%, var(--darker) 100%);
        min-height: 100vh;
        color: white;
        position: relative;
    }

    /* Subtle animated background */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 25% 25%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);
        animation: pulse 8s ease-in-out infinite alternate;
        pointer-events: none;
        z-index: -1;
    }

    @keyframes pulse {
        0% { opacity: 0.5; }
        100% { opacity: 0.8; }
    }

    .glass-card {
        background: var(--glass);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        transition: var(--transition);
    }

    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-header {
        background: rgba(37, 99, 235, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1.5rem;
    }

    .card-header h5 {
        color: white;
        font-weight: 600;
        font-size: 1.25rem;
        margin: 0;
    }

    .btn {
        border-radius: var(--border-radius-sm);
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.75rem 1.5rem;
        transition: var(--transition);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        color: white;
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        color: white;
    }

    .btn-danger, .btn-outline-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover, .btn-outline-danger:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        color: white;
        border-color: transparent;
    }

    .btn-outline-primary {
        background: rgba(37, 99, 235, 0.1);
        border: 1px solid var(--primary);
        color: var(--primary);
    }

    .btn-outline-primary:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }

    .table {
        background: transparent;
        color: white;
    }

    .table th {
        background: rgba(37, 99, 235, 0.2);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem;
        position: relative;
    }

    .table th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: rgba(255, 255, 255, 0.2);
    }

    .table td {
        background: rgba(15, 23, 42, 0.4);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1rem;
        vertical-align: middle;
        transition: var(--transition);
    }

    .table tbody tr {
        transition: var(--transition);
    }

    .table tbody tr:hover {
        background: rgba(37, 99, 235, 0.1);
        transform: translateX(4px);
    }

    .table tbody tr:hover td {
        background: transparent;
    }

    .stock-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 8px;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .stock-high { background: var(--success); }
    .stock-medium { background: var(--warning); }
    .stock-low { background: var(--danger); }

    .product-info h6 {
        color: white;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .product-description {
        color: var(--gray-light);
        font-size: 0.8rem;
        opacity: 0.8;
    }

    .price-display {
        color: var(--accent);
        font-weight: 600;
        font-size: 1rem;
    }

    .stock-display {
        color: var(--gray-light);
        font-weight: 500;
    }

    .modal-content {
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(30px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius);
        color: white;
    }

    .modal-header {
        background: rgba(37, 99, 235, 0.1);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-close {
        filter: invert(1);
    }

    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius-sm);
        color: white;
        padding: 0.75rem 1rem;
        transition: var(--transition);
    }

    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.1);
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        color: white;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .form-label {
        color: var(--gray-light);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }

    /* DataTables styling */
    .dataTables_wrapper .dataTables_filter input {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 0.5rem 1rem;
        color: white;
        transition: var(--transition);
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--primary);
        outline: none;
    }

    .dataTables_wrapper .dataTables_filter input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length label {
        color: var(--gray-light);
    }

    .dataTables_wrapper .dataTables_length select {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius-sm);
        color: white;
        padding: 0.25rem 0.5rem;
    }

    .page-link {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        margin: 0 2px;
        border-radius: var(--border-radius-sm);
        transition: var(--transition);
    }

    .page-link:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
        transform: translateY(-1px);
    }

    .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
        color: white;
    }

    .text-danger {
        color: var(--danger) !important;
    }

    /* Loading state */
    .btn-loading {
        position: relative;
        color: transparent !important;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            gap: 1rem;
        }
        
        .btn-group {
            width: 100%;
        }
        
        .btn {
            flex: 1;
        }
        
        .table th,
        .table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding: 1rem;
        }
        
        .action-btns .btn {
            padding: 0.5rem;
            margin: 0 0.1rem;
        }
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4 glass-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">
                <i class="bi bi-box-seam me-2"></i>Manajemen Produk
            </h5>
            <div class="d-flex flex-wrap gap-2">
                <div class="btn-group">
                    <button class="btn btn-success btn-sm" id="exportExcel">
                        <i class="bi bi-file-excel"></i>
                        Excel
                    </button>
                    <button class="btn btn-danger btn-sm" id="exportPdf">
                        <i class="bi bi-file-pdf"></i>
                        PDF
                    </button>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#produkModal">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Produk
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="produkTable">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk as $item)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="product-info">
                                    <h6 class="mb-0">{{ $item->nama_produk }}</h6>
                                    @if($item->deskripsi)
                                    <div class="product-description">{{ Str::limit($item->deskripsi, 40) }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="price-display">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="stock-indicator 
                                        @if($item->jumlah_stok > 20) stock-high 
                                        @elseif($item->jumlah_stok > 5) stock-medium 
                                        @else stock-low @endif"></span>
                                    <span class="stock-display">{{ $item->jumlah_stok }} unit</span>
                                </div>
                            </td>
                            <td class="action-btns">
                                <button class="btn btn-sm btn-outline-primary edit-btn" 
                                    data-id="{{ $item->id }}" 
                                    data-nama="{{ $item->nama_produk }}" 
                                    data-harga="{{ $item->harga }}"
                                    data-stok="{{ $item->jumlah_stok }}"
                                    data-deskripsi="{{ $item->deskripsi ?? '' }}"
                                    title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" 
                                    data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama_produk }}"
                                    title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Produk -->
<div class="modal fade" id="produkModal" tabindex="-1" aria-labelledby="produkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="produkForm">
                @csrf
                <input type="hidden" id="produk_id" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="produkModalLabel">
                        <i class="bi bi-box-seam me-2"></i>Tambah Produk
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_produk" name="nama_produk" required placeholder="Masukkan nama produk">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="harga" class="form-label">Harga <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga" name="harga" required min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_stok" class="form-label">Stok <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="jumlah_stok" name="jumlah_stok" required min="0" placeholder="0">
                                <span class="input-group-text">unit</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi produk (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-check"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#produkTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-excel"></i> Excel',
                className: 'btn btn-success d-none',
                filename: 'Daftar_Produk_' + new Date().toISOString().slice(0,10),
                title: 'Daftar Produk',
                exportOptions: {
                    columns: [1, 2, 3]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-pdf"></i> PDF',
                className: 'btn btn-danger d-none',
                filename: 'Daftar_Produk_' + new Date().toISOString().slice(0,10),
                title: 'Daftar Produk',
                exportOptions: {
                    columns: [1, 2, 3]
                }
            }
        ],
        language: {
            "sProcessing": "Memproses...",
            "sLengthMenu": "Tampilkan _MENU_ data",
            "sZeroRecords": "Tidak ada data",
            "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
            "sInfoFiltered": "(disaring dari _MAX_ total data)",
            "sSearch": "Cari:",
            "oPaginate": {
                "sFirst": "Pertama",
                "sPrevious": "Sebelumnya",
                "sNext": "Selanjutnya",
                "sLast": "Terakhir"
            }
        },
        pageLength: 10,
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [4] }
        ]
    });

    // Export buttons
    $('#exportExcel').click(function() {
        showLoading(this);
        table.button('.buttons-excel').trigger();
        setTimeout(() => hideLoading(this, '<i class="bi bi-file-excel"></i> Excel'), 1000);
    });

    $('#exportPdf').click(function() {
        showLoading(this);
        table.button('.buttons-pdf').trigger();
        setTimeout(() => hideLoading(this, '<i class="bi bi-file-pdf"></i> PDF'), 1000);
    });

    // Form submit
    $('#produkForm').submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $('#submitBtn');
        const isEdit = $('#produk_id').val() !== '';
        const formData = $(this).serialize();
        
        showLoading(submitBtn[0]);
        
        $.ajax({
            url: isEdit ? '/admin/produk/' + $('#produk_id').val() : '/admin/produk',
            method: isEdit ? 'PUT' : 'POST',
            data: formData,
            success: function(response) {
                hideLoading(submitBtn[0], '<i class="bi bi-check"></i> Simpan');
                
                if(response.success) {
                    $('#produkModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                        background: 'rgba(15, 23, 42, 0.95)',
                        color: 'white'
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                hideLoading(submitBtn[0], '<i class="bi bi-check"></i> Simpan');
                
                let errorMessage = 'Terjadi kesalahan';
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMessage,
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: 'white'
                });
            }
        });
    });

    // Edit button
    $(document).on('click', '.edit-btn', function() {
        const data = $(this).data();
        
        $('#produk_id').val(data.id);
        $('#nama_produk').val(data.nama);
        $('#harga').val(data.harga);
        $('#jumlah_stok').val(data.stok);
        $('#deskripsi').val(data.deskripsi);
        
        $('#produkModalLabel').html('<i class="bi bi-pencil me-2"></i>Edit Produk');
        $('#produkModal').modal('show');
    });

    // Delete button
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        
        Swal.fire({
            title: 'Konfirmasi',
            text: `Hapus produk "${nama}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            background: 'rgba(15, 23, 42, 0.95)',
            color: 'white'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/produk/' + id,
                    method: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Produk berhasil dihapus',
                                showConfirmButton: false,
                                timer: 1500,
                                background: 'rgba(15, 23, 42, 0.95)',
                                color: 'white'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menghapus produk',
                            background: 'rgba(15, 23, 42, 0.95)',
                            color: 'white'
                        });
                    }
                });
            }
        });
    });

    // Reset modal when hidden
    $('#produkModal').on('hidden.bs.modal', function() {
        $('#produkForm')[0].reset();
        $('#produk_id').val('');
        $('#produkModalLabel').html('<i class="bi bi-box-seam me-2"></i>Tambah Produk');
        
        // Remove any validation classes
        $('.form-control').removeClass('is-invalid is-valid');
        $('.invalid-feedback').remove();
    });

    // Number formatting for price input
    $('#harga').on('input', function() {
        let value = $(this).val().replace(/[^\d]/g, '');
        if (value) {
            $(this).val(value);
        }
    });

    // Loading state functions
    function showLoading(button) {
        const $btn = $(button);
        $btn.addClass('btn-loading').prop('disabled', true);
    }

    function hideLoading(button, originalContent) {
        const $btn = $(button);
        $btn.removeClass('btn-loading').prop('disabled', false).html(originalContent);
    }

    // Format currency display
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    // Auto-save draft functionality (optional)
    let draftTimer;
    $('#produkForm input, #produkForm textarea').on('input', function() {
        clearTimeout(draftTimer);
        draftTimer = setTimeout(function() {
            // Save to localStorage as draft
            const formData = {
                nama_produk: $('#nama_produk').val(),
                harga: $('#harga').val(),
                jumlah_stok: $('#jumlah_stok').val(),
                deskripsi: $('#deskripsi').val()
            };
            localStorage.setItem('produk_draft', JSON.stringify(formData));
        }, 1000);
    });

    // Load draft when modal opens
    $('#produkModal').on('show.bs.modal', function() {
        // Only load draft for new products (not editing)
        if (!$('#produk_id').val()) {
            const draft = localStorage.getItem('produk_draft');
            if (draft) {
                const data = JSON.parse(draft);
                $('#nama_produk').val(data.nama_produk || '');
                $('#harga').val(data.harga || '');
                $('#jumlah_stok').val(data.jumlah_stok || '');
                $('#deskripsi').val(data.deskripsi || '');
            }
        }
    });

    // Clear draft when form is successfully submitted
    $(document).on('formSubmitSuccess', function() {
        localStorage.removeItem('produk_draft');
    });

    // Enhanced form validation
    $('#produkForm').on('submit', function(e) {
        let isValid = true;
        
        // Remove previous validation
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        // Validate required fields
        const requiredFields = ['nama_produk', 'harga', 'jumlah_stok'];
        requiredFields.forEach(function(field) {
            const $field = $('#' + field);
            if (!$field.val().trim()) {
                $field.addClass('is-invalid');
                $field.after('<div class="invalid-feedback">Field ini wajib diisi</div>');
                isValid = false;
            }
        });
        
        // Validate numeric fields
        if ($('#harga').val() && parseFloat($('#harga').val()) < 0) {
            $('#harga').addClass('is-invalid');
            $('#harga').after('<div class="invalid-feedback">Harga tidak boleh negatif</div>');
            isValid = false;
        }
        
        if ($('#jumlah_stok').val() && parseInt($('#jumlah_stok').val()) < 0) {
            $('#jumlah_stok').addClass('is-invalid');
            $('#jumlah_stok').after('<div class="invalid-feedback">Stok tidak boleh negatif</div>');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 75) {
            e.preventDefault();
            $('input[type="search"]').focus();
        }
        
        // Ctrl/Cmd + N to add new product
        if ((e.ctrlKey || e.metaKey) && e.keyCode === 78) {
            e.preventDefault();
            $('#produkModal').modal('show');
        }
    });

    // Real-time search enhancement
    let searchTimer;
    $(document).on('input', 'input[type="search"]', function() {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val();
        
        searchTimer = setTimeout(function() {
            table.search(searchTerm).draw();
        }, 300);
    });

    // Stock alert system
    function checkLowStock() {
        const lowStockItems = [];
        $('tbody tr').each(function() {
            const stockText = $(this).find('.stock-display').text();
            const stock = parseInt(stockText.match(/\d+/)[0]);
            const productName = $(this).find('.product-info h6').text();
            
            if (stock <= 5 && stock > 0) {
                lowStockItems.push({name: productName, stock: stock});
            }
        });
        
        if (lowStockItems.length > 0) {
            const message = lowStockItems.map(item => 
                `• ${item.name}: ${item.stock} unit`
            ).join('\n');
            
            // Show notification after page load
            setTimeout(() => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan Stok Rendah',
                    html: `<div style="text-align: left; white-space: pre-line;">${message}</div>`,
                    confirmButtonText: 'Mengerti',
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: 'white'
                });
            }, 1000);
        }
    }
    
    // Check low stock on page load
    checkLowStock();
});

// Global error handler
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
});

// Service Worker registration (optional, for offline functionality)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('SW registered: ', registration);
            })
            .catch(function(registrationError) {
                console.log('SW registration failed: ', registrationError);
            });
    });
}
</script>
@endsection