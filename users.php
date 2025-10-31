<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        // Prevent deleting own account
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = "ไม่สามารถลบบัญชีของตัวเองได้";
        } else {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลผู้ใช้เรียบร้อยแล้ว";
        }
        header("Location: users.php");
        exit();
    }
    
    if ($action == 'toggle_active' && $id) {
        $stmt = $db->prepare("UPDATE users SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "อัพเดทสถานะผู้ใช้เรียบร้อยแล้ว";
        header("Location: users.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_user'])) {
        // Check if username exists
        $check_stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
        $check_stmt->execute([$_POST['username']]);
        $user_exists = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($user_exists > 0) {
            $_SESSION['error'] = "ชื่อผู้ใช้นี้มีอยู่ในระบบแล้ว";
        } else {
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, password, employee_id, full_name, role, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([
                $_POST['username'],
                $hashed_password,
                $_POST['employee_id'],               
                $_POST['full_name'],                
                $_POST['role']
            ]);
            $_SESSION['success'] = "เพิ่มข้อมูลผู้ใช้เรียบร้อยแล้ว";
        }
        header("Location: users.php");
        exit();
    }
    
    if (isset($_POST['edit_user'])) {
        // Update password if provided
        if (!empty($_POST['password'])) {
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET username=?, password=?, employee_id=?, full_name=?, role=? WHERE id=?");
            $stmt->execute([
                $_POST['username'],
                $hashed_password,
                $_POST['employee_id'],
                $_POST['full_name'],
                $_POST['role'],
                $_POST['id']
            ]);
        } else {
            $stmt = $db->prepare("UPDATE users SET username=?, employee_id=?, full_name=?, role=? WHERE id=?");
            $stmt->execute([
                $_POST['username'],
                $_POST['employee_id'],
                $_POST['full_name'],
                $_POST['role'],
                $_POST['id']
            ]);
        }
        
        $_SESSION['success'] = "แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว";
        header("Location: users.php");
        exit();
    }
}

// Get users list
$users_query = "SELECT u.* FROM users u ORDER BY u.id";
$users_list = $db->query($users_query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการข้อมูลผู้ใช้</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> เพิ่มผู้ใช้
        </button>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายการผู้ใช้ทั้งหมด</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>ชื่อผู้ใช้</th>                            
                            <th>รหัสพนักงาน</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th>สิทธิ์การใช้งาน</th>
                            <th>สถานะ</th>
                            <th>เข้าใช้ล่าสุด</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php foreach($users_list as $user): ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['employee_id']; ?></td>
                            <td><?php echo $user['full_name']; ?></td>
                            <td>
                                <?php 
                                $role_badge = [
                                    'admin' => 'danger',
                                    'user' => 'primary',
                                    'technician' => 'warning'
                                ];
                                $current_role = $user['role'];
                                $badge_color = isset($role_badge[$current_role]) ? $role_badge[$current_role] : 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $badge_color; ?>">
                                    <?php 
                                    $role_names = [
                                        'admin' => 'ผู้ดูแลระบบ',
                                        'user' => 'ผู้ใช้งาน',
                                        'technician' => 'ช่างซ่อม'
                                    ];
                                    echo isset($role_names[$current_role]) ? $role_names[$current_role] : $current_role;
                                    ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $user['is_active'] ? 'ใช้งาน' : 'ปิดใช้งาน'; ?>
                                </span>
                            </td>
                            <td><?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'ยังไม่เคยเข้าใช้'; ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick='editUser(<?php echo json_encode($user); ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['id']): ?>
                                        <a href="users.php?action=toggle_active&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>" title="<?php echo $user['is_active'] ? 'ปิดใช้งาน' : 'เปิดใช้งาน'; ?>">
                                            <i class="fas fa-<?php echo $user['is_active'] ? 'pause' : 'play'; ?>"></i>
                                        </a>
                                        <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">เพิ่มผู้ใช้</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="userForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อผู้ใช้ *</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" id="passwordLabel">รหัสผ่าน *</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                        <small class="text-muted" id="passwordHelp" style="display: none;">เว้นว่างหากไม่ต้องการเปลี่ยนรหัสผ่าน</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">รหัสพนักงาน *</label>
                        <input type="text" class="form-control" name="employee_id" id="employee_id" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อ-นามสกุล *</label>
                        <input type="text" class="form-control" name="full_name" id="full_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สิทธิ์การใช้งาน *</label>
                        <select class="form-control" name="role" id="role" required>
                            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                            <option value="user">ผู้ใช้งาน (User)</option>
                            <option value="technician">ช่างซ่อม (Technician)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_user" id="submitBtn" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearForm() {
    document.getElementById('userForm').reset();
    document.getElementById('id').value = '';
    document.getElementById('userModalLabel').textContent = 'เพิ่มผู้ใช้';
    document.getElementById('submitBtn').name = 'add_user';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    
    // Reset password field
    document.getElementById('password').required = true;
    document.getElementById('passwordLabel').textContent = 'รหัสผ่าน *';
    document.getElementById('passwordHelp').style.display = 'none';
}

function editUser(user) {
    document.getElementById('id').value = user.id;
    document.getElementById('username').value = user.username;
    document.getElementById('password').value = '';
    document.getElementById('employee_id').value = user.employee_id;
    document.getElementById('full_name').value = user.full_name;
    document.getElementById('role').value = user.role;
    
    document.getElementById('userModalLabel').textContent = 'แก้ไขข้อมูลผู้ใช้';
    document.getElementById('submitBtn').name = 'edit_user';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
    
    // Make password optional in edit mode
    document.getElementById('password').required = false;
    document.getElementById('passwordLabel').textContent = 'รหัสผ่าน';
    document.getElementById('passwordHelp').style.display = 'block';
}

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        order: [[0, 'asc']], // เรียงตามคอลัมน์ลำดับ
        columnDefs: [
            { orderable: false, targets: [6] } // ปิดการเรียงลำดับคอลัมน์จัดการ (เปลี่ยนจาก 7 เป็น 6)
        ],
        pageLength: 25,
        responsive: true
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>