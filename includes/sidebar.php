<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav sb-sidenav-blue" id="sidenavAccordion" style="background-color: #1e88e5;">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <!-- Divider -->
                     
                    <div class="sb-sidenav-menu-heading">เมนูหลัก</div>
                    <!-- Nav Item - Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-fw fa-tachometer-alt me-2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                 

                    <!-- จัดการข้อมูลครุภัณฑ์ -->
                    <?php if (hasPermission(['admin', 'user'])): ?>
                    <div class="sb-sidenav-menu-heading">จัดการข้อมูลครุภัณฑ์</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'equipment.php' ? 'active' : ''; ?>" href="equipment.php">
                                <i class="fas fa-fw fa-list me-2"></i>
                                <span>รายการครุภัณฑ์</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                                <i class="fas fa-fw fa-tags me-2"></i>
                                <span>หมวดหมู่และประเภท</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'disposal.php' ? 'active' : ''; ?>" href="disposal.php">
                                <i class="fas fa-trash-alt me-2"></i>
                                <span>จำหน่ายครุภัณฑ์</span>
                            </a>
                        </li>
                        
                    </ul>
                    
                    <?php endif; ?>
                    <!-- ระบบซ่อมบำรุง -->
                    <?php if (hasPermission(['admin', 'user','technician'])): ?>
                    <div class="sb-sidenav-menu-heading">ระบบซ่อมบำรุง</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'maintenance.php' ? 'active' : ''; ?>" href="maintenance.php">
                                <i class="fas fa-fw fa-wrench me-2"></i>
                                <span>ระบบแจ้งซ่อม</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'equipment_classroom.php' ? 'active' : ''; ?>" href="equipment_classroom.php">
                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                <span>อุปกรณ์ในห้องเรียน</span>
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>

                    <!-- ข้อมูลองค์กร -->
                    <?php if (hasPermission(['admin', 'user'])): ?>
                    <div class="sb-sidenav-menu-heading">ข้อมูลองค์กร</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : ''; ?>" href="departments.php">
                                <i class="fas fa-fw fa-university me-2"></i>
                                <span>โรงเรียน/แผนก</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'buildingfloorplans.php' ? 'active' : ''; ?>" href="buildingfloorplans.php">
                                <i class="fas fa-fw fa-map me-2"></i>
                                <span>แผนผังตารางห้อง</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : ''; ?>" href="employees.php">
                                <i class="fas fa-fw fa-users me-2"></i>
                                <span>พนักงาน</span>
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>

                    <!-- รายงานและการตั้งค่า -->
                    <?php if (hasPermission(['admin'])): ?>
                    <div class="sb-sidenav-menu-heading">รายงานและการตั้งค่า</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                                <i class="fas fa-fw fa-chart-bar me-2"></i>
                                <span>รายงาน</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                                <i class="fas fa-fw fa-user-cog me-2"></i>
                                <span>จัดการข้อมูลผู้ใช้</span>
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            <div class="sb-sidenav-footer" style="background-color: #1976d2;">
                <div class="small">Logged in as: <?php echo $_SESSION['username']; ?></div>
                <div class="user-role small"><?php echo ucfirst($_SESSION['role']); ?></div>
            </div>
        </nav>
    </div>


<style>
/* ปรับแต่ง Sidebar ให้เป็นสีฟ้า */
.sb-sidenav-blue {
    background-color: #1e88e5 !important;
}

/* เพิ่มเงาขอบให้กับ sidebar */
#sidenavAccordion {
    background-color: #1e88e5 !important;
    box-shadow: 
        3px 0 15px rgba(0, 0, 0, 0.15),
        1px 0 5px rgba(0, 0, 0, 0.1),
        inset -1px 0 2px rgba(255, 255, 255, 0.1);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.sb-sidenav-menu-heading {
    padding: 0.75rem 1rem 0.5rem;
    font-size: 0.85rem !important;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0;
    color: #e3f2fd;
    border-bottom: 1px solid #42a5f5;
}

.sb-sidenav .nav-link {
    padding: 0.5rem 1rem 0.5rem 1.5rem !important;
    font-size: 0.875rem;
    font-weight: 400;
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
    color: #e3f2fd;
}

.sb-sidenav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-left-color: #bbdefb;
    color: #ffffff;
    box-shadow: inset 2px 0 5px rgba(255, 255, 255, 0.05);
}

.sb-sidenav .nav-link.active {
    background-color: rgba(187, 222, 251, 0.2);
    border-left-color: #bbdefb;
    font-weight: 500;
    color: #ffffff;
    box-shadow: 
        inset 2px 0 8px rgba(255, 255, 255, 0.1),
        0 2px 4px rgba(0, 0, 0, 0.1);
}

.sb-sidenav .nav-link i {
    font-size: 0.8rem;
    width: 1.2rem;
    opacity: 0.9;
    color: #e3f2fd;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.sb-sidenav .nav-link.active i {
    opacity: 1;
    color: #ffffff;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.sb-sidenav .nav-link:hover i {
    color: #ffffff;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.sb-sidenav .nav-item {
    margin-bottom: 0.05rem;
}

.sb-sidenav-footer {
    padding: 1rem;
    border-top: 1px solid #42a5f5;
    background-color: #1976d2;
    box-shadow: 
        inset 0 1px 3px rgba(255, 255, 255, 0.1),
        0 -2px 5px rgba(0, 0, 0, 0.1);
}

.sb-sidenav-footer .small {
    font-size: 0.75rem;
    font-weight: 300;
    margin-bottom: 0.1rem;
    color: #e3f2fd;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
}

.user-role {
    font-weight: 400;
    color: #bbdefb;
    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
}

/* ขยับเมนูทั้งหมดเข้ามา */
.sb-sidenav-menu {
    padding: 0.5rem 0;
}

.nav.flex-column {
    margin-left: 0.5rem;
}

/* เพิ่มระยะห่างระหว่าง section */
.sb-sidenav-menu-heading:not(:first-child) {
    margin-top: 0.5rem;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* ปรับสีเมื่อ hover บน footer */
.sb-sidenav-footer:hover {
    background-color: #1565c0 !important;
    box-shadow: 
        inset 0 1px 3px rgba(255, 255, 255, 0.15),
        0 -2px 8px rgba(0, 0, 0, 0.15);
}

/* เพิ่มเอฟเฟกต์เงาให้กับเมนูหัวข้อ */
.sb-sidenav-menu-heading {
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* เพิ่มเงาให้กับเมนูย่อยเมื่อ hover */
.nav.flex-column .nav-link:hover {
    box-shadow: 
        inset 2px 0 8px rgba(255, 255, 255, 0.08),
        0 2px 4px rgba(0, 0, 0, 0.05);
}
</style>