<?php 
require_once '../includes/auth.php';
protectAdminRoute();

$users = getAllUsers();

// Handle user status changes
if (isset($_GET['activate'])) {
    $user_id = sanitize($_GET['activate']);
    if (updateUserStatus($user_id, 'active')) {
        $_SESSION['success'] = "User activated successfully";
    } else {
        $_SESSION['error'] = "Failed to activate user";
    }
    header("Location: manage_users.php");
    exit();
}

if (isset($_GET['deactivate'])) {
    $user_id = sanitize($_GET['deactivate']);
    if (updateUserStatus($user_id, 'inactive')) {
        $_SESSION['success'] = "User deactivated successfully";
    } else {
        $_SESSION['error'] = "Failed to deactivate user";
    }
    header("Location: manage_users.php");
    exit();
}

if (isset($_GET['ban'])) {
    $user_id = sanitize($_GET['ban']);
    if (updateUserStatus($user_id, 'banned')) {
        $_SESSION['success'] = "User banned successfully";
    } else {
        $_SESSION['error'] = "Failed to ban user";
    }
    header("Location: manage_users.php");
    exit();
}

if (isset($_GET['unban'])) {
    $user_id = sanitize($_GET['unban']);
    if (updateUserStatus($user_id, 'active')) {
        $_SESSION['success'] = "User unbanned successfully";
    } else {
        $_SESSION['error'] = "Failed to unban user";
    }
    header("Location: manage_users.php");
    exit();
}

include '../includes/header.php';
?>

<div class="dashboard-container">
    <?php include 'sidebar.php'; ?>
    
    <div class="dashboard-content">
        <div class="dashboard-header">
            <h1>Manage Users</h1>
            <p>View and manage all users in the system</p>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="users-filter">
            <label>Filter by role:</label>
            <select class="form-control" onchange="window.location.href='manage_users.php?role='+this.value">
                <option value="">All Roles</option>
                <option value="job_seeker" <?= isset($_GET['role']) && $_GET['role'] === 'job_seeker' ? 'selected' : '' ?>>Job Seekers</option>
                <option value="employer" <?= isset($_GET['role']) && $_GET['role'] === 'employer' ? 'selected' : '' ?>>Employers</option>
            </select>
            
            <label>Filter by status:</label>
            <select class="form-control" onchange="window.location.href='manage_users.php?status='+this.value">
                <option value="">All Statuses</option>
                <option value="active" <?= isset($_GET['status']) && $_GET['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= isset($_GET['status']) && $_GET['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="banned" <?= isset($_GET['status']) && $_GET['status'] === 'banned' ? 'selected' : '' ?>>Banned</option>
            </select>
        </div>
        
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No users found</p>
            </div>
        <?php else: ?>
            <div class="users-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $user['role'])) ?></td>
                                <td>
                                    <span class="status-badge <?= $user['status'] ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <?php if ($user['status'] === 'active'): ?>
                                            <a href="manage_users.php?deactivate=<?= $user['id'] ?>" class="btn-deactivate" title="Deactivate">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="manage_users.php?activate=<?= $user['id'] ?>" class="btn-activate" title="Activate">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($user['status'] !== 'banned'): ?>
                                            <a href="manage_users.php?ban=<?= $user['id'] ?>" class="btn-ban" title="Ban" onclick="return confirm('Are you sure you want to ban this user?')">
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="manage_users.php?unban=<?= $user['id'] ?>" class="btn-unban" title="Unban">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>