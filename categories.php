<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete_category' && $id) {
        // Check if category is used in equipment
        $check_stmt = $db->prepare("SELECT COUNT(*) as count FROM equipment WHERE category_id = ?");
        $check_stmt->execute([$id]);
        $used_count = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($used_count > 0) {
            $_SESSION['error'] = "ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากมีครุภัณฑ์ที่ใช้งานอยู่";
        } else {
            // Get category code first
            $stmt = $db->prepare("SELECT code FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category) {
                // Delete related categories_items first
                $delete_items_stmt = $db->prepare("DELETE FROM categories_items WHERE category_code = ?");
                $delete_items_stmt->execute([$category['code']]);
                
                // Then delete category
                $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                $_SESSION['success'] = "ลบข้อมูลหมวดหมู่และรายการอุปกรณ์เรียบร้อยแล้ว";
            }
        }
        header("Location: categories.php");
        exit();
    }
    
    if ($action == 'delete_item' && $id) {
        // Check if item is used in equipment
        $check_stmt = $db->prepare("SELECT COUNT(*) as count FROM equipment WHERE category_item_id = ?");
        $check_stmt->execute([$id]);
        $used_count = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($used_count > 0) {
            $_SESSION['error'] = "ไม่สามารถลบรายการนี้ได้ เนื่องจากมีครุภัณฑ์ที่ใช้งานอยู่";
        } else {
            $stmt = $db->prepare("DELETE FROM categories_items WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลรายการอุปกรณ์เรียบร้อยแล้ว";
        }
        header("Location: categories.php?category_code=" . ($_GET['category_code'] ?? ''));
        exit();
    }
}

// Handle Form Submissions
if ($_POST) {
    // Add/Edit Category with Items
    if (isset($_POST['save_category'])) {
        $code = $_POST['code'];
        $name = $_POST['name'];
        $items = $_POST['items'] ?? [];
        
        try {
            $db->beginTransaction();
            
            if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
                // Edit existing category
                $category_id = $_POST['category_id'];
                $old_code = $_POST['old_code'];
                
                $stmt = $db->prepare("UPDATE categories SET code=?, name=? WHERE id=?");
                $stmt->execute([$code, $name, $category_id]);
                
                // Update category_code in related items if code changed
                if ($old_code != $code) {
                    $update_items_stmt = $db->prepare("UPDATE categories_items SET category_code=? WHERE category_code=?");
                    $update_items_stmt->execute([$code, $old_code]);
                }
                
                // Update existing items and add new items
                foreach ($items as $item) {
                    if (!empty($item['name'])) {
                        if (isset($item['id']) && !empty($item['id'])) {
                            // Update existing item
                            $stmt = $db->prepare("UPDATE categories_items SET name=? WHERE id=? AND category_code=?");
                            $stmt->execute([$item['name'], $item['id'], $code]);
                        } else {
                            // Add new item
                            $stmt = $db->prepare("INSERT INTO categories_items (category_code, name) VALUES (?, ?)");
                            $stmt->execute([$code, $item['name']]);
                        }
                    }
                }
                
                $_SESSION['success'] = "แก้ไขข้อมูลหมวดหมู่และรายการอุปกรณ์เรียบร้อยแล้ว";
            } else {
                // Add new category
                // Generate code if not provided
                if (empty($code)) {
                    $max_code_stmt = $db->query("SELECT MAX(CAST(code AS UNSIGNED)) as max_code FROM categories WHERE code REGEXP '^[0-9]+$'");
                    $max_code = $max_code_stmt->fetch(PDO::FETCH_ASSOC)['max_code'] ?: 0;
                    $code = str_pad($max_code + 1, 2, '0', STR_PAD_LEFT);
                }
                
                $stmt = $db->prepare("INSERT INTO categories (code, name) VALUES (?, ?)");
                $stmt->execute([$code, $name]);
                $category_id = $db->lastInsertId();
                
                // Add items
                foreach ($items as $item) {
                    if (!empty($item['name'])) {
                        $stmt = $db->prepare("INSERT INTO categories_items (category_code, name) VALUES (?, ?)");
                        $stmt->execute([$code, $item['name']]);
                    }
                }
                
                $_SESSION['success'] = "เพิ่มข้อมูลหมวดหมู่และรายการอุปกรณ์เรียบร้อยแล้ว";
            }
            
            $db->commit();
            header("Location: categories.php?category_code=" . $code);
            exit();
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            header("Location: categories.php");
            exit();
        }
    }
}

// Get categories list with item counts
$categories_query = "
    SELECT c.*, COUNT(ci.id) as item_count 
    FROM categories c 
    LEFT JOIN categories_items ci ON c.code = ci.category_code AND ci.is_active = TRUE
    GROUP BY c.id 
    ORDER BY c.code";
$categories_list = $db->query($categories_query)->fetchAll(PDO::FETCH_ASSOC);

