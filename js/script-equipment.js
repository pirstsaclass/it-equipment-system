
// เก็บข้อมูลหมวดหมู่ย่อยทั้งหมด
const subcategories = <?php echo json_encode($subcategories_all); ?>;

function updateSubcategories() {
    const categoryId = document.getElementById('category_id').value;
    const subcategorySelect = document.getElementById('subcategory_id');
    
    // ล้างตัวเลือกทั้งหมดยกเว้นตัวเลือกแรก
    subcategorySelect.innerHTML = '<option value="">เลือกหมวดหมู่ย่อย</option>';
    
    if (categoryId) {
        // กรองหมวดหมู่ย่อยตามหมวดหมู่ที่เลือก
        const filteredSubcategories = subcategories.filter(sub => sub.category_id == categoryId);
        
        filteredSubcategories.forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.subcategory_name;
            subcategorySelect.appendChild(option);
        });
    }
}

function clearForm() {
    document.getElementById('equipmentForm').reset();
    document.getElementById('equipment_id').value = '';
    document.getElementById('current_equipment_image').value = '';
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('equipmentModalLabel').textContent = 'เพิ่มครุภัณฑ์';
    document.getElementById('submitButton').name = 'add_equipment';
    document.getElementById('submitButton').textContent = 'บันทึก';
    
    // รีเซ็ตหมวดหมู่ย่อย
    document.getElementById('subcategory_id').innerHTML = '<option value="">เลือกหมวดหมู่ย่อย</option>';
}

function editEquipment(equipment) {
    document.getElementById('equipmentModalLabel').textContent = 'แก้ไขครุภัณฑ์';
    document.getElementById('submitButton').name = 'edit_equipment';
    document.getElementById('submitButton').textContent = 'อัพเดต';
    
    // ตั้งค่าฟิลด์ต่างๆ
    document.getElementById('equipment_id').value = equipment.id;
    document.getElementById('equipment_code').value = equipment.equipment_code;
    document.getElementById('equipment_name').value = equipment.equipment_name;
    document.getElementById('category_id').value = equipment.category_id;
    
    // อัพเดตหมวดหมู่ย่อยตามหมวดหมู่ที่เลือก
    updateSubcategories();
    
    // ตั้งค่าหมวดหมู่ย่อยหลังจากอัพเดตตัวเลือกแล้ว
    setTimeout(() => {
        document.getElementById('subcategory_id').value = equipment.subcategory_id;
    }, 100);
    
    document.getElementById('brand_name').value = equipment.brand_name || '';
    document.getElementById('model_name').value = equipment.model_name || '';
    document.getElementById('serial_number').value = equipment.serial_number || '';
    document.getElementById('equipment_status').value = equipment.equipment_status;
    document.getElementById('purchase_date').value = equipment.purchase_date || '';
    document.getElementById('warranty_expiry_date').value = equipment.warranty_expiry_date || '';
    document.getElementById('purchase_price').value = equipment.purchase_price || '';
    document.getElementById('supplier_name').value = equipment.supplier_name || '';
    document.getElementById('location_school').value = equipment.location_school || '';
    document.getElementById('location_building').value = equipment.location_building || '';
    document.getElementById('location_floor').value = equipment.location_floor || '';
    document.getElementById('location_room').value = equipment.location_room || '';
    document.getElementById('responsible_person').value = equipment.responsible_person || '';
    document.getElementById('notes').value = equipment.notes || '';
    document.getElementById('current_equipment_image').value = equipment.image_path || '';
    
    // แสดงรูปภาพถ้ามี
    const imagePreview = document.getElementById('imagePreview');
    imagePreview.innerHTML = '';
    if (equipment.image_path) {
        const img = document.createElement('img');
        img.src = 'uploads/img_equipment/' + equipment.image_path;
        img.alt = 'รูปครุภัณฑ์';
        img.style.maxWidth = '200px';
        img.style.maxHeight = '200px';
        img.className = 'img-thumbnail';
        imagePreview.appendChild(img);
    }
}

