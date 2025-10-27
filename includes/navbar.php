<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell fa-fw"></i>
                        <?php
                        // Count pending maintenance
                        $pending_query = "SELECT COUNT(*) as count FROM maintenance WHERE status = 'รอซ่อม'";
                        $pending_count = $db->query($pending_query)->fetch(PDO::FETCH_ASSOC)['count'];
                        ?>
                        <?php if ($pending_count > 0): ?>
                            <span class="badge bg-danger"><?php echo $pending_count; ?></span>
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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user fa-fw"></i> <?php echo $_SESSION['username']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw"></i> โปรไฟล์</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw"></i> ตั้งค่า</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw"></i> ออกจากระบบ</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>