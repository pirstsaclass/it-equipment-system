<div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            
                            
                             <!-- Divider -->
                        <hr class="sidebar-divider my-2">

                        <!-- Nav Item - Dashboard -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                                    <i class="fas fa-fw fa-tachometer-alt"></i>
                                    <span>Dashboard</span>
                            </a>
                        </li>
                        <!-- Divider -->
                        <hr class="sidebar-divider my-2">

                            
                            <!-- จัดการข้อมูล -->
                            <?php if (hasPermission(['admin', 'user'])): ?>
                            <div class="sb-sidenav-menu-heading">จัดการข้อมูลครุภัณฑ์</div>
                            <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'equipment.php' ? 'active' : ''; ?>" href="equipment.php">
                                            <i class="fas fa-fw fa-laptop"></i> 
                                             ครุภัณฑ์
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                                            <i class="fas fa-fw fa-tags"></i> 
                                             หมวดหมู่/ประเภท
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'disposal.php' ? 'active' : ''; ?>" href="disposal.php">
                                            <i class="fas fa-trash-alt me-2"></i> 
                                            จำหน่ายครุภัณฑ์
                                        </a>
                                    </li>
                            </ul>
                            <?php endif; ?>
                            
                            <!-- ระบบซ่อมบำรุง -->
                             <!-- จัดการข้อมูล -->
                            <?php if (hasPermission(['admin', 'user','technician'])): ?>
                            <div class="sb-sidenav-menu-heading">ระบบซ่อมบำรุง</div>
                            <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'maintenance.php' ? 'active' : ''; ?>" href="maintenance.php">
                                            <i class="fas fa-fw fa-tools"></i>
                                            ระบบแจ้งซ่อม
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'equipment_classroom.php' ? 'active' : ''; ?>" href="equipment_classroom.php">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                            อุปกรณ์ในห้องเรียน
                                        </a>
                                    </li>
                            </ul>
                            <?php endif; ?>

                        <!-- ข้อมูลองค์กร -->
                        <?php if (hasPermission(['admin', 'user'])): ?>
                            <div class="sb-sidenav-menu-heading">ข้อมูลองค์กร</div>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'departments.php' ? 'active' : ''; ?>" href="departments.php">
                                        <i class="fas fa-fw fa-building"></i>
                                        โรงเรียน/แผนก
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

            