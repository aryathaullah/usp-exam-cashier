// fungsi untuk menghitung total harga di keranjang
function hitungTotal() {
    let total = 0;
    $('.subtotal').each(function() {
        total += parseFloat($(this).data('value'));
    });
    $('#total-harga').text(formatRupiah(total));
    $('#total-harga-input').val(total);
}

// fungsi untuk update jumlah item di keranjang
function updateJumlah(itemId, harga) {
    const jumlah = $('#jumlah-' + itemId).val();
    const subtotal = jumlah * harga;
    $('#subtotal-' + itemId).text(formatRupiah(subtotal));
    $('#subtotal-' + itemId).data('value', subtotal);
    hitungTotal();
}

// fungsi untuk format angka ke rupiah
function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// konfirmasi sebelum menghapus
function konfirmasiHapus(url, nama) {
    if (confirm('Apakah Anda yakin ingin menghapus ' + nama + '?')) {
        window.location.href = url;
    }
}

// fungsi untuk print struk
function printStruk() {
    const printContents = document.getElementById('struk').innerHTML;
    const originalContents = document.body.innerHTML;
    
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}

// event handler saat dokumen siap
$(document).ready(function() {
    // Update jumlah item di keranjang
    $('.jumlah-item').on('change', function() {
        const itemId = $(this).data('id');
        const harga = $(this).data('harga');
        updateJumlah(itemId, harga);
    });
    
    // Hitung total awal
    hitungTotal();
    
    // Validasi form
    $('.needs-validation').submit(function(event) {
        if (this.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
    // Tombol print struk
    $('#btn-print').click(function() {
        printStruk();
    });
});
