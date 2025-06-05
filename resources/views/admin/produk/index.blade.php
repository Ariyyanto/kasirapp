@extends('layouts.app')

@section('title', 'Manajemen Produk')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Produk</h5>
                <button class="btn btn-light add-btn" data-bs-toggle="modal" data-bs-target="#produkModal">
                    <i class="bi bi-plus"></i> Tambah Produk
                </button>                
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="produkTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Barcode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produk as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_produk }}</td>
                            <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td>{{ $item->jumlah_stok }}</td>
                            <td>{{ $item->barcode }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $item->id }}" 
                                    data-nama="{{ $item->nama_produk }}" 
                                    data-harga="{{ $item->harga }}"
                                    data-stok="{{ $item->jumlah_stok }}"
                                    data-barcode="{{ $item->barcode }}"
                                    data-deskripsi="{{ $item->deskripsi }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $item->id }}">
                                    <i class="bi bi-trash"></i> Hapus
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
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="produkForm" method="POST">
                @csrf
                <input type="hidden" id="produk_id" name="id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="produkModalLabel">Tambah Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_produk" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_stok" class="form-label">Jumlah Stok</label>
                        <input type="number" class="form-control" id="jumlah_stok" name="jumlah_stok" required>
                    </div>
                    <div class="mb-3">
                        <label for="barcode" class="form-label">Barcode</label>
                        <input type="text" class="form-control" id="barcode" name="barcode">
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTable dengan konfigurasi bahasa Indonesia
        $('#produkTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            responsive: true
        });

        // Set CSRF token untuk semua AJAX request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fungsi untuk menampilkan modal dengan data produk
        const showProdukModal = (title, action, method = 'POST', data = {}) => {
            $('#produkModalLabel').text(title);
            $('#produkForm').attr('action', action);
            
            // Reset form dan hapus method yang ada
            $('#produkForm')[0].reset();
            $('input[name="_method"]').remove();
            
            // Jika method bukan POST, tambahkan input hidden _method
            if (method !== 'POST') {
                $('#produkForm').append(`<input type="hidden" name="_method" value="${method}">`);
            }
            
            // Isi data jika ada
            if (Object.keys(data).length > 0) {
                $('#produk_id').val(data.id || '');
                $('#nama_produk').val(data.nama || '');
                $('#harga').val(data.harga || '');
                $('#jumlah_stok').val(data.stok || '');
                $('#barcode').val(data.barcode || '');
                $('#deskripsi').val(data.deskripsi || '');
            }
            
            $('#produkModal').modal('show');
        };

        // Handler tombol edit
        $(document).on('click', '.edit-btn', function() {
            const produkData = {
                id: $(this).data('id'),
                nama: $(this).data('nama'),
                harga: $(this).data('harga'),
                stok: $(this).data('stok'),
                barcode: $(this).data('barcode'),
                deskripsi: $(this).data('deskripsi')
            };
            
            showProdukModal(
                'Edit Produk', 
                "{{ url('admin/produk') }}/" + produkData.id, 
                'PUT', 
                produkData
            );
        });

        // Handler tombol tambah
        $('.add-btn').click(function() {
            showProdukModal(
                'Tambah Produk', 
                "{{ route('produk.store') }}"
            );
        });

        // Handler tombol hapus
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');
            const url = "{{ url('admin/produk') }}/" + id;
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.message || 'Produk berhasil dihapus',
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'Gagal menghapus produk';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errorMessage,
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        });

        // Handler submit form
        $('#produkForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const action = $(form).attr('action');
            const method = $(form).find('input[name="_method"]').val() || 'POST';
            const isEdit = method !== 'POST';
            
            Swal.fire({
                title: 'Konfirmasi',
                text: isEdit ? 'Apakah Anda yakin ingin memperbarui produk?' : 'Apakah Anda yakin ingin menambahkan produk?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tambahkan _method ke FormData jika diperlukan
                    if (isEdit) {
                        formData.append('_method', method);
                    }
                    
                    $.ajax({
                        url: action,
                        method: 'POST', // Selalu gunakan POST dan biarkan _method handle PUT/PATCH
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#produkModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses!',
                                text: response.message || 'Operasi berhasil dilakukan',
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan';
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                if (xhr.responseJSON.errors) {
                                    errorMessage = Object.values(xhr.responseJSON.errors).join('<br>');
                                }
                            } else if (xhr.status === 419) {
                                errorMessage = 'Session expired. Silakan refresh halaman dan coba lagi.';
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                html: errorMessage,
                                timer: 5000,
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });
        });

        // Tampilkan pesan sukses/error dari session
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    });
</script>
@endsection