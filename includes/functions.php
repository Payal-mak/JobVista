<?php
require_once 'db_connect.php';

// Sanitize input data
function sanitize($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($data)));
}
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    // Calculate weeks separately
    $weeks = floor($diff->d / 7);
    $days = $diff->d % 7;

    $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => $weeks > 0 ? $weeks . ' week' . ($weeks > 1 ? 's' : '') : null,
        'd' => $days > 0 ? $days . ' day' . ($days > 1 ? 's' : '') : null,
        'h' => $diff->h ? $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') : null,
        'i' => $diff->i ? $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') : null,
        's' => $diff->s ? $diff->s . ' second' . ($diff->s > 1 ? 's' : '') : null,
    ];

    // Remove null values
    $string = array_filter($string);

    if (!$full) {
        $string = array_slice($string, 0, 1);
    }

    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


// Get user by ID
function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get all jobs with improved LIMIT handling
function getAllJobs($limit = null, $category = null, $location = null, $type = null, $search = null) {
    global $conn;
    
    $sql = "SELECT j.*, u.name as employer_name, c.name as category_name 
            FROM jobs j 
            JOIN users u ON j.employer_id = u.id 
            JOIN categories c ON j.category_id = c.id 
            WHERE j.status = 'active'";
    
    $params = [];
    
    if ($category) {
        $sql .= " AND j.category_id = ?";
        $params[] = $category;
    }
    
    if ($location) {
        $sql .= " AND j.location LIKE ?";
        $params[] = "%$location%";
    }
    
    if ($type) {
        $sql .= " AND j.type = ?";
        $params[] = $type;
    }
    
    if ($search) {
        $sql .= " AND (j.title LIKE ? OR j.description LIKE ? OR u.name LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY j.posted_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = (int)$limit;
    }
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameters with proper types
    foreach ($params as $key => $value) {
        $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $stmt->bindValue($key + 1, $value, $paramType);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get job by ID
function getJobById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT j.*, u.name as employer_name, c.name as category_name 
                           FROM jobs j 
                           JOIN users u ON j.employer_id = u.id 
                           JOIN categories c ON j.category_id = c.id 
                           WHERE j.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get categories with icons and descriptions
function getCategories() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM categories");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add icons and descriptions for each category
    $category_data = [
        'IT & Software' => ['icon' => 'fas fa-laptop-code', 'description' => 'Explore opportunities in software development, IT support, and more.'],
        'Marketing' => ['icon' => 'fas fa-bullhorn', 'description' => 'Find roles in digital marketing, branding, and advertising.'],
        'Finance' => ['icon' => 'fas fa-chart-line', 'description' => 'Discover jobs in accounting, financial analysis, and banking.'],
        'Healthcare' => ['icon' => 'fas fa-heartbeat', 'description' => 'Browse positions in nursing, medical research, and healthcare services.'],
        'Education' => ['icon' => 'fas fa-book-reader', 'description' => 'Search for teaching, training, and academic administration roles.']
    ];

    foreach ($categories as &$category) {
        $category_name = $category['name'];
        $category['icon'] = $category_data[$category_name]['icon'] ?? 'fas fa-briefcase';
        $category['description'] = $category_data[$category_name]['description'] ?? 'Explore opportunities in this field.';
    }

    return $categories;
}

// Post a new job
function postJob($employer_id, $title, $description, $category_id, $location, $salary, $type, $schedule, $benefits) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO jobs 
                          (employer_id, title, description, category_id, location, salary, type, schedule, benefits) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$employer_id, $title, $description, $category_id, $location, $salary, $type, $schedule, $benefits]);
}

// Apply for a job
function applyForJob($job_id, $user_id, $name, $email, $contact, $resume_path) {
    global $conn;
    
    // Check if already applied
    $stmt = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$job_id, $user_id]);
    if ($stmt->rowCount() > 0) {
        return "You have already applied for this job";
    }
    
    $stmt = $conn->prepare("INSERT INTO applications 
                          (job_id, user_id, name, email, contact, resume_path) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$job_id, $user_id, $name, $email, $contact, $resume_path]);
}

// Check if a job is saved by a user
function isJobSaved($user_id, $job_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM saved_jobs WHERE user_id = ? AND job_id = ?");
    $stmt->execute([$user_id, $job_id]);
    return $stmt->fetchColumn() > 0;
}

