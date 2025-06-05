@extends('layouts.app')
@section('title', 'Kasir')

@section('styles')
<style>
    /* Optimasi untuk semua perangkat */
    .container {
        max-width: 100%;
        padding: 10px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    #barcode-scanner {
        height: 250px;
        background: #000;
        transform: scale(1.3);
        transform-origin: center center;
    }
    
    .scanner-controls .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        margin: 0 3px;
    }
    
    #cart-items {
        max-height: 300px;
        overflow-y: auto;
    }
    
    /* Scanner overlay */
    #scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        pointer-events: none;
    }
    
    #scanner-overlay div {
        border: 3px dashed rgba(255, 255, 255, 0.7);
        width: 80%;
        height: 50%;
        border-radius: 8px;
    }
    
    /* Tombol quantity */
    #decrease-qty, #increase-qty {
        width: 45px;
        font-weight: bold;
    }
    
    /* Input number tanpa spinner */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    
    input[type=number] {
        -moz-appearance: textfield;
        font-weight: bold;
        text-align: center;
    }
    
    /* Animasi untuk cart item */
    .cart-item {
        transition: all 0.3s ease;
        border-radius: 5px;
    }
    
    .cart-item:hover {
        background: #e9ecef !important;
        transform: translateY(-2px);
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        #barcode-scanner {
            height: 200px;
            transform: scale(1.1);
        }
        
        .scanner-controls .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
        }
        
        #cart-items {
            max-height: 200px;
        }
    }
    
    /* Modal pembayaran */
    #changeModal .modal-body {
        padding: 20px;
    }
    
    #cash-paid {
        font-size: 1.2rem;
        font-weight: bold;
    }
    
    #change-amount {
        color: #28a745;
        font-weight: bold;
    }
    #barcode-scanner {
    filter: contrast(1.2) brightness(1.1) saturate(1.1);
    }

    /* Overlay untuk membantu fokus */
    #scanner-overlay div {
        border: 3px solid #00ff00;
        background: rgba(0, 255, 0, 0.1);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 0.6; }
        50% { opacity: 1; }
        100% { opacity: 0.6; }
    }    
</style>
@endsection

@section('content')
<div class="container py-3">
    <div class="row">
        <!-- SCANNER & INPUT -->
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-qrcode me-2"></i>Pemindai Barcode</h5>
                </div>
                <div class="card-body">
                    <div class="scanner-container mb-3">
                        <div id="scanner-viewport" class="position-relative" style="width: 100%; height: 250px; background: #000; overflow: hidden;">
                            <video id="barcode-scanner" style="width: 100%; height: 100%; object-fit: cover;"></video>
                            <div id="scanner-overlay">
                                <div></div>
                            </div>
                        </div>
                        <div class="scanner-controls mt-3 d-flex justify-content-center flex-wrap">
                            <button class="btn btn-dark" id="toggle-scanner">
                                <i class="fas fa-play"></i> Nyalakan Scanner
                            </button>
                            <button class="btn btn-secondary" id="switch-camera">
                                <i class="fas fa-camera"></i> Ganti Kamera
                            </button>
                            <button class="btn btn-info" id="enhance-quality">
                                <i class="fas fa-sun"></i> Perbaiki Kualitas
                            </button>
                            <button class="btn btn-warning" id="zoom-in">
                                <i class="fas fa-search-plus"></i> Zoom
                            </button>
                        </div>                        
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" id="barcode-input" class="form-control form-control-lg" placeholder="Scan atau ketik barcode" autocomplete="off" autofocus>
                        <button class="btn btn-primary" id="manual-scan-btn"><i class="fas fa-search me-1"></i>Cari</button>
                    </div>
                    <div id="scanner-feedback" class="alert" style="display:none;"></div>
                    <div id="product-info" class="card p-3 mb-3" style="display:none;">
                        <h5 id="product-name" class="text-success mb-2"></h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Harga: <strong id="product-price" class="text-primary"></strong></span>
                            <span>Stok: <strong id="product-stock" class="text-info"></strong></span>
                        </div>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" id="decrease-qty"><i class="fas fa-minus"></i></button>
                            <input type="number" id="product-qty" class="form-control text-center" value="1" min="1">
                            <button class="btn btn-outline-secondary" id="increase-qty"><i class="fas fa-plus"></i></button>
                            <button class="btn btn-success" id="add-to-cart"><i class="fas fa-cart-plus me-1"></i>Tambah</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KERANJANG -->
        <div class="col-md-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang</h5>
                </div>
                <div class="card-body">
                    <div id="cart-items">
                        <div id="empty-cart-message" class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p class="mb-0">Keranjang belanja kosong</p>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Total:</h5>
                        <h4 class="text-success mb-0">Rp <span id="cart-total">0</span></h4>
                    </div>
                    <button class="btn btn-primary w-100 py-2 mb-2" id="checkout-btn" disabled>
                        <i class="fas fa-cash-register me-1"></i>Bayar (<span id="cart-count">0</span> item)
                    </button>
                    <button class="btn btn-outline-danger w-100 py-2" id="clear-cart-btn" disabled>
                        <i class="fas fa-trash-alt me-1"></i>Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kembalian -->