function viewEquipment(equipment) {
    const modalBody = document.getElementById('viewModalBody');
    
    let content = `
        <div class="row">
            <div class="col-md-4 text-center mb-3">
    `;
    
    if (equipment.image_path) {
        content += `
            <img src="uploads/img_equipment/${equipment.image_path}" 
                 alt="รูปครุภัณฑ์" 
                 class="img-fluid rounded shadow-sm"
                 style="max-height: 200px;">
        `;
    } else {
        content += `
            <div class="text-muted">
                <i class="fas fa-image fa-3x mb-2"></i>
                <br>ไม่มีรูปภาพ
            </div>
        `;
    }
    
    content += `
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">รหัสครุภัณฑ์</th>
                        <td>${equipment.equipment_code}</td>
                    </tr>
                    <tr>
                        <th>ชื่อครุภัณฑ์</th>
                        <td>${equipment.equipment_name}</td>
                    </tr>
                    <tr>
                        <th>หมวดหมู่</th>
                        <td>${equipment.category_name || '-'}</td>
                    </tr>
                    <tr>
                        <th>หมวดหมู่ย่อย</th>
                        <td>${equipment.subcategory_name || '-'}</td>
                    </tr>
                    <tr>
                        <th>ยี่ห้อ/รุ่น</th>
                        <td>${equipment.brand_name || '-'} / ${equipment.model_name || '-'}</td>
                    </tr>
                    <tr>
                        <th>หมายเลขซีเรียล</th>
                        <td>${equipment.serial_number || '-'}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">สถานะครุภัณฑ์</th>
                        <td>
                            <span class="badge bg-${getStatusBadgeColor(equipment.equipment_status)}">
                                ${equipment.equipment_status}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>วันที่ซื้อ</th>
                        <td>${equipment.purchase_date ? formatDate(equipment.purchase_date) : '-'}</td>
                    </tr>
                    <tr>
                        <th>วันที่หมดประกัน</th>
                        <td>${equipment.warranty_expiry_date ? formatDate(equipment.warranty_expiry_date) : '-'}</td>
                    </tr>
                    <tr>
                        <th>ราคาซื้อ</th>
                        <td>${equipment.purchase_price ? formatCurrency(equipment.purchase_price) : '-'}</td>
                    </tr>
                    <tr>
                        <th>ผู้จำหน่าย</th>
                        <td>${equipment.supplier_name || '-'}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">ตำแหน่งที่ตั้ง</th>
                        <td>
                            ${[equipment.location_building, equipment.location_floor ? 'ชั้น ' + equipment.location_floor : '', equipment.location_room].filter(Boolean).join(' / ') || '-'}
                        </td>
                    </tr>
                    <tr>
                        <th>โรงเรียน</th>
                        <td>${equipment.location_school || '-'}</td>
                    </tr>
                    <tr>
                        <th>ผู้รับผิดชอบ</th>
                        <td>${equipment.responsible_person || '-'}</td>
                    </tr>
                    <tr>
                        <th>สถานะซ่อม</th>
                        <td>
                            <span class="badge bg-${getRepairStatusBadgeColor(equipment.repair_status)}">
                                ${equipment.repair_status || 'ไม่มี'}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    `;
    
    if (equipment.notes) {
        content += `
            <div class="row mt-3">
                <div class="col-12">
                    <strong>หมายเหตุ:</strong>
                    <div class="border p-2 rounded bg-light">${equipment.notes}</div>
                </div>
            </div>
        `;
    }
    
    modalBody.innerHTML = content;
}

function getStatusBadgeColor(status) {
    const colors = {
        'ใหม่': 'success',
        'ใช้งานปกติ': 'primary',
        'ชำรุด': 'warning',
        'กำลังซ่อม': 'info',
        'ซ่อมเสร็จแล้ว': 'success',
        'จำหน่ายแล้ว': 'dark'
    };
    return colors[status] || 'secondary';
}

function getRepairStatusBadgeColor(status) {
    const colors = {
        'รอซ่อม': 'warning',
        'กำลังดำเนินการ': 'info',
        'ซ่อมเสร็จ': 'success',
        'ยกเลิก': 'danger'
    };
    return colors[status] || 'secondary';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('th-TH');
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(amount);
}

// แสดงตัวอย่างรูปภาพก่อนอัพโหลด
document.getElementById('equipment_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    preview.innerHTML = '';
    
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('ขนาดไฟล์ต้องไม่เกิน 5MB');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'ตัวอย่างรูปภาพ';
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.className = 'img-thumbnail';
            preview.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
});

// ป้องกันการส่งฟอร์มถ้ามีข้อผิดพลาด
document.getElementById('equipmentForm').addEventListener('submit', function(e) {
    const equipmentCode = document.getElementById('equipment_code').value.trim();
    const equipmentName = document.getElementById('equipment_name').value.trim();
    const categoryId = document.getElementById('category_id').value;
    
    if (!equipmentCode || !equipmentName || !categoryId) {
        e.preventDefault();
        alert('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน');
        return;
    }
});

