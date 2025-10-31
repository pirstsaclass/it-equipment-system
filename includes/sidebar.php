<!-- Sidebar -->
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="position-sticky d-flex flex-column h-100">
        <!-- ส่วนเนื้อหาเมนู -->
        <div class="flex-grow-1">
            <a href="index.php" class="sidebar-brand d-flex align-items-center justify-content-center">
                <i class="fas fa-laptop-code me-2"></i>
                <span>IT Equipment</span>
            </a>
            
            <ul class="nav flex-column">
                <!-- หน้าหลัก -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        หน้าหลัก
                    </a>
                </li>
                
                <!-- จัดการข้อมูล -->
                <li class="nav-item mt-1">
                    <div class="sidebar-section-header">
                        <span class="text-white fw-bold">จัดการข้อมูล</span>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'equipment.php' ? 'active' : ''; ?>" href="equipment.php">
                        <i class="fas fa-fw fa-laptop"></i>
                        ครุภัณฑ์
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                        <i class="fas fa-fw fa-tags"></i>
                        หมวดหมู่และประเภทครุภัณฑ์
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'maintenance.php' ? 'active' : ''; ?>" href="maintenance.php">
                        <i class="fas fa-fw fa-tools"></i>
                        ระบบซ่อมบำรุง
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'equipment_classroom.php' ? 'active' : ''; ?>" href="equipment_classroom.php">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>อุปกรณ์ในห้องเรียน</span>
                    </a>
                </li>                
                
                <!-- ข้อมูลองค์กร -->
                <li class="nav-item mt-2">
                    <div class="sidebar-section-header">
                        <span class="text-white fw-bold">ข้อมูลองค์กร</span>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : ''; ?>" href="departments.php">
                        <i class="fas fa-fw fa-building"></i>
                        แผนก
                    </a>
                </li>                
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'buildingfloorplans.php' ? 'active' : ''; ?>" href="buildingfloorplans.php">
                        <i class="fas fa-fw fa-building"></i>
                        แผนผังตารางห้อง
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : ''; ?>" href="employees.php">
                        <i class="fas fa-fw fa-users"></i>
                        พนักงาน
                    </a>
                </li>
                
                <!-- รายงานและการตั้งค่า -->
                <li class="nav-item mt-2">
                    <div class="sidebar-section-header">
                        <span class="text-white fw-bold">รายงานและการตั้งค่า</span>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        รายงาน
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                        <i class="fas fa-fw fa-user-cog"></i>
                        จัดการข้อมูลผู้ใช้
                    </a>
                </li>
            </ul>
        </div>

        <!-- User Info อยู่ล่างสุด -->
        <div class="user-profile mt-auto">
            <div class="d-flex align-items-center">
                <img src="https://via.placeholder.com/45" alt="ผู้ใช้" class="user-avatar">
                <div class="user-info ms-3">
                    <div class="user-name"><?php echo $_SESSION['username']; ?></div>
                    <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
                </div>
                <a href="logout.php" class="logout-btn ms-auto" title="ออกจากระบบ">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </div>
</nav>