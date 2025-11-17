<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <?php if (hasPermission(['admin'])): ?>
                    <div class="sb-sidenav-menu-heading" style="padding: 0.5rem 1.5rem 0.25rem; font-size: 0.75rem; margin-top: 0;">Home</div> 
                    
                    <!-- Nav Item - Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php" style="padding: 0.6rem 1.5rem; font-size: 1rem; font-weight: 500;">
                            <i class="fas fa-fw fa-tachometer-alt me-2" style="font-size: 0.95rem;"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <?php endif; ?>
                         <!-- Divider -->
                        <hr class="sidebar-divider my-2">

                    <!-- จัดการข้อมูลครุภัณฑ์ -->
                    <?php if (hasPermission(['admin'])): ?>
                    <div class="sb-sidenav-menu-heading" style="padding: 0.5rem 1.5rem 0.25rem; font-size: 0.75rem;">จัดการข้อมูลครุภัณฑ์</div>
                    <ul class="nav flex-column" style="margin-bottom: 0.5rem;">
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'equipment.php' ? 'active' : ''; ?>" href="equipment.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-laptop me-2" style="font-size: 0.95rem;"></i> 
                                ครุภัณฑ์
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-tags me-2" style="font-size: 0.95rem;"></i> 
                                หมวดหมู่/ประเภท
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'disposal.php' ? 'active' : ''; ?>" href="disposal.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-trash-alt me-2" style="font-size: 0.95rem;"></i> 
                                จำหน่ายครุภัณฑ์
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>

                      <!-- Divider -->
                        <hr class="sidebar-divider my-2">

                    
                    <!-- ระบบซ่อมบำรุง -->
                    <?php if (hasPermission(['admin', 'user','technician'])): ?>
                    <div class="sb-sidenav-menu-heading" style="padding: 0.5rem 1.5rem 0.25rem; font-size: 0.75rem;">ระบบซ่อมบำรุง</div>
                    <ul class="nav flex-column" style="margin-bottom: 0.5rem;">
                        <!-- เมนูสำหรับผู้ใช้ทั่วไป -->
                        <?php if (hasPermission(['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'maintenance_user.php' ? 'active' : ''; ?>" href="maintenance_user.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-plus-circle me-2" style="font-size: 0.95rem;"></i>
                                แจ้งซ่อม
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- เมนูสำหรับ admin และ technician -->
                        <?php if (hasPermission(['admin', 'technician'])): ?>
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'maintenance.php' ? 'active' : ''; ?>" href="maintenance.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-tools me-2" style="font-size: 0.95rem;"></i>
                                ระบบแจ้งซ่อม
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if (hasPermission(['admin', 'technician'])): ?>
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'equipment_classroom.php' ? 'active' : ''; ?>" href="equipment_classroom.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-chalkboard-teacher me-2" style="font-size: 0.95rem;"></i>
                                อุปกรณ์ในห้องเรียน
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <?php endif; ?>

                      <!-- Divider -->
                        <hr class="sidebar-divider my-2">

                    <!-- ข้อมูลองค์กร -->
                    <?php if (hasPermission(['admin'])): ?>
                    <div class="sb-sidenav-menu-heading" style="padding: 0.5rem 1.5rem 0.25rem; font-size: 0.75rem;">ข้อมูลองค์กร</div>
                    <ul class="nav flex-column" style="margin-bottom: 0.5rem;">
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : ''; ?>" href="departments.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-building me-2" style="font-size: 0.95rem;"></i>
                                โรงเรียน/แผนก
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'buildingfloorplans.php' ? 'active' : ''; ?>" href="buildingfloorplans.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-map me-2" style="font-size: 0.95rem;"></i>
                                แผนผังตารางห้อง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : ''; ?>" href="employees.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-users me-2 " style="font-size: 0.95rem;"></i>
                                พนักงาน
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>
                        <!-- Divider -->
                        <hr class="sidebar-divider my-2">

                    <!-- รายงานและการตั้งค่า -->
                    <?php if (hasPermission(['admin'])): ?>
                    <div class="sb-sidenav-menu-heading" style="padding: 0.5rem 1.5rem 0.25rem; font-size: 0.75rem;">รายงานและการตั้งค่า</div> 
                    <ul class="nav flex-column" style="margin-bottom: 0.5rem;">
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-chart-bar me-2" style="font-size: 0.95rem;"></i>
                                รายงาน
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php" style="padding: 0.5rem 1.5rem 0.5rem 2.5rem; font-size: 1rem;">
                                <i class="fas fa-fw fa-user-cog me-2" style="font-size: 0.95rem;"></i>
                                จัดการข้อมูลผู้ใช้
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>

                </div>
            </div>
            
            <div class="sb-sidenav-footer" style="padding: 0.75rem 1.5rem;">
                <div class="small" style="font-size: 0.8rem; line-height: 1.3; margin-bottom: 0.2rem;">Logged in as: <?php echo $_SESSION['username']; ?></div>
                <div class="user-role" style="font-size: 0.75rem; color: #1a73e8; font-weight: 600;"><?php echo ucfirst($_SESSION['role']); ?></div>
            </div>
        </nav>
    </div>