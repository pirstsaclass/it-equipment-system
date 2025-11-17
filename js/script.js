// SB Admin Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    // Toggle sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb-sidenav-toggled', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
    
    // Restore sidebar state from localStorage
    if (localStorage.getItem('sb-sidenav-toggled') === 'true') {
        document.body.classList.add('sb-sidenav-toggled');
    }
    
    // Handle responsive behavior
    function handleResize() {
        if (window.innerWidth < 768) {
            document.body.classList.remove('sb-sidenav-toggled');
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize();
});

// Global functions
function confirmAction(message = 'คุณแน่ใจหรือไม่?') {
    return confirm(message);
}

// Toast notifications
function showToast(message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toastEl);
    
    // Initialize and show toast
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
    
    // Remove toast from DOM after it's hidden
    toastEl.addEventListener('hidden.bs.toast', function() {
        toastEl.remove();
    });
}

// DataTables initialization
function initializeDataTables(tableId, options = {}) {
    const defaultOptions = {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        responsive: true,
        autoWidth: false
    };
    
    const mergedOptions = {...defaultOptions, ...options};
    return $(tableId).DataTable(mergedOptions);
}

// Form validation helper
function validateForm(formId, rules) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let isValid = true;
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (rules[input.name] && !rules[input.name].test(input.value)) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// AJAX helper functions
const AjaxHelper = {
    get: function(url, callback) {
        fetch(url)
            .then(response => response.json())
            .then(data => callback(null, data))
            .catch(error => callback(error, null));
    },
    
    post: function(url, data, callback) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => callback(null, data))
        .catch(error => callback(error, null));
    },
    
    put: function(url, data, callback) {
        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => callback(null, data))
        .catch(error => callback(error, null));
    },
    
    delete: function(url, callback) {
        fetch(url, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => callback(null, data))
        .catch(error => callback(error, null));
    }
};

// Chart initialization helper
function initializeChart(canvasId, config) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    return new Chart(ctx, config);
}

// Sidebar Accordion Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar accordion
    initializeSidebarAccordion();
    
    // Handle sidebar collapse on mobile
    handleMobileSidebar();
});

function initializeSidebarAccordion() {
    const collapseElements = document.querySelectorAll('.sb-sidenav-menu .collapse');
    
    collapseElements.forEach(collapse => {
        // Set initial state based on active child
        const activeChild = collapse.querySelector('.nav-link.active');
        if (activeChild) {
            const parentCollapse = collapse.closest('.nav-item').querySelector('[data-bs-toggle="collapse"]');
            if (parentCollapse) {
                parentCollapse.setAttribute('aria-expanded', 'true');
                collapse.classList.add('show');
            }
        }
    });
}

function handleMobileSidebar() {
    // Close sidebar when clicking on a link in mobile view
    if (window.innerWidth < 768) {
        const navLinks = document.querySelectorAll('.sb-sidenav .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (document.body.classList.contains('sb-sidenav-toggled')) {
                    document.body.classList.remove('sb-sidenav-toggled');
                }
            });
        });
    }
}

// Enhanced sidebar toggle with animation
function toggleSidebar() {
    const sidebar = document.getElementById('layoutSidenav_nav');
    const content = document.getElementById('layoutSidenav_content');
    
    document.body.classList.toggle('sb-sidenav-toggled');
    localStorage.setItem('sb-sidenav-toggled', document.body.classList.contains('sb-sidenav-toggled'));
}

// Function to check and set sidebar state
function setSidebarState() {
    const isToggled = localStorage.getItem('sb-sidenav-toggled') === 'true';
    if (isToggled) {
        document.body.classList.add('sb-sidenav-toggled');
    } else {
        document.body.classList.remove('sb-sidenav-toggled');
    }
}

// Call this function on page load
setSidebarState();

// Export functions to global scope
window.showToast = showToast;
window.confirmAction = confirmAction;
window.initializeDataTables = initializeDataTables;
window.validateForm = validateForm;
window.AjaxHelper = AjaxHelper;
window.initializeChart = initializeChart;
window.toggleSidebar = toggleSidebar;
window.setSidebarState = setSidebarState;

