<?php
// Function to get equipment status options
function getEquipmentStatusOptions() {
    return [
        'ใหม่' => 'ใหม่',
        'ใช้งานปกติ' => 'ใช้งานปกติ',
        'ชำรุด' => 'ชำรุด',
        'รอซ่อม' => 'รอซ่อม',
        'กำลังซ่อม' => 'กำลังซ่อม',
        'ซ่อมเสร็จแล้ว' => 'ซ่อมเสร็จแล้ว',
        'ส่งคืนแล้ว' => 'ส่งคืนแล้ว',
        'จำหน่ายแล้ว' => 'จำหน่ายแล้ว'
    ];
}

// Function to get status badge class
function getStatusBadgeClass($status) {
    $badge_classes = [
        'ใหม่' => 'success',
        'ใช้งานปกติ' => 'primary',
        'ชำรุด' => 'warning',
        'รอซ่อม' => 'info',
        'กำลังซ่อม' => 'warning',
        'ซ่อมเสร็จแล้ว' => 'success',
        'ส่งคืนแล้ว' => 'primary',
        'จำหน่ายแล้ว' => 'danger'
    ];
    
    return $badge_classes[$status] ?? 'secondary';
}
?>