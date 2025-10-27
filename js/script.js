// Custom JavaScript for IT Equipment Management System

document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize DataTables
    if ($.fn.DataTable) {
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
        $('.alert').alert('close');
    }, 5000);
});

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