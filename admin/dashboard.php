<?php 
require_once '../includes/auth.php';
protectAdminRoute();

$users = getAllUsers();
$jobs = getAllJobsForAdmin();
$pending_jobs = array_filter($jobs, function($job) {
    return $job['status'] === 'pending';
});

include '../includes/header.php';
?>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <div class="header-content">
                <h1>Admin Dashboard</h1>
                <p>Welcome back! Here's what's happening with FindJobs today.</p>
            </div>
            <div class="header-actions">
                <span class="current-date">
                    <i class="far fa-calendar-alt"></i> <?= date('l, F j, Y') ?>
                </span>
            </div>
        </div>
        
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($users) ?></h3>
                    <p>Total Users</p>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i> 12% from last week
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($jobs) ?></h3>
                    <p>Total Jobs</p>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i> 8% from last week
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($pending_jobs) ?></h3>
                    <p>Pending Jobs</p>
                    <div class="stat-trend down">
                        <i class="fas fa-arrow-down"></i> 3% from last week
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($jobs) - count($pending_jobs) ?></h3>
                    <p>Approved Jobs</p>
                    <div class="stat-trend up">
                        <i class="fas fa-arrow-up"></i> 5% from last week
                    </div>
                </div>
            </div>
        </div>
        
        <div class="dashboard-sections">
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-clock section-icon pending"></i>
                        <h2>Pending Job Approvals</h2>
                        <?php if (!empty($pending_jobs)): ?>
                            <span class="badge"><?= count($pending_jobs) ?> pending</span>
                        <?php endif; ?>
                    </div>
                    <a href="manage_jobs.php" class="btn btn-outline">
                        <i class="fas fa-list"></i> View All Jobs
                    </a>
                </div>
                
                <?php if (empty($pending_jobs)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-check-circle success"></i>
                        </div>
                        <h3>All caught up!</h3>
                        <p>No jobs pending approval at this moment.</p>
                        <a href="manage_jobs.php" class="btn btn-primary">
                            <i class="fas fa-briefcase"></i> Manage Jobs
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Employer</th>
                                    <th>Category</th>
                                    <th>Posted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach(array_slice($pending_jobs, 0, 5) as $job): ?>
                                    <tr>
                                        <td>
                                            <a href="../job_details.php?id=<?= $job['id'] ?>" class="job-title-link">
                                                <?= $job['title'] ?>
                                            </a>
                                            <div class="job-meta">
                                                <span class="meta-item">
                                                    <i class="fas fa-map-marker-alt"></i> <?= $job['location'] ?>
                                                </span>
                                                <span class="meta-item">
                                                    <i class="fas fa-business-time"></i> <?= ucfirst(str_replace('-', ' ', $job['type'])) ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="employer-cell">
                                            <div class="employer-info">
                                                <span class="employer-name"><?= $job['employer_name'] ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="category-tag"><?= $job['category_name'] ?></span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($job['posted_at'])) ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="manage_jobs.php?approve=<?= $job['id'] ?>" class="btn-action btn-approve" title="Approve">
                                                    <i class="fas fa-check"></i> Approve
                                                </a>
                                                <a href="manage_jobs.php?reject=<?= $job['id'] ?>" class="btn-action btn-reject" title="Reject">
                                                    <i class="fas fa-times"></i> Reject
                                                </a>
                                                <a href="../job_details.php?id=<?= $job['id'] ?>" class="btn-action btn-view" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($pending_jobs) > 5): ?>
                        <div class="table-footer">
                            <a href="manage_jobs.php" class="btn btn-text">
                                View all pending jobs <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <div class="dashboard-section">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fas fa-users section-icon users"></i>
                        <h2>Recent Users</h2>
                        <span class="badge"><?= count($users) ?> total</span>
                    </div>
                    <a href="manage_users.php" class="btn btn-outline">
                        <i class="fas fa-user-cog"></i> Manage Users
                    </a>
                </div>
                
                <?php if (empty($users)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h3>No users found</h3>
                        <p>There are currently no registered users in the system.</p>
                        <a href="manage_users.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Invite Users
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="styled-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach(array_slice($users, 0, 5) as $user): ?>
                                    <tr>
                                        <td class="user-cell">
                                            <div class="user-avatar">
                                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                            </div>
                                            <div class="user-info">
                                                <span class="user-name"><?= $user['name'] ?></span>
                                                <span class="user-join-date">Joined <?= date('M Y', strtotime($user['created_at'])) ?></span>
                                            </div>
                                        </td>
                                        <td><?= $user['email'] ?></td>
                                        <td>
                                            <span class="role-badge <?= strtolower($user['role']) ?>">
                                                <?= ucfirst(str_replace('_', ' ', $user['role'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $user['status'] ?>">
                                                <span class="status-dot"></span>
                                                <?= ucfirst($user['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <a href="manage_users.php?deactivate=<?= $user['id'] ?>" class="btn-action btn-deactivate" title="Deactivate">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="manage_users.php?activate=<?= $user['id'] ?>" class="btn-action btn-activate" title="Activate">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <?php if ($user['status'] !== 'banned'): ?>
                                                    <a href="manage_users.php?ban=<?= $user['id'] ?>" class="btn-action btn-ban" title="Ban" onclick="return confirm('Are you sure you want to ban this user?')">
                                                        <i class="fas fa-ban"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="manage_users.php?unban=<?= $user['id'] ?>" class="btn-action btn-unban" title="Unban">
                                                        <i class="fas fa-check-circle"></i>
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <a href="user_details.php?id=<?= $user['id'] ?>" class="btn-action btn-view" title="View Details">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (count($users) > 5): ?>
                        <div class="table-footer">
                            <a href="manage_users.php" class="btn btn-text">
                                View all users <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>