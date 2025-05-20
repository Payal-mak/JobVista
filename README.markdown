# Job Portal Project

## Introduction

The **Job Portal** is a web-based platform designed to connect job seekers with employers, facilitating an efficient job search and hiring process. Built using PHP, MySQL, and styled with Tailwind CSS and Font Awesome, this project provides a user-friendly interface for job seekers to browse, apply for, and save jobs, while employers can post job listings, manage applications, and communicate with applicants. The platform also includes an admin panel for managing users, jobs, and reports, ensuring smooth operation and oversight.

### Key Features
- **User Roles**:
  - **Job Seekers**: Browse jobs, apply for positions, save jobs for later, and manage applications and messages.
  - **Employers**: Post job listings, view applications, and communicate with job seekers.
  - **Admin**: Manage users, jobs, and reports, with the ability to ban users or remove jobs.
- **Job Search and Filtering**: Search jobs by category, location, type, or keywords.
- **Messaging System**: Employers and job seekers can communicate via a messaging system.
- **Responsive Design**: Built with Tailwind CSS for a responsive, mobile-friendly experience.
- **Database-Driven**: Uses MySQL for storing users, jobs, applications, messages, and more.

### Technologies Used
- **Backend**: PHP (Procedural)
- **Database**: MySQL/MariaDB
- **Frontend**:
  - Tailwind CSS (via CDN) for styling
  - Font Awesome (via CDN) for icons
- **Server**: XAMPP (Local development environment)

## Working Flow of the Project

### 1. User Registration and Login
- Users (job seekers or employers) register via the registration page (`register.php`), selecting their role.
- Upon registration, user details are stored in the `users` table.
- Users log in via `login.php`, where their credentials are verified against the `users` table.
- Sessions are used to maintain user authentication (`auth.php` handles route protection).

### 2. Job Seeker Workflow
- **Dashboard (`job_seeker/dashboard.php`)**:
  - Displays a summary of applications, saved jobs, and unread messages.
  - Shows recommended jobs (limited to 3) with options to apply or save.
  - Lists categories for browsing jobs by category.
  - Displays recent applications and saved jobs in a card-style layout.
- **Browsing Jobs (`jobs.php`)**:
  - Job seekers can filter jobs by category, location, type, or search term.
  - Jobs are fetched using `getAllJobs()` from `functions.php`.
- **Applying for Jobs**:
  - Job seekers can apply via `job_details.php`, uploading a resume and providing contact details.
  - Applications are stored in the `applications` table.
- **Saving Jobs**:
  - Job seekers can save jobs for later by clicking the bookmark button.
  - Saved jobs are stored in the `saved_jobs` table and displayed on the dashboard.
- **Messaging**:
  - Job seekers can receive messages from employers, tracked in the `messages` table.
  - Unread messages are counted using `getUnreadMessageCount()`.

### 3. Employer Workflow
- **Dashboard (`employer/dashboard.php`)**:
  - Employers can view their posted jobs and the number of applications for each.
  - Displays a list of applications received for their jobs.
- **Posting Jobs**:
  - Employers can post new jobs via `post_job.php`, providing details like title, description, category, location, salary, type, schedule, and benefits.
  - Jobs are stored in the `jobs` table with a status of `active`.
- **Managing Applications**:
  - Employers can view applications for their jobs and update the status (e.g., pending, shortlisted, rejected, accepted) via `updateApplicationStatus()`.
- **Messaging**:
  - Employers can send messages to job seekers regarding their applications, stored in the `messages` table.

### 4. Admin Workflow
- **Dashboard (`admin/dashboard.php`)**:
  - Admins can view all users and jobs on the platform.
  - Manage reports submitted by users about jobs or other users.
- **User Management**:
  - Admins can ban users (`banUser()`) or update their status (`updateUserStatus()`).
- **Job Management**:
  - Admins can update job status (`updateJobStatus()`) or remove jobs (`removeJob()`).