// Get categories_items for selected category
$selected_category_code = isset($_GET['category_code']) ? $_GET['category_code'] : ($categories_list[0]['code'] ?? null);
$selected_category = null;
$categories_items = [];

if ($selected_category_code) {
    // Get category details
    $stmt = $db->prepare("SELECT * FROM categories WHERE code = ?");
    $stmt->execute([$selected_category_code]);
    $selected_category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get items for this category
    $items_query = "
        SELECT ci.*, c.name as category_name, c.code as category_code
        FROM categories_items ci 
        JOIN categories c ON ci.category_code = c.code 
        WHERE ci.category_code = ? 
        ORDER BY ci.sort_order, ci.name";
    $stmt = $db->prepare($items_query);
    $stmt->execute([$selected_category_code]);
    $categories_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if we're in edit mode
$edit_mode = isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']);
$edit_category = null;

if ($edit_mode) {
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get items for edit category
    $items_stmt = $db->prepare("SELECT * FROM categories_items WHERE category_code = ? ORDER BY sort_order, name");
    $items_stmt->execute([$edit_category['code']]);
    $categories_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการหมวดหมู่และรายการอุปกรณ์</h1>
        <div>
            <?php if (!$edit_mode): ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="fas fa-plus"></i> เพิ่มหมวดหมู่ใหม่
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ฟอร์มเพิ่ม/แก้ไขหมวดหมู่ -->
    <?php if ($edit_mode): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-edit me-2"></i>แก้ไขหมวดหมู่และรายการอุปกรณ์
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" id="categoryForm">
                    <input type="hidden" name="category_id" value="<?php echo $edit_category['id']; ?>">
                    <input type="hidden" name="old_code" value="<?php echo $edit_category['code']; ?>">
                    
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label fw-bold">รหัสหมวดหมู่ *</label>
                                <input type="text" class="form-control" name="code" 
                                       value="<?php echo $edit_category['code']; ?>" required
                                       placeholder="เช่น 01, 02" maxlength="2" pattern="[0-9]{2}">
                                <div class="form-text">รหัสประจำหมวดหมู่ (2 หลัก)</div>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อหมวดหมู่ *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="<?php echo $edit_category['name']; ?>" required
                                       placeholder="เช่น อุปกรณ์คอมพิวเตอร์">
                            </div>
                        </div>
                    </div>

                    <!-- รายการอุปกรณ์ -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold">รายการอุปกรณ์</label>
                            <button type="button" class="btn btn-success btn-sm" onclick="addItemRow()">
                                <i class="fas fa-plus me-1"></i> เพิ่มรายการ
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="90%">ชื่อรายการอุปกรณ์ *</th>
                                        <th width="5%">ลบ</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    <?php if (count($categories_items) > 0): ?>
                                        <?php foreach($categories_items as $index => $item): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $index + 1; ?></td>
                                            <td>
                                                <input type="hidden" name="items[<?php echo $index; ?>][id]" value="<?php echo $item['id']; ?>">
                                                <input type="text" class="form-control" name="items[<?php echo $index; ?>][name]" 
                                                       value="<?php echo $item['name']; ?>" required
                                                       placeholder="เช่น เครื่องคอมพิวเตอร์ตั้งโต๊ะ (PC)">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="noItemsRow">
                                            <td colspan="3" class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle me-2"></i>ยังไม่มีรายการอุปกรณ์
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="categories.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> ยกเลิก
                        </a>
                        <button type="submit" name="save_category" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- แสดงรายการหมวดหมู่ -->
        <div class="row">
            <!-- Sidebar - Categories List -->
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h5 class="m-0 font-weight-bold">หมวดหมู่ทั้งหมด</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach($categories_list as $category): ?>
                            <a href="categories.php?category_code=<?php echo $category['code']; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $selected_category_code == $category['code'] ? 'active' : ''; ?>">
                                <div>
                                    <strong class="text-primary"><?php echo $category['code']; ?></strong> - <?php echo $category['name']; ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $category['item_count']; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content - Categories Items -->
            <div class="col-md-8">
                <?php if ($selected_category_code): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-list me-2"></i>
                                รายการอุปกรณ์ - <?php echo $selected_category['code'] ?? '' ?> <?php echo $selected_category['name'] ?? 'ไม่พบหมวดหมู่' ?>
                            </h6>
                            <div>
                                <a href="categories.php?action=edit&id=<?php echo $selected_category['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> แก้ไข
                                </a>
                                <a href="categories.php?action=delete_category&id=<?php echo $selected_category['id']; ?>" 
                                   class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่นี้?')">
                                    <i class="fas fa-trash me-1"></i> ลบ
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (count($categories_items) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="10%">#</th>
                                                <th width="80%">ชื่อรายการอุปกรณ์</th>
                                                <th width="10%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($categories_items as $index => $item): ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?php echo $index + 1; ?></td>
                                                <td class="fw-bold"><?php echo $item['name']; ?></td>
                                                <td class="text-center">
                                                    <a href="categories.php?action=delete_item&id=<?php echo $item['id']; ?>&category_code=<?php echo $selected_category_code; ?>" 
                                                       class="btn btn-danger btn-sm" title="ลบ" onclick="return confirm('คุณแน่ใจหรือไม่?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">ไม่มีรายการอุปกรณ์ในหมวดหมู่นี้</h5>
                                    <p class="text-muted">คลิกปุ่ม "แก้ไข" เพื่อเพิ่มรายการอุปกรณ์</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">กรุณาเลือกหมวดหมู่เพื่อดูรายการอุปกรณ์</h5>
                        <p class="text-muted">หรือคลิกปุ่ม "เพิ่มหมวดหมู่ใหม่" เพื่อสร้างหมวดหมู่แรก</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<!-- Modal สำหรับเพิ่มหมวดหมู่ใหม่ -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">
                    <i class="fas fa-plus me-2"></i>เพิ่มหมวดหมู่ใหม่
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="newCategoryForm">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">รหัสหมวดหมู่</label>
                                <input type="text" class="form-control" name="code" 
                                       placeholder="เช่น 01, 02 (เว้นว่างเพื่อสร้างอัตโนมัติ)" 
                                       maxlength="2" pattern="[0-9]{2}">
                                <div class="form-text">รหัสประจำหมวดหมู่ (2 หลัก)</div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อหมวดหมู่ *</label>
                                <input type="text" class="form-control" name="name" required
                                       placeholder="เช่น อุปกรณ์คอมพิวเตอร์">
                            </div>
                        </div>
                    </div>

                    <!-- รายการอุปกรณ์สำหรับหมวดหมู่ใหม่ -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold">รายการอุปกรณ์ (สามารถเพิ่มภายหลังได้)</label>
                            <button type="button" class="btn btn-success btn-sm" onclick="addNewItemRow()">
                                <i class="fas fa-plus me-1"></i> เพิ่มรายการ
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="newItemsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="90%">ชื่อรายการอุปกรณ์</th>
                                        <th width="5%">ลบ</th>
                                    </tr>
                                </thead>
                                <tbody id="newItemsTableBody">
                                    <tr id="noNewItemsRow">
                                        <td colspan="3" class="text-center text-muted py-3">
                                            <i class="fas fa-info-circle me-2"></i>ยังไม่มีรายการอุปกรณ์
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="save_category" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> บันทึกหมวดหมู่
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemCount = <?php echo count($categories_items); ?>;
let newItemCount = 0;

// Functions for edit form
function addItemRow() {
    const tbody = document.getElementById('itemsTableBody');
    const noItemsRow = document.getElementById('noItemsRow');
    
    if (noItemsRow) {
        noItemsRow.remove();
    }
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td class="text-center">${tbody.children.length + 1}</td>
        <td>
            <input type="text" class="form-control" name="items[${itemCount}][name]" required
                   placeholder="เช่น เครื่องคอมพิวเตอร์ตั้งโต๊ะ (PC)">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    itemCount++;
}

function removeItemRow(button) {
    const row = button.closest('tr');
    row.remove();
    
    // Update row numbers
    const tbody = document.getElementById('itemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length === 0) {
        tbody.innerHTML = `
            <tr id="noItemsRow">
                <td colspan="3" class="text-center text-muted py-3">
                    <i class="fas fa-info-circle me-2"></i>ยังไม่มีรายการอุปกรณ์
                </td>
            </tr>
        `;
    } else {
        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1;
        });
    }
}

// Functions for new category form
function addNewItemRow() {
    const tbody = document.getElementById('newItemsTableBody');
    const noItemsRow = document.getElementById('noNewItemsRow');
    
    if (noItemsRow) {
        noItemsRow.remove();
    }
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td class="text-center">${tbody.children.length + 1}</td>
        <td>
            <input type="text" class="form-control" name="items[${newItemCount}][name]"
                   placeholder="เช่น เครื่องคอมพิวเตอร์ตั้งโต๊ะ (PC)">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeNewItemRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    newItemCount++;
}

function removeNewItemRow(button) {
    const row = button.closest('tr');
    row.remove();
    
    // Update row numbers
    const tbody = document.getElementById('newItemsTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length === 0) {
        tbody.innerHTML = `
            <tr id="noNewItemsRow">
                <td colspan="3" class="text-center text-muted py-3">
                    <i class="fas fa-info-circle me-2"></i>ยังไม่มีรายการอุปกรณ์
                </td>
            </tr>
        `;
    } else {
        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1;
        });
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const nameInput = this.querySelector('input[name="name"]');
            if (!nameInput.value.trim()) {
                e.preventDefault();
                alert('กรุณากรอกชื่อหมวดหมู่');
                nameInput.focus();
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>