<div class="modal fade" id="changeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i> Pembayaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <p class="mb-1">Total Bayar:</p>
                    <h4 class="text-primary" id="change-total">Rp 0</h4>
                </div>
                <div class="mb-3">
                    <label for="cash-paid" class="form-label">Uang Diterima</label>
                    <input type="number" class="form-control form-control-lg" id="cash-paid" autofocus>
                </div>
                <div class="mb-3">
                    <p class="mb-1">Kembalian:</p>
                    <h4 class="text-success" id="change-amount">Rp 0</h4>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button class="btn btn-success" id="print-receipt">
                    <i class="fas fa-print me-1"></i>Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/@zxing/library@latest"></script>
<script>
$(function () {    
    const cart = [];
    // Inisialisasi codeReader dengan semua format barcode
    const hints = new Map();
    hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, [
        ZXing.BarcodeFormat.UPC_A,
        ZXing.BarcodeFormat.UPC_E,
        ZXing.BarcodeFormat.EAN_8,
        ZXing.BarcodeFormat.EAN_13,
        ZXing.BarcodeFormat.CODE_39,
        ZXing.BarcodeFormat.CODE_93,
        ZXing.BarcodeFormat.CODE_128,
        ZXing.BarcodeFormat.CODABAR,
        ZXing.BarcodeFormat.ITF,
        ZXing.BarcodeFormat.RSS_14,
        ZXing.BarcodeFormat.RSS_EXPANDED,
        ZXing.BarcodeFormat.QR_CODE,
        ZXing.BarcodeFormat.DATA_MATRIX,
        ZXing.BarcodeFormat.AZTEC,
        ZXing.BarcodeFormat.PDF_417,
        ZXing.BarcodeFormat.MAXICODE
    ]);

    let codeReader = new ZXing.BrowserMultiFormatReader();
    let selectedDeviceId = null;
    let isScannerActive = false;
    let stream = null;
    let zoomLevel = 1.3;
    let currentCameraSettings = {};

    // Fungsi untuk menampilkan feedback
    function showFeedback(msg, type = 'info') {
        const feedback = $('#scanner-feedback');
        feedback.removeClass().addClass(`alert alert-${type}`).text(msg).show();
        setTimeout(() => feedback.fadeOut(), 3000);
    }

    // Fungsi untuk mengoptimalkan kualitas kamera
    async function optimizeCamera(deviceId) {
        try {
            const constraints = {
                video: {
                    deviceId: deviceId ? { exact: deviceId } : undefined,
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                    facingMode: 'environment',
                    advanced: [
                        { brightness: { ideal: 0.8 } },
                        { contrast: { ideal: 0.8 } },
                        { saturation: { ideal: 0.8 } }
                    ]
                }
            };

            // Hentikan stream sebelumnya jika ada
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }

            // Dapatkan stream kamera baru
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            const video = document.getElementById('barcode-scanner');
            video.srcObject = stream;
            
            // Simpan pengaturan kamera
            const track = stream.getVideoTracks()[0];
            if (track && track.getCapabilities) {
                currentCameraSettings = track.getCapabilities();
            }
            
            // Terapkan zoom
            applyZoom();
            
            return stream;
        } catch (error) {
            console.error('Camera optimization error:', error);
            showFeedback('Gagal mengakses kamera', 'danger');
            return null;
        }
    }

    // Fungsi untuk menerapkan zoom
    function applyZoom() {
        $('#barcode-scanner').css('transform', `scale(${zoomLevel})`);
    }

    // Fungsi untuk memulai scanner
    async function startScanner(deviceId = null) {
        try {
            const videoInputDevices = await codeReader.listVideoInputDevices();
            
            if (videoInputDevices.length === 0) {
                showFeedback('Tidak ada kamera yang terdeteksi', 'warning');
                return;
            }
            
            // Pilih kamera terbaik
            if (!deviceId) {
                const backCamera = videoInputDevices.find(device => 
                    device.label.toLowerCase().includes('back') || 
                    device.label.toLowerCase().includes('rear'));
                
                selectedDeviceId = backCamera ? backCamera.deviceId : videoInputDevices[0].deviceId;
            } else {
                selectedDeviceId = deviceId;
            }
            
            // Optimalkan kamera sebelum memulai scan
            await optimizeCamera(selectedDeviceId);
            
            // Mulai scanner
            await codeReader.decodeFromVideoDevice(selectedDeviceId, 'barcode-scanner', (result, err) => {
                if (result) {
                    $('#barcode-input').val(result.text);
                    scanProduct(result.text);
                }
                if (err && !(err instanceof ZXing.NotFoundException)) {
                    console.error('Scan error:', err);
                }
            });
            
            isScannerActive = true;
            $('#toggle-scanner').html('<i class="fas fa-stop"></i> Matikan Scanner');
            $('#scanner-viewport').show();
            showFeedback('Scanner aktif - Arahkan barcode ke area kotak', 'success');
        } catch (error) {
            console.error('Scanner error:', error);
            showFeedback('Gagal mengaktifkan scanner', 'danger');
        }
    }

    // Fungsi untuk menghentikan scanner
    async function stopScanner() {
        try {
            codeReader.reset();
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            zoomLevel = 1.3;
            isScannerActive = false;
            $('#toggle-scanner').html('<i class="fas fa-play"></i> Nyalakan Scanner');
            $('#scanner-viewport').hide();
        } catch (error) {
            console.error('Error stopping scanner:', error);
        }
    }

    // Toggle scanner
    $('#toggle-scanner').click(function() {
        if (isScannerActive) {
            stopScanner();
        } else {
            startScanner();
        }
    });

    // Ganti kamera
    $('#switch-camera').click(async function() {
        if (!isScannerActive) return;
        
        try {
            const videoInputDevices = await codeReader.listVideoInputDevices();
            
            if (videoInputDevices.length < 2) {
                showFeedback('Hanya ada 1 kamera yang tersedia', 'info');
                return;
            }
            
            const currentIndex = videoInputDevices.findIndex(device => 
                device.deviceId === selectedDeviceId);
            const nextIndex = (currentIndex + 1) % videoInputDevices.length;
            
            stopScanner();
            startScanner(videoInputDevices[nextIndex].deviceId);
            
            showFeedback(`Menggunakan kamera: ${videoInputDevices[nextIndex].label}`, 'info');
        } catch (error) {
            console.error('Error switching camera:', error);
            showFeedback('Gagal mengganti kamera', 'danger');
        }
    });

    // Meningkatkan kualitas kamera
    $('#enhance-quality').click(async function() {
        if (!isScannerActive) return;
        
        try {
            showFeedback('Meningkatkan kualitas gambar...', 'info');
            const track = stream.getVideoTracks()[0];
            if (track && track.getCapabilities) {
                const settings = {
                    brightness: currentCameraSettings.brightness?.max || 1.0,
                    contrast: currentCameraSettings.contrast?.max || 1.0,
                    saturation: 1.0,
                    focusMode: 'continuous'
                };
                await track.applyConstraints({ advanced: [settings] });
                showFeedback('Kualitas gambar ditingkatkan', 'success');
            }
        } catch (error) {
            console.error('Error enhancing quality:', error);
            showFeedback('Gagal meningkatkan kualitas', 'danger');
        }
    });

    // Zoom in/out
    $('#zoom-in').click(function() {
        zoomLevel = zoomLevel < 2.5 ? zoomLevel + 0.2 : 1.0;
        applyZoom();
        showFeedback(`Zoom: ${zoomLevel.toFixed(1)}x`, 'info');
    });

    // Fungsi untuk memindai produk
    function scanProduct(barcode) {
        if (!barcode) return;
        
        showFeedback('Memproses barcode...', 'info');
        
        $.get(`/api/products/${barcode}`, function(res) {
            if (res.success) {
                const p = res.data;
                $('#product-info').show().data('product', p);
                $('#product-name').text(p.nama_produk);
                $('#product-price').text(parseInt(p.harga).toLocaleString('id-ID'));
                $('#product-stock').text(p.jumlah_stok);
                $('#product-qty').val(1).attr('max', p.jumlah_stok).focus();
                
                showFeedback('Produk ditemukan: ' + p.nama_produk, 'success');
                
                if (isScannerActive) {
                    codeReader.reset();
                    startScanner(selectedDeviceId);
                }
            } else {
                showFeedback('Produk tidak ditemukan', 'warning');
            }
        }).fail(function() {
            showFeedback('Gagal memuat data produk', 'danger');
        });
    }

    // Pindai manual
    $('#manual-scan-btn').click(function() {
        const code = $('#barcode-input').val().trim();
        if (code) scanProduct(code);
    });

    // Input barcode langsung enter
    $('#barcode-input').keypress(function(e) {
        if (e.which === 13) {
            const code = $(this).val().trim();
            if (code) scanProduct(code);
        }
    });

    // Tombol tambah/kurangi quantity
    $('#decrease-qty').click(function() {
        const qtyInput = $('#product-qty');
        let qty = parseInt(qtyInput.val()) || 1;
        if (qty > 1) qty--;
        qtyInput.val(qty);
    });

    $('#increase-qty').click(function() {
        const qtyInput = $('#product-qty');
        const max = parseInt(qtyInput.attr('max')) || Infinity;
        let qty = parseInt(qtyInput.val()) || 1;
        if (qty < max) qty++;
        qtyInput.val(qty);
    });

    // Tambah ke keranjang
    $('#add-to-cart').click(function() {
        const product = $('#product-info').data('product');
        const qty = parseInt($('#product-qty').val()) || 1;
        
        if (!product || qty < 1 || qty > product.jumlah_stok) {
            return showFeedback('Jumlah tidak valid', 'danger');
        }

        const idx = cart.findIndex(i => i.id === product.id);
        if (idx !== -1) {
            const newQty = cart[idx].quantity + qty;
            if (newQty > product.jumlah_stok) return showFeedback('Stok tidak cukup', 'danger');
            cart[idx].quantity = newQty;
        } else {
            cart.push({ ...product, quantity: qty });
        }

        $('#product-info').hide();
        $('#barcode-input').val('').focus();
        updateCart();
        showFeedback('Produk ditambahkan ke keranjang', 'success');
    });

    // Update tampilan keranjang
    function updateCart() {
        const $items = $('#cart-items').empty();
        let total = 0;
        
        if (!cart.length) {
            $('#checkout-btn').prop('disabled', true);
            $('#clear-cart-btn').prop('disabled', true);
            $('#cart-total').text('0');
            $('#cart-count').text('0');
            return $items.html($('#empty-cart-message').clone().show());
        }

        cart.forEach((item, idx) => {
            const itemTotal = item.harga * item.quantity;
            total += itemTotal;
            
            $items.append(`
                <div class="cart-item mb-2 p-2" style="background: #f8f9fa;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <strong class="d-block">${item.nama_produk}</strong>
                            <small class="text-muted">${item.quantity} x Rp ${parseInt(item.harga).toLocaleString('id-ID')}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">Rp ${itemTotal.toLocaleString('id-ID')}</div>
                            <button class="btn btn-sm btn-outline-danger mt-1" onclick="removeItem(${idx})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
        });

        $('#cart-total').text(total.toLocaleString('id-ID'));
        $('#cart-count').text(cart.reduce((a, i) => a + i.quantity, 0));
        $('#checkout-btn').prop('disabled', false);
        $('#clear-cart-btn').prop('disabled', false);
    }

    // Fungsi global untuk menghapus item
    window.removeItem = function(index) {
        const removed = cart.splice(index, 1)[0];
        updateCart();
        showFeedback(`${removed.nama_produk} dihapus dari keranjang`, 'info');
    };

    // Kosongkan keranjang
    $('#clear-cart-btn').click(function() {
        if (cart.length > 0) {
            if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                cart.length = 0;
                updateCart();
                showFeedback('Keranjang telah dikosongkan', 'info');
            }
        }
    });

    // Proses checkout
    $('#checkout-btn').click(function() {
        const total = cart.reduce((sum, i) => sum + (i.harga * i.quantity), 0);
        $('#change-total').text('Rp ' + total.toLocaleString('id-ID'));
        $('#cash-paid').val('').focus();
        $('#change-amount').text('Rp 0');
        $('#changeModal').modal('show');
    });

    // Hitung kembalian
    $('#cash-paid').on('input', function() {
        const bayar = parseInt($(this).val()) || 0;
        const total = cart.reduce((sum, i) => sum + (i.harga * i.quantity), 0);
        const kembali = bayar - total;
        $('#change-amount').text('Rp ' + (kembali > 0 ? kembali.toLocaleString('id-ID') : 0));
    });

    // Cetak struk
    $('#print-receipt').click(function() {
        const total = cart.reduce((sum, i) => sum + (i.harga * i.quantity), 0);
        const cashPaid = parseInt($('#cash-paid').val()) || 0;
        const change = cashPaid - total;
        
        if (cashPaid < total) {
            showFeedback('Uang yang dibayarkan kurang', 'danger');
            return;
        }

        $.post('/checkout', {
            _token: '{{ csrf_token() }}',
            items: cart,
            total: total,
            cash_paid: cashPaid,
            change: change
        }, function(res) {
            if (res.success) {
                printReceipt(total, cashPaid, change);
                cart.length = 0;
                updateCart();
                $('#changeModal').modal('hide');
            } else {
                showFeedback(res.message || 'Gagal menyimpan transaksi', 'danger');
            }
        }).fail(function() {
            showFeedback('Gagal menyimpan transaksi', 'danger');
        });
    });

    // Fungsi cetak struk
    function printReceipt(total, cashPaid, change) {
        const struk = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Struk Belanja</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 10px; max-width: 300px; margin: 0 auto; }
                    .header { text-align: center; margin-bottom: 10px; }
                    .item { margin-bottom: 5px; }
                    .divider { border-top: 1px dashed #000; margin: 10px 0; }
                    .text-right { text-align: right; }
                    .text-center { text-align: center; }
                    .text-bold { font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h3>Toko Arnis</h3>
                    <p>Jl. Contoh No. 123, Jakarta</p>
                    <p>${new Date().toLocaleString('id-ID')}</p>
                </div>
                <div class="divider"></div>
                ${cart.map(i => `
                    <div class="item">
                        <div>${i.nama_produk}</div>
                        <div class="text-right">
                            ${i.quantity} x Rp ${i.harga.toLocaleString('id-ID')} = <span class="text-bold">Rp ${(i.harga * i.quantity).toLocaleString('id-ID')}</span>
                        </div>
                    </div>
                `).join('')}
                <div class="divider"></div>
                <div class="text-right">
                    <p>Total: <span class="text-bold">Rp ${total.toLocaleString('id-ID')}</span></p>
                    <p>Tunai: <span class="text-bold">Rp ${cashPaid.toLocaleString('id-ID')}</span></p>
                    <p>Kembali: <span class="text-bold">Rp ${change.toLocaleString('id-ID')}</span></p>
                </div>
                <div class="divider"></div>
                <div class="text-center">
                    <p>Terima kasih telah berbelanja</p>
                    <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
                </div>
            </body>
            </html>
        `;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(struk);
        printWindow.document.close();
        
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    // Inisialisasi awal
    $(document).ready(function() {
        $('#scanner-viewport').hide();
        $('#barcode-input').focus();
        
        // Deteksi perangkat mobile
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            $('input, select, textarea').attr('autocomplete', 'off');
            $('input[type="number"]').attr('pattern', '[0-9]*');
            zoomLevel = 1.1; // Kurangi zoom untuk mobile
        }
    });
});
</script>
@endsection