- **Report Management**:
  - Admins can view, resolve, or delete reports (`getReports()`, `resolveReport()`, `deleteReport()`).

### 5. Database Interactions
- **Tables**:
  - `users`: Stores user information (id, name, email, password, role, status).
  - `jobs`: Stores job listings (id, employer_id, title, description, category_id, location, salary, type, schedule, benefits, status, posted_at).
  - `categories`: Stores job categories (id, name).
  - `applications`: Stores job applications (id, job_id, user_id, resume_path, contact, status, applied_at, name, email).
  - `saved_jobs`: Stores saved jobs for job seekers (id, job_id, user_id, saved_at).
  - `messages`: Stores messages between users (id, sender_id, receiver_id, job_id, subject, message, sent_at, is_read).
  - `reports`: Stores reports submitted by users (id, reporter_id, user_id, job_id, type, description, status, created_at, resolved_at).
- **Functions (`includes/functions.php`)**:
  - Handles all database interactions, such as fetching jobs (`getAllJobs()`), saving jobs (`saveJob()`), sending messages (`sendMessage()`), and more.
  - Uses PDO for secure database queries with prepared statements.

### 6. Frontend Rendering
- Pages are rendered using PHP, with HTML templates styled using Tailwind CSS.
- Font Awesome icons are used for visual elements (e.g., bookmarks, locations, briefcases).
- The layout is responsive, adapting to different screen sizes using Tailwind’s grid system.

## Setup Instructions

### Prerequisites
- XAMPP (or any PHP/MySQL server environment)
- A web browser
- Internet connection (for CDN links to Tailwind CSS and Font Awesome)

### Installation Steps
1. **Clone the Project**:
   - Copy the project files into your XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs\Job Portal`).

2. **Set Up the Database**:
   - Start XAMPP and ensure Apache and MySQL are running.
   - Open phpMyAdmin (http://localhost/phpmyadmin) and create a new database named `job_portal`.
   - Import the database schema by running the following SQL (or use the provided `database.sql` file if available):
     ```sql
     CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(255) NOT NULL,
         email VARCHAR(255) NOT NULL UNIQUE,
         password VARCHAR(255) NOT NULL,
         role ENUM('job_seeker', 'employer', 'admin') NOT NULL,
         status ENUM('active', 'banned') DEFAULT 'active',
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );

     CREATE TABLE categories (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(255) NOT NULL
     );

     CREATE TABLE jobs (
         id INT AUTO_INCREMENT PRIMARY KEY,
         employer_id INT NOT NULL,
         title VARCHAR(255) NOT NULL,
         description TEXT NOT NULL,
         category_id INT NOT NULL,
         location VARCHAR(255) NOT NULL,
         salary DECIMAL(10, 2) NOT NULL,
         type ENUM('full-time', 'part-time', 'contract', 'internship') NOT NULL,
         schedule VARCHAR(255),
         benefits TEXT,
         status ENUM('active', 'inactive') DEFAULT 'active',
         posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (employer_id) REFERENCES users(id),
         FOREIGN KEY (category_id) REFERENCES categories(id)
     );

     CREATE TABLE applications (
         id INT AUTO_INCREMENT PRIMARY KEY,
         job_id INT NOT NULL,
         user_id INT NOT NULL,
         resume_path VARCHAR(255) NOT NULL,
         contact VARCHAR(50),
         status ENUM('pending', 'shortlisted', 'rejected', 'accepted') DEFAULT 'pending',
         applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         name VARCHAR(255),
         email VARCHAR(255),
         FOREIGN KEY (job_id) REFERENCES jobs(id),
         FOREIGN KEY (user_id) REFERENCES users(id)
     );

     CREATE TABLE saved_jobs (
         id INT AUTO_INCREMENT PRIMARY KEY,
         job_id INT NOT NULL,
         user_id INT NOT NULL,
         saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         FOREIGN KEY (job_id) REFERENCES jobs(id),
         FOREIGN KEY (user_id) REFERENCES users(id)
     );

     CREATE TABLE messages (
         id INT AUTO_INCREMENT PRIMARY KEY,
         sender_id INT NOT NULL,
         receiver_id INT NOT NULL,
         job_id INT,
         subject VARCHAR(255),
         message TEXT NOT NULL,
         sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         is_read TINYINT(1) DEFAULT 0,
         FOREIGN KEY (sender_id) REFERENCES users(id),
         FOREIGN KEY (receiver_id) REFERENCES users(id),
         FOREIGN KEY (job_id) REFERENCES jobs(id)
     );

     CREATE TABLE reports (
         id INT AUTO_INCREMENT PRIMARY KEY,
         reporter_id INT NOT NULL,
         user_id INT,
         job_id INT,
         type ENUM('user', 'job') NOT NULL,
         description TEXT NOT NULL,
         status ENUM('pending', 'resolved') DEFAULT 'pending',
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
         resolved_at TIMESTAMP,
         FOREIGN KEY (reporter_id) REFERENCES users(id),
         FOREIGN KEY (user_id) REFERENCES users(id),
         FOREIGN KEY (job_id) REFERENCES jobs(id)
     );
     ```
   - Optionally, insert sample data into the `categories` table:
     ```sql
     INSERT INTO categories (name) VALUES ('IT & Software'), ('Marketing'), ('Finance'), ('Healthcare'), ('Education');
     ```

3. **Configure Database Connection**:
   - Open `includes/db_connect.php` and update the database credentials:
     ```php
     $dsn = 'mysql:host=localhost;dbname=job_portal';
     $username = 'root';
     $password = ''; // Default XAMPP password is empty
     try {
         $conn = new PDO($dsn, $username, $password);
         $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
         die("Connection failed: " . $e->getMessage());
     }
     ```

4. **Start the Server**:
   - Ensure XAMPP is running (Apache and MySQL).
   - Open your browser and navigate to `http://localhost/Job Portal`.