// ==================== ฟังก์ชันใหม่สำหรับการกรองข้อมูล ====================

// อัพเดตเวลาปัจจุบัน
function updateCurrentTime() {
    const now = new Date();
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        timeZone: 'Asia/Bangkok'
    };
    const formatter = new Intl.DateTimeFormat('th-TH', options);
    document.getElementById('currentTime').textContent = 'อัพเดตล่าสุด: ' + formatter.format(now);
}

// เรียกอัพเดตเวลาทุก 1 นาที
setInterval(updateCurrentTime, 60000);
updateCurrentTime(); // เรียกครั้งแรก

// ฟังก์ชันกรองข้อมูลแบบ real-time
function filterTable() {
    const globalSearch = document.getElementById('globalSearch').value.toLowerCase();
    const equipmentStatus = document.getElementById('equipmentStatusFilter').value;
    const repairStatus = document.getElementById('repairStatusFilter').value;
    const school = document.getElementById('schoolFilter').value;
    
    const rows = document.querySelectorAll('#equipmentTableBody .equipment-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const code = row.getAttribute('data-code').toLowerCase();
        const name = row.getAttribute('data-name').toLowerCase();
        const category = row.getAttribute('data-category').toLowerCase();
        const subcategory = row.getAttribute('data-subcategory').toLowerCase();
        const status = row.getAttribute('data-status');
        const repair = row.getAttribute('data-repair-status');
        const schoolData = row.getAttribute('data-school');
        const building = row.getAttribute('data-building').toLowerCase();
        const room = row.getAttribute('data-room').toLowerCase();
        
        let showRow = true;
        
        // กรองด้วยช่องค้นหาทั่วไป
        if (globalSearch) {
            const searchText = globalSearch;
            if (!code.includes(searchText) && 
                !name.includes(searchText) && 
                !category.includes(searchText) && 
                !subcategory.includes(searchText) &&
                !building.includes(searchText) &&
                !room.includes(searchText)) {
                showRow = false;
            }
        }
        
        // กรองด้วยสถานะครุภัณฑ์
        if (equipmentStatus && status !== equipmentStatus) {
            showRow = false;
        }
        
        // กรองด้วยสถานะซ่อม
        if (repairStatus && repair !== repairStatus) {
            showRow = false;
        }
        
        // กรองด้วยโรงเรียน
        if (school && schoolData !== school) {
            showRow = false;
        }
        
        // แสดง/ซ่อนแถว
        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // อัพเดตจำนวนผลลัพธ์
    updateResultCount(visibleCount, rows.length);
    
    // แสดงข้อความเมื่อไม่พบผลลัพธ์
    showNoResultsMessage(visibleCount);
}

// อัพเดตจำนวนผลลัพธ์
function updateResultCount(visible, total) {
    const totalCount = document.getElementById('totalCount');
    const showingCount = document.getElementById('showingCount');
    
    totalCount.textContent = total.toLocaleString();
    showingCount.textContent = visible.toLocaleString();
    
    // เปลี่ยนสีข้อความตามจำนวนผลลัพธ์
    const resultCount = document.getElementById('resultCount');
    if (visible === 0) {
        resultCount.className = 'alert alert-danger py-2 mb-0';
    } else if (visible < total) {
        resultCount.className = 'alert alert-warning py-2 mb-0';
    } else {
        resultCount.className = 'alert alert-info py-2 mb-0';
    }
}

// แสดงข้อความเมื่อไม่พบผลลัพธ์
function showNoResultsMessage(visibleCount) {
    let noResultsRow = document.querySelector('#equipmentTableBody tr:not(.equipment-row)');
    
    if (visibleCount === 0) {
        if (!noResultsRow) {
            noResultsRow = document.createElement('tr');
            noResultsRow.innerHTML = `
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">ไม่พบข้อมูลครุภัณฑ์ที่ตรงกับเงื่อนไขการค้นหา</h5>
                    <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือล้างตัวกรอง</p>
                    <button type="button" class="btn btn-primary" onclick="clearFilters()">
                        ล้างตัวกรองทั้งหมด
                    </button>
                </td>
            `;
            document.getElementById('equipmentTableBody').appendChild(noResultsRow);
        }
    } else if (noResultsRow) {
        noResultsRow.remove();
    }
}

// ล้างตัวกรองทั้งหมด
function clearFilters() {
    document.getElementById('globalSearch').value = '';
    document.getElementById('equipmentStatusFilter').value = '';
    document.getElementById('repairStatusFilter').value = '';
    document.getElementById('schoolFilter').value = '';
    
    filterTable();
}

// ส่งออกข้อมูลเป็น Excel
function exportToExcel() {
    const table = document.getElementById('equipmentTable');
    const rows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    
    if (rows.length === 0) {
        alert('ไม่มีข้อมูลที่จะส่งออก');
        return;
    }
    
    let csv = [];
    const headers = [];
    
    // เพิ่มหัวข้อภาษาไทย
    table.querySelectorAll('thead th').forEach(header => {
        headers.push(header.textContent.trim());
    });
    
    // เพิ่ม BOM สำหรับรองรับภาษาไทยใน Excel
    const BOM = "\uFEFF";
    csv.push(BOM + headers.join(','));
    
    // เพิ่มข้อมูล
    rows.forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach(cell => {
            let text = cell.textContent.trim();
            
            // ลบ HTML tags และจัดการกับเครื่องหมาย comma
            text = text.replace(/,/g, ';');
            text = text.replace(/\n/g, ' ');
            text = text.replace(/\r/g, ' ');
            
            // ลบ badge text และเอาเฉพาะข้อความสถานะ
            const badge = cell.querySelector('.badge');
            if (badge) {
                text = badge.textContent.trim();
            }
            
            // จัดการกับข้อมูลการจำหน่าย
            const smallText = cell.querySelector('small');
            if (smallText) {
                text += ' ' + smallText.textContent.trim();
            }
            
            // ใส่ใน quotes เพื่อป้องกันปัญหา comma
            rowData.push(`"${text}"`);
        });
        csv.push(rowData.join(','));
    });
    
    // สร้างไฟล์และดาวน์โหลด
    const csvString = csv.join('\n');
    const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    // ใช้ชื่อไฟล์ภาษาไทย
    const today = new Date();
    const dateString = today.toLocaleDateString('th-TH').replace(/\//g, '-');
    link.setAttribute('href', url);
    link.setAttribute('download', `รายงานครุภัณฑ์_${dateString}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// พิมพ์ตาราง
function printTable() {
    const table = document.getElementById('equipmentTable');
    const rows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    
    if (rows.length === 0) {
        alert('ไม่มีข้อมูลที่จะพิมพ์');
        return;
    }
    
    const printWindow = window.open('', '_blank');
    const currentDate = new Date().toLocaleDateString('th-TH');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>รายงานครุภัณฑ์</title>
                <style>
                    body { font-family: 'Sarabun', sans-serif; margin: 20px; }
                    .print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                    .print-header h2 { margin: 0; color: #333; }
                    .print-info { margin: 10px 0; font-size: 14px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8f9fa; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f8f9fa; }
                    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    .no-print { display: none; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h2>รายงานครุภัณฑ์</h2>
                    <div class="print-info">
                        <strong>วันที่พิมพ์:</strong> ${currentDate} | 
                        <strong>จำนวนรายการ:</strong> ${rows.length} รายการ
                    </div>
                </div>
                ${table.outerHTML}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 500);
                    }
                <\/script>
            </body>
        </html>
    `);
    
    printWindow.document.close();
}

