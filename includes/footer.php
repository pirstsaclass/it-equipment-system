        </main>
        
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Varee IT Team 2025</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>

    </div>  <!-- layoutSidenav_content -->
</div>  <!-- layoutSidenav -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS -->
<script src="js/script.js"></script>
<script src="js/script-importdata.js"></script>

<script>
    // Global functions
    function confirmAction(message = 'คุณแน่ใจหรือไม่?') {
        return confirm(message);
    }
    
    // Toast notifications
    function showToast(message, type = 'success') {
        // Implementation for toast notifications
        console.log('Toast:', message, type);
    }
</script>

</body>
</html>