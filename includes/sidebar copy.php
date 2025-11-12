<div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <li class="nav-item">
                                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                                    <i class="fas fa-fw fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                            </li>
                            <div class="sb-sidenav-menu-heading">Interface</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                                Layouts
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>

                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="layout-static.html">Static Navigation</a>
                                    <a class="nav-link" href="layout-sidenav-light.html">Light Sidenav</a>
                                </nav>
                            </div>

                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Pages
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                        Authentication
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="login.html">Login</a>
                                            <a class="nav-link" href="register.html">Register</a>
                                            <a class="nav-link" href="password.html">Forgot Password</a>
                                        </nav>
                                    </div>
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                        Error
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link" href="401.html">401 Page</a>
                                            <a class="nav-link" href="404.html">404 Page</a>
                                            <a class="nav-link" href="500.html">500 Page</a>
                                        </nav>
                                    </div>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Addons</div>
                            <a class="nav-link" href="charts.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Charts
                            </a>
                            <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Tables
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Start Bootstrap
                    </div>
                </nav>
            </div>




            <!-- Sidebar -->
<div id="layoutSidenav">
            <div id="layoutSidenav_nav">

            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
            <!-- ส่วนเนื้อหาเมนู -->
            <div class="flex-grow-1">                
                <ul class="nav flex-column">
                    <!-- หน้าหลัก -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                    
                    <!-- จัดการข้อมูล -->
                    <?php if (hasPermission(['admin', 'user'])): ?>

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
                        <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'disposal.php' ? 'active' : ''; ?>" href="disposal.php">
                            <i class="fas fa-trash-alt me-2"></i>
                            ระบบจำหน่ายครุภัณฑ์
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebar-submenu <?php echo basename($_SERVER['PHP_SELF']) == 'equipment_classroom.php' ? 'active' : ''; ?>" href="equipment_classroom.php">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span>อุปกรณ์ในห้องเรียน</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <!-- ข้อมูลองค์กร -->
                    <?php if (hasPermission(['admin', 'user'])): ?>
                    <li class="nav-item mt-2">
                        <div class="sidebar-section-header">
                            <span class="text-white fw-bold">ข้อมูลองค์กร</span>
                        </div>
                    </li>
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
                    <?php endif; ?>
                    
                    <!-- รายงานและการตั้งค่า -->
                    <?php if (hasPermission(['admin'])): ?>
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
                    <?php endif; ?>

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