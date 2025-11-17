<nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary">
    <!-- Navbar Brand-->
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
        <a class="navbar-brand ps-5" href="maintenance_user.php">IT Equipment</a>
    <?php else: ?>
        <a class="navbar-brand ps-5" href="index.php">IT Equipment</a>
    <?php endif; ?>

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">                    
            
        </div>
    </form>
    
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <!-- Notification Button - สำหรับ Admin และ Technician เท่านั้น -->
        <?php if (hasPermission(['admin', 'technician'])): ?>
        <li class="nav-item dropdown me-2">
            <a class="btn btn-dark dropdown-toggle position-relative" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" style="border: none;">
                <i class="fas fa-bell fa-fw"></i>
                <?php
                // Count pending maintenance
                $pending_query = "SELECT COUNT(*) as count FROM maintenance_requests WHERE repair_status = 'รอซ่อม'";
                $pending_count = $db->query($pending_query)->fetch(PDO::FETCH_ASSOC)['count'];
                ?>
                <?php if ($pending_count > 0): ?>
                    <span class="badge bg-danger badge-counter"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php if ($pending_count > 0): ?>
                    <li><a class="dropdown-item" href="maintenance.php">มีอุปกรณ์รอซ่อม <?php echo $pending_count; ?> รายการ</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="#">ไม่มีอุปกรณ์รอซ่อม</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="maintenance.php">ดูการแจ้งเตือนทั้งหมด</a></li>
            </ul>
        </li>
        <?php endif; ?>
       
        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <!-- สำหรับ Admin และ Technician -->
                <?php if (hasPermission(['admin', 'technician'])): ?>
                <li><a class="dropdown-item" href="users.php">Settings</a></li>
                <li><a class="dropdown-item" href="#!">Activity Log</a></li>                
                <li><hr class="dropdown-divider" /></li>
                <?php endif; ?>
                
                <!-- สำหรับทุกบทบาท -->
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>