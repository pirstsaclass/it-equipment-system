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
            // Delete related subcategories first
            $delete_subcategories_stmt = $db->prepare("DELETE FROM equipment_subcategories WHERE category_id = ?");
            $delete_subcategories_stmt->execute([$id]);
            
            // Then delete category
            $stmt = $db->prepare("DELETE FROM equipment_categories WHERE category_id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลหมวดหมู่และรายการย่อยเรียบร้อยแล้ว";
        }
        header("Location: categories.php");
        exit();
    }
    
    if ($action == 'delete_subcategory' && $id) {
        // Check if subcategory is used in equipment
        $check_stmt = $db->prepare("SELECT COUNT(*) as count FROM equipment WHERE subcategory_id = ?");
        $check_stmt->execute([$id]);
        $used_count = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($used_count > 0) {
            $_SESSION['error'] = "ไม่สามารถลบหมวดหมู่ย่อยนี้ได้ เนื่องจากมีครุภัณฑ์ที่ใช้งานอยู่";
        } else {
            $stmt = $db->prepare("DELETE FROM equipment_subcategories WHERE subcategory_id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลหมวดหมู่ย่อยเรียบร้อยแล้ว";
        }
        header("Location: categories.php?category_id=" . ($_GET['category_id'] ?? ''));
        exit();
    }
}

// Handle Form Submissions
if ($_POST) {
    // Add/Edit Category with Subcategories
    if (isset($_POST['save_category'])) {
        $category_name = $_POST['category_name'];
        $category_description = $_POST['category_description'] ?? '';
        $subcategories = $_POST['subcategories'] ?? [];
        
        try {
            $db->beginTransaction();
            
            if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
                // Edit existing category
                $category_id = $_POST['category_id'];
                
                $stmt = $db->prepare("UPDATE equipment_categories SET category_name=?, category_description=? WHERE category_id=?");
                $stmt->execute([$category_name, $category_description, $category_id]);
                
                // Update existing subcategories and add new subcategories
                foreach ($subcategories as $subcategory) {
                    if (!empty($subcategory['name'])) {
                        if (isset($subcategory['id']) && !empty($subcategory['id'])) {
                            // Update existing subcategory
                            $stmt = $db->prepare("UPDATE equipment_subcategories SET subcategory_name=?, subcategory_description=? WHERE subcategory_id=? AND category_id=?");
                            $stmt->execute([$subcategory['name'], $subcategory['description'] ?? '', $subcategory['id'], $category_id]);
                        } else {
                            // Add new subcategory
                            $stmt = $db->prepare("INSERT INTO equipment_subcategories (category_id, subcategory_name, subcategory_description) VALUES (?, ?, ?)");
                            $stmt->execute([$category_id, $subcategory['name'], $subcategory['description'] ?? '']);
                        }
                    }
                }
                
                $_SESSION['success'] = "แก้ไขข้อมูลหมวดหมู่และหมวดหมู่ย่อยเรียบร้อยแล้ว";
            } else {
                // Add new category
                $stmt = $db->prepare("INSERT INTO equipment_categories (category_name, category_description) VALUES (?, ?)");
                $stmt->execute([$category_name, $category_description]);
                $category_id = $db->lastInsertId();
                
                // Add subcategories
                foreach ($subcategories as $subcategory) {
                    if (!empty($subcategory['name'])) {
                        $stmt = $db->prepare("INSERT INTO equipment_subcategories (category_id, subcategory_name, subcategory_description) VALUES (?, ?, ?)");
                        $stmt->execute([$category_id, $subcategory['name'], $subcategory['description'] ?? '']);
                    }
                }
                
                $_SESSION['success'] = "เพิ่มข้อมูลหมวดหมู่และหมวดหมู่ย่อยเรียบร้อยแล้ว";
            }
            
            $db->commit();
            header("Location: categories.php?category_id=" . $category_id);
            exit();
            
        } catch (Exception $e) {
            $db->rollBack();
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            header("Location: categories.php");
            exit();
        }
    }
}

// Get categories list with subcategory counts
$categories_query = "
    SELECT ec.*, COUNT(es.subcategory_id) as subcategory_count 
    FROM equipment_categories ec 
    LEFT JOIN equipment_subcategories es ON ec.category_id = es.category_id
    GROUP BY ec.category_id 
    ORDER BY ec.category_name";
$categories_list = $db->query($categories_query)->fetchAll(PDO::FETCH_ASSOC);

// Get subcategories for selected category
$selected_category_id = isset($_GET['category_id']) ? $_GET['category_id'] : ($categories_list[0]['category_id'] ?? null);
$selected_category = null;
$subcategories = [];

