// Theme Blue JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

    // Restore sidebar state
    if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        document.body.classList.add('sb-sidenav-toggled');
    }

    // Hide pre-loader when page is loaded
    window.addEventListener('load', function() {
        const loader = document.querySelector('.loader-bg');
        if (loader) {
            setTimeout(function() {
                loader.style.opacity = '0';
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 300);
            }, 500);
        }
    });

    // Add active class to current page in sidebar
    function setActiveSidebarItem() {
        const currentPage = window.location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.sidebar .nav-link, .sidebar .nav-sub-link');
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('href');
            if (href === currentPage) {
                link.classList.add('active');
                
                // Expand parent collapse if it's a sub-link
                const parentCollapse = link.closest('.collapse');
                if (parentCollapse) {
                    const parentLink = document.querySelector(`[href="#${parentCollapse.id}"]`);
                    if (parentLink) {
                        parentLink.classList.remove('collapsed');
                        parentLink.setAttribute('aria-expanded', 'true');
                        parentCollapse.classList.add('show');
                    }
                }
            }
        });
    }

    setActiveSidebarItem();

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('[data-confirm-delete]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('คุณแน่ใจหรือไม่ที่ต้องการลบรายการนี้?')) {
                e.preventDefault();
            }
        });
    });

    // Form validation enhancement
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Auto-format numbers
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toLocaleString('th-TH');
            }
        });
        
        input.addEventListener('focus', function() {
            if (this.value) {
                this.value = this.value.replace(/,/g, '');
            }
        });
    });

    // Enhanced date picker initialization
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Add Thai date format display
        input.addEventListener('change', function() {
            if (this.value) {
                const date = new Date(this.value);
                const thaiDate = date.toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                // Create or update display element
                let displayEl = this.nextElementSibling;
                if (!displayEl || !displayEl.classList.contains('date-display')) {
                    displayEl = document.createElement('small');
                    displayEl.className = 'date-display form-text text-muted mt-1';
                    this.parentNode.appendChild(displayEl);
                }
                displayEl.textContent = thaiDate;
            }
        });
    });

    // Table row selection
    const selectableTables = document.querySelectorAll('table[data-selectable="true"]');
    selectableTables.forEach(table => {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('click', function() {
                this.classList.toggle('table-primary');
            });
        });
    });

    // Print functionality
    const printButtons = document.querySelectorAll('[data-print]');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            const printSection = document.querySelector(this.dataset.print);
            if (printSection) {
                const printContent = printSection.innerHTML;
                const originalContent = document.body.innerHTML;
                
                document.body.innerHTML = printContent;
                window.print();
                document.body.innerHTML = originalContent;
                location.reload();
            }
        });
    });

    // Export to Excel functionality
    const exportButtons = document.querySelectorAll('[data-export]');
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tableId = this.dataset.export;
            const table = document.getElementById(tableId);
            if (table) {
                let csv = [];
                const rows = table.querySelectorAll('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    let row = [], cols = rows[i].querySelectorAll('td, th');
                    
                    for (let j = 0; j < cols.length; j++) {
                        let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
                        data = data.replace(/"/g, '""');
                        row.push('"' + data + '"');
                    }
                    
                    csv.push(row.join(','));
                }
                
                const csvString = csv.join('\n');
                const filename = `export-${new Date().toISOString().split('T')[0]}.csv`;
                
                const link = document.createElement('a');
                link.style.display = 'none';
                link.setAttribute('target', '_blank');
                link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString));
                link.setAttribute('download', filename);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    });
});

// Utility functions
function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toastEl);
    
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
    
    // Remove toast after hide
    toastEl.addEventListener('hidden.bs.toast', function() {
        toastEl.remove();
    });
}

function confirmAction(message = 'คุณแน่ใจหรือไม่?') {
    return confirm(message);
}

function formatNumber(num) {
    return new Intl.NumberFormat('th-TH').format(num);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}