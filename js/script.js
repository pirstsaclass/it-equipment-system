// Custom JavaScript for IT Equipment Management System

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded'); // สำหรับ debug
    
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize DataTables
    if (window.jQuery && $.fn.DataTable) {
        $('.data-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
            },
            responsive: true
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.classList.contains('alert-dismissible')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
    
    // Initialize Sidebar Toggle
    initializeSidebarToggle();
    
    // Initialize dropdown active states
    initializeDropdowns();
});

// Sidebar Toggle Function
function initializeSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (sidebarToggle) {
        console.log('Sidebar toggle found'); // สำหรับ debug
        
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Sidebar toggle clicked'); // สำหรับ debug
            
            const body = document.body;
            body.classList.toggle('sb-sidenav-toggled');
            
            // Save state to localStorage
            localStorage.setItem('sb|sidebar-toggle', body.classList.contains('sb-sidenav-toggled'));
        });
        
        // Restore sidebar state from localStorage
        const sidebarToggled = localStorage.getItem('sb|sidebar-toggle') === 'true';
        if (sidebarToggled) {
            document.body.classList.add('sb-sidenav-toggled');
        }
    } else {
        console.error('Sidebar toggle button not found');
    }
}

// Initialize dropdown active states
function initializeDropdowns() {
    const currentPage = window.location.pathname.split('/').pop();
    console.log('Current page:', currentPage); // สำหรับ debug
    
    const dropdowns = {
        'equipmentCollapse': ['equipment.php', 'categories.php', 'disposal.php'],
        'maintenanceCollapse': ['maintenance.php', 'equipment_classroom.php'],
        'organizationCollapse': ['departments.php', 'buildingfloorplans.php', 'employees.php']
    };
    
    for (const [dropdownId, pages] of Object.entries(dropdowns)) {
        if (pages.includes(currentPage)) {
            const dropdownElement = document.getElementById(dropdownId);
            const dropdownToggle = document.querySelector(`[data-bs-target="#${dropdownId}"]`);
            
            if (dropdownElement && dropdownToggle) {
                // เปิด dropdown
                const bsCollapse = new bootstrap.Collapse(dropdownElement, {
                    toggle: false
                });
                bsCollapse.show();
                
                dropdownToggle.classList.remove('collapsed');
                dropdownToggle.setAttribute('aria-expanded', 'true');
                
                // เปลี่ยนไอคอนลูกศร
                const arrowIcon = dropdownToggle.querySelector('.fa-angle-down');
                if (arrowIcon) {
                    arrowIcon.classList.remove('fa-angle-down');
                    arrowIcon.classList.add('fa-angle-up');
                }
            }
        }
    }
    
    // จัดการการคลิก dropdown
    const dropdownToggles = document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const arrowIcon = this.querySelector('.fa-angle-down, .fa-angle-up');
            if (arrowIcon) {
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    arrowIcon.classList.remove('fa-angle-up');
                    arrowIcon.classList.add('fa-angle-down');
                } else {
                    arrowIcon.classList.remove('fa-angle-down');
                    arrowIcon.classList.add('fa-angle-up');
                }
            }
        });
    });
}

// Function to show confirmation before delete
function confirmDelete(itemName) {
    return confirm(`คุณแน่ใจว่าต้องการลบ ${itemName} นี้? การกระทำนี้ไม่สามารถย้อนกลับได้`);
}

// Function to export table data to Excel
function exportTableToExcel(tableId, filename = '') {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableId);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    // Specify file name
    filename = filename ? filename + '.xls' : 'excel_data.xls';
    
    // Create download link element
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if (navigator.msSaveOrOpenBlob) {
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        
        // Setting the file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
}

// Function to export table data to PDF
function exportTableToPDF(tableId, filename = '') {
    // This would require a PDF library like jsPDF
    // For now, we'll just alert the user
    alert('ฟังก์ชันการส่งออก PDF กำลังอยู่ในระหว่างการพัฒนา');
}

// Search equipment for maintenance
function searchEquipment() {
    var input = document.getElementById('equipmentSearch');
    var filter = input.value.toUpperCase();
    var table = document.getElementById('equipmentTable');
    var tr = table.getElementsByTagName('tr');
    
    for (var i = 0; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName('td')[0];
        if (td) {
            var txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
}

// Auto-generate equipment code
function generateEquipmentCode() {
    var year = new Date().getFullYear();
    var random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    return 'IT-' + year + '-' + random;
}

// Calculate remaining value for equipment
function calculateRemainingValue(purchasePrice, purchaseDate) {
    var currentDate = new Date();
    var purchase = new Date(purchaseDate);
    var yearsDiff = (currentDate - purchase) / (1000 * 60 * 60 * 24 * 365);
    
    // Assume depreciation of 20% per year
    var depreciation = purchasePrice * 0.2 * yearsDiff;
    var remainingValue = Math.max(0, purchasePrice - depreciation);
    
    return remainingValue.toFixed(2);
}

