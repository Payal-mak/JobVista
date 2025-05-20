document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables for tables
    initDataTables();
    
    // Handle job approval/rejection
    setupJobActions();
    
    // Handle user management actions
    setupUserActions();
    
    // Load dashboard statistics
    loadDashboardStats();
    
    // Setup real-time updates (polling)
    setupRealTimeUpdates();
});

/**
 * Initialize DataTables for all tables in the dashboard
 */
function initDataTables() {
    $('.jobs-table table, .users-table table').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries found",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        pageLength: 10
    });
}

/**
 * Setup event handlers for job approval/rejection actions
 */
function setupJobActions() {
    document.querySelectorAll('.btn-approve, .btn-reject').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const action = this.classList.contains('btn-approve') ? 'approve' : 'reject';
            
            Swal.fire({
                title: `Are you sure you want to ${action} this job?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Yes, ${action}`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success',
                                text: `Job has been ${action}d successfully`,
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to process request',
                            icon: 'error'
                        });
                    });
                }
            });
        });
    });
}

/**
 * Setup event handlers for user management actions
 */
function setupUserActions() {
    document.querySelectorAll('.btn-activate, .btn-deactivate, .btn-ban, .btn-unban').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            let action = '';
            
            if (this.classList.contains('btn-activate')) {
                action = 'activate';
            } else if (this.classList.contains('btn-deactivate')) {
                action = 'deactivate';
            } else if (this.classList.contains('btn-ban')) {
                action = 'ban';
            } else if (this.classList.contains('btn-unban')) {
                action = 'unban';
            }
            
            const actionText = action === 'ban' ? 'ban this user' : 
                             action === 'unban' ? 'unban this user' :
                             `${action} this user`;
            
            Swal.fire({
                title: `Are you sure you want to ${actionText}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Yes, ${action}`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success',
                                text: `User has been ${action}d successfully`,
                                icon: 'success'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'Something went wrong',
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to process request',
                            icon: 'error'
                        });
                    });
                }
            });
        });
    });
}

/**
 * Load dashboard statistics via AJAX
 */
function loadDashboardStats() {
    fetch('../includes/ajax.php?action=get_dashboard_stats', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update stats cards
            document.querySelector('.stat-card:nth-child(1) h3').textContent = data.total_users;
            document.querySelector('.stat-card:nth-child(2) h3').textContent = data.total_jobs;
            document.querySelector('.stat-card:nth-child(3) h3').textContent = data.pending_jobs;
            
            // Update charts if needed
            if (typeof updateCharts === 'function') {
                updateCharts(data.chart_data);
            }
        }
    })
    .catch(error => {
        console.error('Error loading dashboard stats:', error);
    });
}

/**
 * Setup real-time updates for the dashboard
 */
function setupRealTimeUpdates() {
    // Check for new data every 30 seconds
    setInterval(() => {
        fetch('../includes/ajax.php?action=check_updates', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.has_updates) {
                // Show notification
                const notification = new Noty({
                    type: 'info',
                    text: 'New updates available. Refreshing data...',
                    timeout: 3000
                }).show();
                
                // Reload the stats
                loadDashboardStats();
                
                // Optionally refresh specific tables
                if (typeof $ !== 'undefined' && $.fn.DataTable) {
                    $('.jobs-table table, .users-table table').DataTable().ajax.reload(null, false);
                }
            }
        });
    }, 30000); // 30 seconds
}

/**
 * Initialize any charts on the dashboard
 */
function initCharts() {
    if (typeof Chart === 'undefined') return;
    
    const ctx = document.getElementById('dashboardChart');
    if (!ctx) return;
    
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Users', 'Jobs', 'Applications', 'Messages'],
            datasets: [{
                label: 'Last 7 Days',
                data: [0, 0, 0, 0],
                backgroundColor: [
                    'rgba(52, 152, 219, 0.7)',
                    'rgba(46, 204, 113, 0.7)',
                    'rgba(155, 89, 182, 0.7)',
                    'rgba(241, 196, 15, 0.7)'
                ],
                borderColor: [
                    'rgba(52, 152, 219, 1)',
                    'rgba(46, 204, 113, 1)',
                    'rgba(155, 89, 182, 1)',
                    'rgba(241, 196, 15, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    window.updateCharts = function(data) {
        chart.data.datasets[0].data = [
            data.new_users,
            data.new_jobs,
            data.new_applications,
            data.new_messages
        ];
        chart.update();
    };
}

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', initCharts);