// Save job for later
function saveJob($job_id, $user_id) {
    global $conn;
    
    // Check if already saved
    $stmt = $conn->prepare("SELECT id FROM saved_jobs WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$job_id, $user_id]);
    if ($stmt->rowCount() > 0) {
        return "Job already saved";
    }
    
    $stmt = $conn->prepare("INSERT INTO saved_jobs (job_id, user_id) VALUES (?, ?)");
    return $stmt->execute([$job_id, $user_id]);
}

// Unsave a job for a user
function unsaveJob($user_id, $job_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM saved_jobs WHERE user_id = ? AND job_id = ?");
    return $stmt->execute([$user_id, $job_id]);
}

// Get saved jobs
function getSavedJobs($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT j.*, u.name as employer_name, c.name as category_name 
                           FROM saved_jobs sj 
                           JOIN jobs j ON sj.job_id = j.id 
                           JOIN users u ON j.employer_id = u.id 
                           JOIN categories c ON j.category_id = c.id 
                           WHERE sj.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get applications for a job seeker
function getJobSeekerApplications($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT a.*, j.title as job_title, u.name as employer_name, j.status as job_status 
                           FROM applications a 
                           JOIN jobs j ON a.job_id = j.id 
                           JOIN users u ON j.employer_id = u.id 
                           WHERE a.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get applications for an employer
function getEmployerApplications($employer_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT a.*, j.title as job_title, u.name as applicant_name, j.status as job_status 
                           FROM applications a 
                           JOIN jobs j ON a.job_id = j.id 
                           JOIN users u ON a.user_id = u.id 
                           WHERE j.employer_id = ?");
    $stmt->execute([$employer_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get employer's jobs
function getEmployerJobs($employer_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT j.*, c.name as category_name, 
                           (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.id) as application_count 
                           FROM jobs j 
                           JOIN categories c ON j.category_id = c.id 
                           WHERE j.employer_id = ?");
    $stmt->execute([$employer_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update application status
function updateApplicationStatus($application_id, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $application_id]);
}

// Send message
function sendMessage($sender_id, $receiver_id, $job_id, $subject, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO messages 
                          (sender_id, receiver_id, job_id, subject, message) 
                          VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$sender_id, $receiver_id, $job_id, $subject, $message]);
}

// Get user messages
function getUserMessages($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT m.*, 
                           u1.name as sender_name, 
                           u2.name as receiver_name,
                           j.title as job_title
                           FROM messages m
                           JOIN users u1 ON m.sender_id = u1.id
                           JOIN users u2 ON m.receiver_id = u2.id
                           LEFT JOIN jobs j ON m.job_id = j.id
                           WHERE m.receiver_id = ? OR m.sender_id = ?
                           ORDER BY m.sent_at DESC");
    $stmt->execute([$user_id, $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get unread message count
function getUnreadMessageCount($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = FALSE");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// Mark message as read
function markMessageAsRead($message_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE messages SET is_read = TRUE WHERE id = ?");
    return $stmt->execute([$message_id]);
}

// Admin functions
function getAllUsers() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE role != 'admin'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateUserStatus($user_id, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $user_id]);
}

function getAllJobsForAdmin() {
    global $conn;
    $stmt = $conn->prepare("SELECT j.*, u.name as employer_name, c.name as category_name 
                           FROM jobs j 
                           JOIN users u ON j.employer_id = u.id 
                           JOIN categories c ON j.category_id = c.id 
                           ORDER BY j.posted_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateJobStatus($job_id, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE jobs SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $job_id]);
}

// Report management functions
function getReports() {
    global $conn;
    
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    
    $sql = "SELECT r.*, 
                   u1.name as reporter_name, 
                   u2.name as user_name, 
                   j.title as job_title
            FROM reports r
            LEFT JOIN users u1 ON r.reporter_id = u1.id
            LEFT JOIN users u2 ON r.user_id = u2.id
            LEFT JOIN jobs j ON r.job_id = j.id";
    
    $where = [];
    $params = [];
    
    if ($filter === 'unresolved') {
        $where[] = "r.status = 'pending'";
    } elseif ($filter === 'jobs') {
        $where[] = "r.type = 'job'";
    } elseif ($filter === 'users') {
        $where[] = "r.type = 'user'";
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY r.created_at DESC LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $per_page;
    
    $stmt = $conn->prepare($sql);
    
    // Bind limit parameters as integers
    $stmt->bindValue(1, $offset, PDO::PARAM_INT);
    $stmt->bindValue(2, $per_page, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function resolveReport($report_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE reports SET status = 'resolved', resolved_at = NOW() WHERE id = ?");
    return $stmt->execute([$report_id]);
}

function deleteReport($report_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM reports WHERE id = ?");
    return $stmt->execute([$report_id]);
}

function banUser($user_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET status = 'banned' WHERE id = ?");
    return $stmt->execute([$user_id]);
}

function removeJob($job_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE jobs SET status = 'inactive' WHERE id = ?");
    return $stmt->execute([$job_id]);
}
?>