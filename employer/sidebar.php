<?php
// Ensure the user is logged in and is an employer
require_once '../includes/auth.php';
protectEmployerRoute();

$user_id = $_SESSION['user_id'];
$unread_messages = getUnreadMessageCount($user_id);
?>

<!-- Sidebar -->
<aside class="dashboard-sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header d-flex align-items-center">
        <i class="fas fa-user-circle avatar"></i>
        <div class="user-info">
            <h4><?= htmlspecialchars($_SESSION['user_name']) ?></h4>
            <p>Employer</p>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="sidebar-menu">
        <ul>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'post_job.php' ? 'active' : '' ?>">
                <a href="post_job.php">
                    <i class="fas fa-plus-circle"></i>
                    <span>Post a Job</span>
                </a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'manage_jobs.php' ? 'active' : '' ?>">
                <a href="manage_jobs.php">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Jobs</span>
                </a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'applications.php' ? 'active' : '' ?>">
                <a href="applications.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Applications</span>
                </a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : '' ?>">
                <a href="../messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                    <?php if ($unread_messages > 0): ?>
                        <span class="badge"><?= $unread_messages ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">
                <a href="../profile.php">
                    <i class="fas fa-user-cog"></i>
                    <span>Profile Settings</span>
                </a>
            </li>
            <li>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <p>Need assistance?</p>
        <a href="mailto:support@jobvista.com" class="btn-help">Contact Support</a>
    </div>
</aside>