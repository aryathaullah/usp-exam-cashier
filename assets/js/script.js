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