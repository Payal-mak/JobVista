<?php 
$title = isset($title) ? $title : 'JobVista';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Find your dream job or the perfect candidate">
    <meta name="keywords" content="jobs, employment, career, hiring">
    
    <title><?= htmlspecialchars($title) ?> - <?= defined('SITE_NAME') ? SITE_NAME : 'JobVista' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/logo.png">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
    <link rel="stylesheet" href="assets/css/enhanced-styles.css">
    
    <?php if (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false || 
              strpos($_SERVER['REQUEST_URI'], 'job_seeker') !== false || 
              strpos($_SERVER['REQUEST_URI'], 'employer') !== false || 
              strpos($_SERVER['REQUEST_URI'], 'admin') !== false): ?>
        <link rel="stylesheet" href="assets/css/dashboard.css">
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm animate-slideInDown" style="background-color: #29366f;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="assets/images/logo.png" alt="Logo" height="20" width="auto" class="me-2 animate-pulse">
                <span style="font-weight: 600;" class="animate-fadeIn"><?= defined('SITE_NAME') ? SITE_NAME : 'JobVista' ?></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item" data-animation="slideInLeft" data-delay="0.1">
                        <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item" data-animation="slideInLeft" data-delay="0.2">
                        <a class="nav-link" href="jobs.php"><i class="fas fa-briefcase me-1"></i> Browse Jobs</a>
                    </li>
                    <?php if (isLoggedIn() && isEmployer()): ?>
                        <li class="nav-item" data-animation="slideInLeft" data-delay="0.3">
                            <a class="nav-link" href="employer/post_job.php"><i class="fas fa-plus-circle me-1"></i> Post a Job</a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown" data-animation="slideInRight" data-delay="0.1">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="me-2 position-relative">
                                    <i class="fas fa-user-circle fa-lg"></i>
                                    <?php if ($unread = getUnreadMessageCount($_SESSION['user_id'])): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge-animated">
                                            <?= $unread ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 200px;">
                                <?php if (isJobSeeker()): ?>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="job_seeker/dashboard.php"><i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="job_seeker/applications.php"><i class="fas fa-file-alt me-2 text-primary"></i> My Applications</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="job_seeker/saved_jobs.php"><i class="fas fa-bookmark me-2 text-primary"></i> Saved Jobs</a></li>
                                <?php elseif (isEmployer()): ?>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="employer/dashboard.php"><i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="employer/post_job.php"><i class="fas fa-plus-circle me-2 text-primary"></i> Post a Job</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="employer/manage_jobs.php"><i class="fas fa-briefcase me-2 text-primary"></i> Manage Jobs</a></li>
                                <?php elseif (isAdmin()): ?>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="admin/dashboard.php"><i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="admin/manage_users.php"><i class="fas fa-users me-2 text-primary"></i> Manage Users</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center py-2" href="admin/manage_jobs.php"><i class="fas fa-briefcase me-2 text-primary"></i> Manage Jobs</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2" href="messages.php">
                                        <i class="fas fa-envelope me-2 text-primary"></i> Messages 
                                        <?php if ($unread): ?>
                                            <span class="badge bg-primary rounded-pill ms-1"><?= $unread ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                                <li><a class="dropdown-item d-flex align-items-center py-2" href="profile.php"><i class="fas fa-user-cog me-2 text-primary"></i> Profile Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item d-flex align-items-center py-2" href="logout.php"><i class="fas fa-sign-out-alt me-2 text-danger"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item" data-animation="slideInRight" data-delay="0.2">
                            <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                        </li>
                        <li class="nav-item ms-2" data-animation="slideInRight" data-delay="0.3">
                            <a class="btn btn-primary animate-pulse" href="register.php" style="background-color: #1491ea; border-color: #1491ea;"><i class="fas fa-user-plus me-1"></i> Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pb-5">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show mb-0 animate-slideInDown" role="alert">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
            <?php unset($_SESSION['message_type']); ?>
        <?php endif; ?>
