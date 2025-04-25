<div> <!-- End of main content wrapper -->
    </div> <!-- End of wrapper -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    
    <script>
    $(document).ready(function() {
        // Auto hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Enable tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Enable popovers
        $('[data-toggle="popover"]').popover();
    });
    </script>
    
    <!-- Footer -->
    <div class="container-fluid">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom scripts -->
    <script>
    $(document).ready(function() {
        // Tambahkan efek fade-in pada konten
        $('.content').addClass('fade-in');
        
        // Aktifkan tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Auto-hide alert setelah 5 detik
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        // Toggle sidebar pada tampilan mobile
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('active');
            $('.content').toggleClass('active');
        });
    });
    </script>
</div> <!-- end of content -->
</div> <!-- end of wrapper -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

<!-- Custom scripts -->
<script>
$(document).ready(function() {
    // Tambahkan efek fade-in pada konten
    $('.content').addClass('fade-in');
    
    // Aktifkan tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Auto-hide alert setelah 5 detik
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
    
    // Toggle sidebar pada tampilan mobile
    $('#sidebarCollapse').on('click', function() {
        $('#sidebar').toggleClass('active');
        $('.content').toggleClass('active');
    });
});
</script>
</body>
</html>