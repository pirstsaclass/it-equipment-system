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
        $stmt = $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
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
            $stmt = $db->prepare("INSERT INTO users (username, password, employee_id, role, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['username'],
                $hashed_password,
                $_POST['employee_id'],
                $_POST['role'],
                isset($_POST['is_active']) ? 1 : 0
            ]);
            $_SESSION['success'] = "เพิ่มข้อมูลผู้ใช้เรียบร้อยแล้ว";
        }
        header("Location: users.php");
        exit();
    }
    
    if (isset($_POST['edit_user'])) {
        $update_data = [
            $_POST['username'],
            $_POST['employee_id'],
            $_POST['role'],
            isset($_POST['is_active']) ? 1 : 0,
            $_POST['id']
        ];
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET username=?, password=?, employee_id=?, role=?, is_active=? WHERE id=?");
            $update_data = [
                $_POST['username'],
                $hashed_password,
                $_POST['employee_id'],
                $_POST['role'],
                isset($_POST['is_active']) ? 1 : 0,
                $_POST['id']
            ];
        } else {
            $stmt = $db->prepare("UPDATE users SET username=?, employee_id=?, role=?, is_active=? WHERE id=?");
        }
        
        $stmt->execute($update_data);
        $_SESSION['success'] = "แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว";
        header("Location: users.php");
        exit();
    }
}

// Get users list
$users_query = "SELECT u.*, e.first_name, e.last_name, e.employee_code 
    FROM users u 
    LEFT JOIN employees e ON u.employee_id = e.id 
    ORDER BY u.username";
$users_list = $db->query($users_query)->fetchAll(PDO::FETCH_ASSOC);

// Get employees for dropdown
$employees = $db->query("SELECT * FROM employees ORDER BY first_name, last_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

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
                            <th>ชื่อผู้ใช้</th>
                            <th>พนักงาน</th>
                            <th>สิทธิ์การใช้งาน</th>
                            <th>สถานะ</th>
                            <th>เข้าสู่ระบบล่าสุด</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users_list as $user): ?>
                        <tr>
                            <td><?php echo $user['username']; ?></td>
                            <td>
                                <?php if ($user['first_name']): ?>
                                    <?php echo $user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['employee_code'] . ')'; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'user' ? 'primary' : 'secondary'); ?>">
                                    <?php echo $user['role']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="users.php?action=toggle_active&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?>">
                                        <?php echo $user['is_active'] ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-info">บัญชีปัจจุบัน</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'ยังไม่เคยเข้าสู่ระบบ'; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick='editUser(<?php echo json_encode($user); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
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
                    <input type="hidden" name="id" id="user_id">
                    
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
                        <label class="form-label">พนักงาน</label>
                        <select class="form-control" name="employee_id" id="employee_id">
                            <option value="">ไม่ผูกกับพนักงาน</option>
                            <?php foreach($employees as $employee): ?>
                            <option value="<?php echo $employee['id']; ?>">
                                <?php echo $employee['employee_code'] . ' - ' . $employee['first_name'] . ' ' . $employee['last_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สิทธิ์การใช้งาน *</label>
                        <select class="form-control" name="role" id="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
                        <label class="form-check-label" for="is_active">เปิดใช้งานบัญชี</label>
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
    document.getElementById('user_id').value = '';
    document.getElementById('userModalLabel').textContent = 'เพิ่มผู้ใช้';
    document.getElementById('submitBtn').name = 'add_user';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    
    // Reset password field
    document.getElementById('password').required = true;
    document.getElementById('passwordLabel').textContent = 'รหัสผ่าน *';
    document.getElementById('passwordHelp').style.display = 'none';
    document.getElementById('is_active').checked = true;
}

function editUser(user) {
    document.getElementById('user_id').value = user.id;
    document.getElementById('username').value = user.username;
    document.getElementById('password').value = '';
    document.getElementById('employee_id').value = user.employee_id || '';
    document.getElementById('role').value = user.role;
    document.getElementById('is_active').checked = user.is_active == 1;
    
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
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>