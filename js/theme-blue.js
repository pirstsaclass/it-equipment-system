document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    
    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            content.classList.toggle('active');
            
            // Change icon when sidebar is collapsed
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('active')) {
                icon.classList.remove('fa-align-left');
                icon.classList.add('fa-align-right');
            } else {
                icon.classList.remove('fa-align-right');
                icon.classList.add('fa-align-left');
            }
        });
    }
    
    // Initialize Charts
    initializeCharts();
    
    // Handle dropdown menus
    initializeDropdowns();
    
    // Handle responsive behavior
    handleResponsive();
});

// Initialize Chart.js charts
function initializeCharts() {
    // Area Chart
    const areaChartCtx = document.getElementById('myAreaChart');
    if (areaChartCtx) {
        new Chart(areaChartCtx, {
            type: 'line',
            data: {
                labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                datasets: [{
                    label: 'รายได้',
                    lineTension: 0.3,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [65000, 79000, 80000, 81000, 86000, 95000, 98000, 105000, 115000, 120000, 125000, 130000],
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return '฿' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleColor: '#6e707e',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'รายได้: ฿' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Pie Chart
    const pieChartCtx = document.getElementById('myPieChart');
    if (pieChartCtx) {
        new Chart(pieChartCtx, {
            type: 'doughnut',
            data: {
                labels: ['โซเชียลมีเดีย', 'อีเมล', 'เว็บไซต์'],
                datasets: [{
                    data: [55, 30, 15],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                },
                cutout: '80%',
            },
        });
    }
}

// Initialize dropdown functionality
function initializeDropdowns() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.matches('.dropdown-toggle')) {
            const dropdowns = document.getElementsByClassName('dropdown-menu');
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    });
    
    // Add animation to dropdowns
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('show.bs.dropdown', function() {
            this.querySelector('.dropdown-menu').classList.add('animated--fade-in');
        });
    });
}

// Handle responsive behavior
function handleResponsive() {
    // Auto-close sidebar on small screens when clicking a link
    const sidebarLinks = document.querySelectorAll('#sidebar a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                document.getElementById('sidebar').classList.add('active');
                document.getElementById('content').classList.add('active');
                
                const sidebarCollapseBtn = document.getElementById('sidebarCollapse');
                if (sidebarCollapseBtn) {
                    const icon = sidebarCollapseBtn.querySelector('i');
                    icon.classList.remove('fa-align-right');
                    icon.classList.add('fa-align-left');
                }
            }
        });
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('content').classList.remove('active');
            
            const sidebarCollapseBtn = document.getElementById('sidebarCollapse');
            if (sidebarCollapseBtn) {
                const icon = sidebarCollapseBtn.querySelector('i');
                icon.classList.remove('fa-align-right');
                icon.classList.add('fa-align-left');
            }
        }
    });
}

// Utility function to toggle dark mode (optional)
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    
    // You can add more specific dark mode styles as needed
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('darkMode', 'enabled');
    } else {
        localStorage.setItem('darkMode', 'disabled');
    }
}

// Check for saved dark mode preference
if (localStorage.getItem('darkMode') === 'enabled') {
    document.body.classList.add('dark-mode');
}