if ($selected_category_id) {
    // Get category details
    $stmt = $db->prepare("SELECT * FROM equipment_categories WHERE category_id = ?");
    $stmt->execute([$selected_category_id]);
    $selected_category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get subcategories for this category
    $subcategories_query = "
        SELECT es.*, ec.category_name
        FROM equipment_subcategories es 
        JOIN equipment_categories ec ON es.category_id = ec.category_id 
        WHERE es.category_id = ? 
        ORDER BY es.subcategory_name";
    $stmt = $db->prepare($subcategories_query);
    $stmt->execute([$selected_category_id]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if we're in edit mode
$edit_mode = isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']);
$edit_category = null;

if ($edit_mode) {
    $stmt = $db->prepare("SELECT * FROM equipment_categories WHERE category_id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get subcategories for edit category
    $subcategories_stmt = $db->prepare("SELECT * FROM equipment_subcategories WHERE category_id = ? ORDER BY subcategory_name");
    $subcategories_stmt->execute([$edit_category['category_id']]);
    $subcategories = $subcategories_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการหมวดหมู่และหมวดหมู่ย่อยครุภัณฑ์</h1>
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
                    <i class="fas fa-edit me-2"></i>แก้ไขหมวดหมู่และหมวดหมู่ย่อย
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" id="categoryForm">
                    <input type="hidden" name="category_id" value="<?php echo $edit_category['category_id']; ?>">
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อหมวดหมู่ *</label>
                                <input type="text" class="form-control" name="category_name" 
                                       value="<?php echo $edit_category['category_name']; ?>" required
                                       placeholder="เช่น อุปกรณ์คอมพิวเตอร์">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">คำอธิบายหมวดหมู่</label>
                                <textarea class="form-control" name="category_description" rows="2"
                                       placeholder="รายละเอียดเพิ่มเติมเกี่ยวกับหมวดหมู่"><?php echo $edit_category['category_description']; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- รายการหมวดหมู่ย่อย -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold">หมวดหมู่ย่อย</label>
                            <button type="button" class="btn btn-success btn-sm" onclick="addSubcategoryRow()">
                                <i class="fas fa-plus me-1"></i> เพิ่มหมวดหมู่ย่อย
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="subcategoriesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="45%">ชื่อหมวดหมู่ย่อย *</th>
                                        <th width="45%">คำอธิบาย</th>
                                        <th width="5%">ลบ</th>
                                    </tr>
                                </thead>
                                <tbody id="subcategoriesTableBody">
                                    <?php if (count($subcategories) > 0): ?>
                                        <?php foreach($subcategories as $index => $subcategory): ?>
                                        <tr>
                                            <td class="text-center"><?php echo $index + 1; ?></td>
                                            <td>
                                                <input type="hidden" name="subcategories[<?php echo $index; ?>][id]" value="<?php echo $subcategory['subcategory_id']; ?>">
                                                <input type="text" class="form-control" name="subcategories[<?php echo $index; ?>][name]" 
                                                       value="<?php echo $subcategory['subcategory_name']; ?>" required
                                                       placeholder="เช่น เครื่องคอมพิวเตอร์ตั้งโต๊ะ">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="subcategories[<?php echo $index; ?>][description]" 
                                                       value="<?php echo $subcategory['subcategory_description']; ?>"
                                                       placeholder="คำอธิบายหมวดหมู่ย่อย">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeSubcategoryRow(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr id="noSubcategoriesRow">
                                            <td colspan="4" class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle me-2"></i>ยังไม่มีหมวดหมู่ย่อย
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
                            <a href="categories.php?category_id=<?php echo $category['category_id']; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $selected_category_id == $category['category_id'] ? 'active' : ''; ?>">
                                <div>
                                    <strong><?php echo $category['category_name']; ?></strong>
                                    <?php if ($category['category_description']): ?>
                                        <br><small class="text-muted"><?php echo $category['category_description']; ?></small>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-primary rounded-pill"><?php echo $category['subcategory_count']; ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content - Subcategories -->
            <div class="col-md-8">
                <?php if ($selected_category_id): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-list me-2"></i>
                                หมวดหมู่ย่อย - <?php echo $selected_category['category_name'] ?? 'ไม่พบหมวดหมู่' ?>
                            </h6>
                            <div>
                                <a href="categories.php?action=edit&id=<?php echo $selected_category['category_id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> แก้ไข
                                </a>
                                <a href="categories.php?action=delete_category&id=<?php echo $selected_category['category_id']; ?>" 
                                   class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่นี้? การลบจะทำให้หมวดหมู่ย่อยทั้งหมดถูกลบด้วย')">
                                    <i class="fas fa-trash me-1"></i> ลบ
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($selected_category['category_description']): ?>
                                <div class="alert alert-info">
                                    <strong>คำอธิบาย:</strong> <?php echo $selected_category['category_description']; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (count($subcategories) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="10%">#</th>
                                                <th width="45%">ชื่อหมวดหมู่ย่อย</th>
                                                <th width="40%">คำอธิบาย</th>
                                                <th width="5%">จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($subcategories as $index => $subcategory): ?>
                                            <tr>
                                                <td class="text-center fw-bold"><?php echo $index + 1; ?></td>
                                                <td class="fw-bold"><?php echo $subcategory['subcategory_name']; ?></td>
                                                <td><?php echo $subcategory['subcategory_description'] ?: '-'; ?></td>
                                                <td class="text-center">
                                                    <a href="categories.php?action=delete_subcategory&id=<?php echo $subcategory['subcategory_id']; ?>&category_id=<?php echo $selected_category_id; ?>" 
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
                                    <h5 class="text-muted">ไม่มีหมวดหมู่ย่อยในหมวดหมู่นี้</h5>
                                    <p class="text-muted">คลิกปุ่ม "แก้ไข" เพื่อเพิ่มหมวดหมู่ย่อย</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">กรุณาเลือกหมวดหมู่เพื่อดูหมวดหมู่ย่อย</h5>
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
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">ชื่อหมวดหมู่ *</label>
                                <input type="text" class="form-control" name="category_name" required
                                       placeholder="เช่น อุปกรณ์คอมพิวเตอร์">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold">คำอธิบายหมวดหมู่</label>
                                <textarea class="form-control" name="category_description" rows="2"
                                       placeholder="รายละเอียดเพิ่มเติมเกี่ยวกับหมวดหมู่"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- หมวดหมู่ย่อยสำหรับหมวดหมู่ใหม่ -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-bold">หมวดหมู่ย่อย (สามารถเพิ่มภายหลังได้)</label>
                            <button type="button" class="btn btn-success btn-sm" onclick="addNewSubcategoryRow()">
                                <i class="fas fa-plus me-1"></i> เพิ่มหมวดหมู่ย่อย
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="newSubcategoriesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="45%">ชื่อหมวดหมู่ย่อย</th>
                                        <th width="45%">คำอธิบาย</th>
                                        <th width="5%">ลบ</th>
                                    </tr>
                                </thead>
                                <tbody id="newSubcategoriesTableBody">
                                    <tr id="noNewSubcategoriesRow">
                                        <td colspan="4" class="text-center text-muted py-3">
                                            <i class="fas fa-info-circle me-2"></i>ยังไม่มีหมวดหมู่ย่อย
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
let subcategoryCount = <?php echo count($subcategories); ?>;
let newSubcategoryCount = 0;

// Functions for edit form
function addSubcategoryRow() {
    const tbody = document.getElementById('subcategoriesTableBody');
    const noSubcategoriesRow = document.getElementById('noSubcategoriesRow');
    
    if (noSubcategoriesRow) {
        noSubcategoriesRow.remove();
    }
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td class="text-center">${tbody.children.length + 1}</td>
        <td>
            <input type="text" class="form-control" name="subcategories[${subcategoryCount}][name]" required
                   placeholder="เช่น เครื่องคอมพิวเตอร์ตั้งโต๊ะ">
        </td>
        <td>
            <input type="text" class="form-control" name="subcategories[${subcategoryCount}][description]"
                   placeholder="คำอธิบายหมวดหมู่ย่อย">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeSubcategoryRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    subcategoryCount++;
}

function removeSubcategoryRow(button) {
    const row = button.closest('tr');
    row.remove();
    
    // Update row numbers
    const tbody = document.getElementById('subcategoriesTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length === 0) {
        tbody.innerHTML = `
            <tr id="noSubcategoriesRow">
                <td colspan="4" class="text-center text-muted py-3">
                    <i class="fas fa-info-circle me-2"></i>ยังไม่มีหมวดหมู่ย่อย
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
function addNewSubcategoryRow() {
    const tbody = document.getElementById('newSubcategoriesTableBody');
    const noSubcategoriesRow = document.getElementById('noNewSubcategoriesRow');
    
    if (noSubcategoriesRow) {
        noSubcategoriesRow.remove();
    }
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td class="text-center">${tbody.children.length + 1}</td>
        <td>
            <input type="text" class="form-control" name="subcategories[${newSubcategoryCount}][name]"
                   placeholder="เช่น เครื่องคอมพิวเตอร์ตั้งโต๊ะ">
        </td>
        <td>
            <input type="text" class="form-control" name="subcategories[${newSubcategoryCount}][description]"
                   placeholder="คำอธิบายหมวดหมู่ย่อย">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeNewSubcategoryRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    newSubcategoryCount++;
}

function removeNewSubcategoryRow(button) {
    const row = button.closest('tr');
    row.remove();
    
    // Update row numbers
    const tbody = document.getElementById('newSubcategoriesTableBody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length === 0) {
        tbody.innerHTML = `
            <tr id="noNewSubcategoriesRow">
                <td colspan="4" class="text-center text-muted py-3">
                    <i class="fas fa-info-circle me-2"></i>ยังไม่มีหมวดหมู่ย่อย
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
            const nameInput = this.querySelector('input[name="category_name"]');
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