<nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary" style="box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2), 0 1px 5px rgba(0, 0, 0, 0.15);">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="index.php">IT Equipment</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        <div class="input-group">                    
            
        </div>
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        
        <!-- Notification Button -->
        <li class="nav-item dropdown me-2">
            <a class="btn btn-dark dropdown-toggle position-relative" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" style="border: none; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);">
                <i class="fas fa-bell fa-fw"></i> อุปกรณ์รอซ่อม
                <?php
                // Count pending maintenance
                $pending_query = "SELECT COUNT(*) as count FROM maintenance_requests WHERE repair_status = 'รอซ่อม'";
                $pending_count = $db->query($pending_query)->fetch(PDO::FETCH_ASSOC)['count'];
                ?>
                <?php if ($pending_count > 0): ?>
                    <span class="badge bg-danger ms-1" style="box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.1);">
                <?php if ($pending_count > 0): ?>
                    <li><a class="dropdown-item" href="maintenance.php">มีอุปกรณ์รอซ่อม <?php echo $pending_count; ?> รายการ</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="#">ไม่มีอุปกรณ์รอซ่อม</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="maintenance.php">ดูการแจ้งเตือนทั้งหมด</a></li>
            </ul>
        </li>
        
        <!-- User Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.1);">
                <li><a class="dropdown-item" href="users.php">Settings</a></li>
                <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<style>
/* เพิ่มเอฟเฟกต์เงาและ Animation ให้ Navbar */
.sb-topnav {
    box-shadow: 
        0 2px 15px rgba(0, 0, 0, 0.2),
        0 1px 5px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.sb-topnav:hover {
    box-shadow: 
        0 3px 20px rgba(0, 0, 0, 0.25),
        0 2px 8px rgba(0, 0, 0, 0.18),
        inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
}

/* เงาให้กับปุ่ม Notification */
.navbar-nav .btn-dark {
    box-shadow: 
        0 2px 6px rgba(0, 0, 0, 0.2),
        0 1px 3px rgba(0, 0, 0, 0.15) !important;
    transition: all 0.2s ease;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.navbar-nav .btn-dark:hover {
    box-shadow: 
        0 4px 12px rgba(0, 0, 0, 0.25),
        0 2px 6px rgba(0, 0, 0, 0.2) !important;
    transform: translateY(-1px);
}

/* เงาให้กับ Badge */
.badge {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

/* เงาให้กับ Dropdown Menus */
.dropdown-menu {
    box-shadow: 
        0 4px 20px rgba(0, 0, 0, 0.15),
        0 2px 8px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    margin-top: 5px !important;
}

.dropdown-item {
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: rgba(0, 123, 255, 0.1);
    box-shadow: inset 2px 0 5px rgba(0, 123, 255, 0.2);
}

/* เงาให้กับ User Icon */
.nav-link.dropdown-toggle {
    box-shadow: 
        0 2px 4px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.nav-link.dropdown-toggle:hover {
    box-shadow: 
        0 4px 8px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
    transform: translateY(-1px);
}

/* เงาให้กับ Navbar Brand */
.navbar-brand {
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    font-weight: 600;
    transition: all 0.3s ease;
}

.navbar-brand:hover {
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
    transform: translateX(2px);
}

/* เงาให้กับ Sidebar Toggle Button */
#sidebarToggle {
    box-shadow: 
        0 2px 4px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
    border-radius: 4px;
    transition: all 0.3s ease;
}

#sidebarToggle:hover {
    box-shadow: 
        0 3px 8px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
    transform: translateY(-1px);
}

/* เพิ่มเงาเมื่อ Scroll */
.scrolled-nav {
    box-shadow: 
        0 4px 25px rgba(0, 0, 0, 0.25),
        0 2px 10px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
}

/* Animation สำหรับ badge เมื่อมี notification ใหม่ */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

.badge.bg-danger {
    animation: pulse 2s infinite;
}
</style>

<script>
// เพิ่มเอฟเฟกต์เมื่อ scroll
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.sb-topnav');
    if (window.scrollY > 10) {
        navbar.classList.add('scrolled-nav');
    } else {
        navbar.classList.remove('scrolled-nav');
    }
});
</script>