// ==================== ฟังก์ชัน Import ====================

// Import Functions
function downloadTemplate() {
    // สร้าง template ข้อมูลตัวอย่าง
    const templateData = [
        ['รหัสครุภัณฑ์', 'ชื่อครุภัณฑ์', 'หมวดหมู่', 'หมวดหมู่ย่อย', 'ยี่ห้อ', 'รุ่น', 'หมายเลขซีเรียล', 'วันที่ซื้อ', 'วันที่หมดประกัน', 'ราคาซื้อ', 'ผู้จำหน่าย', 'สถานะครุภัณฑ์', 'โรงเรียน', 'ตึก/อาคาร', 'ชั้น', 'ห้อง', 'ผู้รับผิดชอบ', 'หมายเหตุ'],
        ['EQ001', 'คอมพิวเตอร์ตั้งโต๊ะ', 'คอมพิวเตอร์', 'คอมพิวเตอร์ตั้งโต๊ะ', 'Dell', 'OptiPlex 7070', 'SN123456', '2024-01-15', '2026-01-15', '25000.00', 'Dell Thailand', 'ใช้งานปกติ', 'โรงเรียนวารีเชียงใหม่', 'ตึกอำนวยการ', '2', '201', 'ครูสมชาย', 'ใช้งานในห้องพักครู'],
        ['EQ002', 'โปรเจคเตอร์', 'อุปกรณ์มัลติมีเดีย', 'โปรเจคเตอร์', 'Epson', 'EB-X06', 'SN789012', '2024-02-20', '2026-02-20', '15000.00', 'Epson Thailand', 'ใช้งานปกติ', 'โรงเรียนวารีเชียงใหม่', 'ตึกวิทยาศาสตร์', '3', '301', 'ครูสมหญิง', 'ใช้งานในห้องวิทยาศาสตร์'],
        ['EQ003', 'เครื่องพิมพ์', 'อุปกรณ์สำนักงาน', 'เครื่องพิมพ์เลเซอร์', 'HP', 'LaserJet Pro', 'SN345678', '2024-03-10', '2026-03-10', '8000.00', 'HP Thailand', 'ใช้งานปกติ', 'โรงเรียนวารีเชียงใหม่', 'ตึกอำนวยการ', '1', '101', 'ครูสมหมาย', 'ใช้งานในห้องธุรการ']
    ];
    
    let csvContent = "\uFEFF"; // BOM for UTF-8
    templateData.forEach(row => {
        csvContent += row.map(field => `"${field}"`).join(',') + '\r\n';
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'equipment_template.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Preview file before import
document.getElementById('import_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('importPreview');
    const previewHeaders = document.getElementById('previewHeaders');
    const previewBody = document.getElementById('previewBody');
    const previewInfo = document.getElementById('previewInfo');
    
    previewHeaders.innerHTML = '';
    previewBody.innerHTML = '';
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const data = e.target.result;
            const lines = data.split('\n').filter(line => line.trim() !== '');
            
            if (lines.length > 0) {
                const headers = lines[0].split(',').map(h => h.replace(/"/g, '').trim());
                
                // Create headers
                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    th.className = 'small';
                    previewHeaders.appendChild(th);
                });
                
                // Create preview rows (max 5 rows)
                const previewRowCount = Math.min(5, lines.length - 1);
                for (let i = 1; i <= previewRowCount; i++) {
                    if (lines[i]) {
                        const row = document.createElement('tr');
                        const cells = lines[i].split(',').map(c => c.replace(/"/g, '').trim());
                        
                        cells.forEach(cell => {
                            const td = document.createElement('td');
                            td.textContent = cell;
                            td.className = 'small';
                            row.appendChild(td);
                        });
                        
                        previewBody.appendChild(row);
                    }
                }
                
                previewInfo.textContent = `แสดง ${previewRowCount} จาก ${lines.length - 1} แถว`;
                preview.style.display = 'block';
            }
        };
        
        reader.readAsText(file, 'UTF-8');
    } else {
        preview.style.display = 'none';
    }
});

// Handle import form submission
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fileInput = document.getElementById('import_file');
    const submitBtn = document.getElementById('importSubmit');
    const progress = document.getElementById('importProgress');
    
    if (!fileInput.files[0]) {
        alert('กรุณาเลือกไฟล์ที่จะ Import');
        return;
    }
    
    // Show progress
    submitBtn.disabled = true;
    progress.style.display = 'block';
    
    const progressBar = progress.querySelector('.progress-bar');
    const progressText = progress.querySelector('#progressText');
    
    // Simulate progress
    let progressValue = 0;
    const progressInterval = setInterval(() => {
        progressValue += 5;
        progressBar.style.width = progressValue + '%';
        
        if (progressValue >= 90) {
            clearInterval(progressInterval);
        }
    }, 100);
    
    // Submit form
    const formData = new FormData(this);
    
    fetch('equipment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(() => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        progressText.textContent = 'Import สำเร็จ!';
        
        setTimeout(() => {
            location.reload();
        }, 1500);
    })
    .catch(error => {
        clearInterval(progressInterval);
        progressText.textContent = 'เกิดข้อผิดพลาด: ' + error;
        progressBar.classList.remove('progress-bar-animated');
        progressBar.classList.add('bg-danger');
        submitBtn.disabled = false;
    });
});

// Clear preview when modal is closed
document.getElementById('importModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('import_file').value = '';
    document.getElementById('importPreview').style.display = 'none';
    document.getElementById('importProgress').style.display = 'none';
    document.getElementById('importSubmit').disabled = false;
});

// เรียกใช้ฟังก์ชันกรองเมื่อโหลดหน้าเว็บเสร็จ
document.addEventListener('DOMContentLoaded', function() {
    filterTable(); // กรองข้อมูลครั้งแรก
});
