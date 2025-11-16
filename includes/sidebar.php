[file name]: sidebar.php
[file content begin]
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <!-- Divider -->
                    <!-- Nav Item - Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <!-- Divider -->
                    <hr class="sidebar-divider my-2">

                    <!-- จัดการข้อมูลครุภัณฑ์ -->
                    <?php if (hasPermission(['admin', 'user'])): ?>
                    <div class="sb-sidenav-menu-heading">จัดการข้อมูลครุภัณฑ์</div>
                    <ul class="nav flex-column">
                        <!-- Dropdown สำหรับจัดการครุภัณฑ์ -->
                        <li class="nav-item">
                            <a class="nav-link collapsed sidebar-submenu" href="#" data-bs-toggle="collapse" data-bs-target="#equipmentCollapse" aria-expanded="false" aria-controls="equipmentCollapse">
                                <i class="fas fa-fw fa-laptop"></i>
                                <span>จัดการครุภัณฑ์</span>
                                <i class="fas fa-angle-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="equipmentCollapse" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'equipment.php' ? 'active' : ''; ?>" href="equipment.php">
                                        <i class="fas fa-fw fa-list"></i>
                                        รายการครุภัณฑ์
                                    </a>
                                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                                        <i class="fas fa-fw fa-tags"></i>
                                        หมวดหมู่และประเภท
                                    </a>
                                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'disposal.php' ? 'active' : ''; ?>" href="disposal.php">
                                        <i class="fas fa-trash-alt me-2"></i>
                                        จำหน่ายครุภัณฑ์
                                    </a>
                                </nav>
                            </div>
                        </li>
                    </ul>
                    <?php endif; ?>

                    <!-- ระบบซ่อมบำรุง -->
                    <?php if (hasPermission(['admin', 'user','technician'])): ?>
                    <div class="sb-sidenav-menu-heading">ระบบซ่อมบำรุง</div>
                    <ul class="nav flex-column">
                        <!-- Dropdown สำหรับระบบซ่อมบำรุง -->
                        <li class="nav-item">
                            <a class="nav-link collapsed sidebar-submenu" href="#" data-bs-toggle="collapse" data-bs-target="#maintenanceCollapse" aria-expanded="false" aria-controls="maintenanceCollapse">
                                <i class="fas fa-fw fa-tools"></i>
                                <span>ระบบซ่อมบำรุง</span>
                                <i class="fas fa-angle-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="maintenanceCollapse" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'maintenance.php' ? 'active' : ''; ?>" href="maintenance.php">
                                        <i class="fas fa-fw fa-wrench"></i>
                                        ระบบแจ้งซ่อม
                                    </a>
                                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'equipment_classroom.php' ? 'active' : ''; ?>" href="equipment_classroom.php">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                        อุปกรณ์ในห้องเรียน
                                    </a>
                                </nav>
                            </div>
                        </li>
                    </ul>
                    <?php endif; ?>

                    <!-- ข้อมูลองค์กร -->
                    <?php if (hasPermission(['admin', 'user'])): ?>
                    <div class="sb-sidenav-menu-heading">ข้อมูลองค์กร</div>
                    <ul class="nav flex-column">
                        <!-- Dropdown สำหรับข้อมูลองค์กร -->
                        <li class="nav-item">
                            <a class="nav-link collapsed sidebar-submenu" href="#" data-bs-toggle="collapse" data-bs-target="#organizationCollapse" aria-expanded="false" aria-controls="organizationCollapse">
                                <i class="fas fa-fw fa-building"></i>
                                <span>ข้อมูลองค์กร</span>
                                <i class="fas fa-angle-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="organizationCollapse" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : ''; ?>" href="departments.php">
                                        <i class="fas fa-fw fa-university"></i>
                                        โรงเรียน/แผนก
                                    </a>
                                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'buildingfloorplans.php' ? 'active' : ''; ?>" href="buildingfloorplans.php">
                                        <i class="fas fa-fw fa-map"></i>
                                        แผนผังตารางห้อง
                                    </a>
                                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : ''; ?>" href="employees.php">
                                        <i class="fas fa-fw fa-users"></i>
                                        พนักงาน
                                    </a>
                                </nav>
                            </div>
                        </li>
                    </ul>
                    <?php endif; ?>

                    <!-- รายงานและการตั้งค่า -->
                    <?php if (hasPermission(['admin'])): ?>
                    <div class="sb-sidenav-menu-heading">รายงานและการตั้งค่า</div>
                    <ul class="nav flex-column">
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
                    <?php endif; ?>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as: <?php echo $_SESSION['username']; ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
                <!--<a href="logout.php" class="logout-btn ms-auto" title="ออกจากระบบ">Logout
                 <i class="fas fa-sign-out-alt"></i>
               </a>-->
            </div>
        </nav>
    </div>

    <!-- JavaScript สำหรับจัดการสถานะ active ของ dropdown -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ฟังก์ชันสำหรับตั้งค่า dropdown ให้เปิดอัตโนมัติเมื่ออยู่ในหน้าที่เกี่ยวข้อง
        function setActiveDropdown() {
            const currentPage = '<?php echo basename($_SERVER['PHP_SELF']); ?>';
            
            // ตรวจสอบแต่ละ dropdown ว่ามีลิงก์ที่ active อยู่หรือไม่
            const dropdowns = {
                'equipmentCollapse': ['equipment.php', 'categories.php', 'disposal.php'],
                'maintenanceCollapse': ['maintenance.php', 'equipment_classroom.php'],
                'organizationCollapse': ['departments.php', 'buildingfloorplans.php', 'employees.php']
            };
            
            // ตรวจสอบแต่ละ dropdown
            for (const [dropdownId, pages] of Object.entries(dropdowns)) {
                if (pages.includes(currentPage)) {
                    const dropdownElement = document.getElementById(dropdownId);
                    const dropdownToggle = document.querySelector(`[data-bs-target="#${dropdownId}"]`);
                    
                    if (dropdownElement && dropdownToggle) {
                        // เปิด dropdown
                        dropdownElement.classList.add('show');
                        dropdownToggle.classList.remove('collapsed');
                        dropdownToggle.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        }
        
        // เรียกใช้ฟังก์ชันเมื่อโหลดหน้า
        setActiveDropdown();
        
        // เพิ่ม event listener สำหรับการคลิก dropdown
        const dropdownToggles = document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target').replace('#', '');
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    
                    // เปลี่ยนไอคอนลูกศร
                    const arrowIcon = this.querySelector('.fa-angle-down, .fa-angle-up');
                    if (arrowIcon) {
                        if (isExpanded) {
                            arrowIcon.classList.remove('fa-angle-up');
                            arrowIcon.classList.add('fa-angle-down');
                        } else {
                            arrowIcon.classList.remove('fa-angle-down');
                            arrowIcon.classList.add('fa-angle-up');
                        }
                    }
                }
            });
        });
        
        // ตั้งค่าไอคอนลูกศรเริ่มต้นสำหรับ dropdown ที่เปิดอยู่
        const activeDropdownToggles = document.querySelectorAll('.nav-link[data-bs-toggle="collapse"][aria-expanded="true"]');
        activeDropdownToggles.forEach(toggle => {
            const arrowIcon = toggle.querySelector('.fa-angle-down');
            if (arrowIcon) {
                arrowIcon.classList.remove('fa-angle-down');
                arrowIcon.classList.add('fa-angle-up');
            }
        });
    });
    </script>