5. **Register and Test**:
   - Register as a job seeker, employer, and admin to test all functionalities.
   - Log in as each user type to explore their respective dashboards.

## Project Structure
- **`includes/`**: Core files for configuration, authentication, and functions.
  - `config.php`: Base configuration (e.g., base URL).
  - `db_connect.php`: Database connection using PDO.
  - `auth.php`: Authentication and route protection functions.
  - `functions.php`: Utility functions for database interactions (e.g., `getAllJobs()`, `saveJob()`).
  - `header.php`, `footer.php`: Common header and footer templates.
- **`job_seeker/`**: Job seeker-specific pages.
  - `dashboard.php`: Job seeker dashboard with stats, recommended jobs, categories, applications, and saved jobs.
  - `sidebar.php`: Sidebar navigation for job seekers.
- **`employer/`**: Employer-specific pages.
  - `dashboard.php`: Employer dashboard for managing jobs and applications.
- **`admin/`**: Admin-specific pages.
  - `dashboard.php`: Admin dashboard for managing users, jobs, and reports.
- **`jobs.php`**: Public page for browsing all jobs.
- **`job_details.php`**: Displays job details and application form.
- **`register.php`, `login.php`**: User registration and login pages.

## Contributing
- Feel free to fork the repository and submit pull requests for improvements.
- Report issues or suggest features by creating an issue on the project repository (if hosted on GitHub).

## Known Issues
- The messaging system lacks a dedicated inbox page; messages are currently only counted on the dashboard.
- File uploads (e.g., resumes) are stored as paths; ensure the upload directory is writable and secure.

## Future Enhancements
- Add a dedicated messaging inbox page for job seekers and employers.
- Implement job recommendations based on user preferences or skills.
- Add pagination for job listings and applications.
- Enhance security with CSRF tokens and input validation.

## License
This project is licensed under the MIT License. Feel free to use, modify, and distribute it as needed.

---

**Last Updated**: May 20, 2025