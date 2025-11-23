<?php
// student_dashboard.php - Student dashboard page

require_once 'auth_check.php';

// Check if user is a student
if ($_SESSION['role'] !== 'student') {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Attendance Management</title>
    <link rel="stylesheet" href="requests/css/style.css">
    <link rel="stylesheet" href="requests/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <h2>📚 Attendance Management - Student</h2>
        </div>
        <div class="nav-menu">
            <span>Welcome, <?php echo htmlspecialchars($first_name); ?>!</span>
            <button class="btn btn-secondary logout-btn">Logout</button>
        </div>
    </nav>
    
    <div class="container">
        <header class="dashboard-header">
            <h1>Student Dashboard</h1>
            <nav class="section-nav">
                <button class="nav-link active" data-section="courses">My Courses</button>
                <button class="nav-link" data-section="available">Available Courses</button>
                <button class="nav-link" data-section="pending">Pending Requests</button>
                <button class="nav-link" data-section="schedule">Session Schedule</button>
                <button class="nav-link" data-section="grades">Grades & Reports</button>
            </nav>
        </header>
        
        <!-- My Courses Section -->
        <section id="courses-section" class="dashboard-section active">
            <div class="card">
                <header class="card-header">
                    <h3>My Enrolled Courses</h3>
                </header>
                <div id="enrolledCourses">
                    <p class="loading">Loading courses...</p>
                </div>
            </div>
        </section>
        
        <!-- Available Courses Section -->
        <section id="available-section" class="dashboard-section">
            <div class="card">
                <header class="card-header">
                    <h3>Available Courses</h3>
                    <div class="filter-info">
                        <small>Click a course to join as Regular, Auditor, or Observer</small>
                    </div>
                </header>
                <div id="availableCourses">
                    <p class="loading">Loading available courses...</p>
                </div>
            </div>
        </section>
        
        <!-- Pending Requests Section -->
        <section id="pending-section" class="dashboard-section">
            <div class="card">
                <header class="card-header">
                    <h3>Pending Enrollment Requests</h3>
                </header>
                <div id="pendingRequests">
                    <p class="loading">Loading pending requests...</p>
                </div>
            </div>
        </section>
        
        <!-- Session Schedule Section -->
        <section id="schedule-section" class="dashboard-section">
            <div class="card">
                <header class="card-header">
                    <h3>Upcoming Sessions</h3>
                </header>
                <div id="sessionSchedule">
                    <p class="loading">Loading schedule...</p>
                </div>
            </div>
        </section>
        
        <!-- Grades & Reports Section -->
        <section id="grades-section" class="dashboard-section">
            <div class="dashboard-grid">
                <div class="card">
                    <h3>My Grades</h3>
                    <div id="gradesList">
                        <p class="loading">Loading grades...</p>
                    </div>
                </div>
                
                <div class="card">
                    <h3>Feedback & Reports</h3>
                    <div id="feedbackList">
                        <p class="loading">Loading feedback...</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <!-- Join Course Modal -->
    <div id="joinCourseModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Join Course</h2>
            <div id="courseInfo" style="margin-bottom: 20px;">
                <!-- Course info will be inserted here -->
            </div>
            <form id="joinCourseForm">
                <input type="hidden" id="selected_course_id" name="course_id">
                
                <div class="form-group">
                    <label>Select Enrollment Type</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="enrollment_type" value="regular" checked>
                            <div class="radio-content">
                                <strong>Regular Student</strong>
                                <small>Full enrollment with grades and credits</small>
                            </div>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="enrollment_type" value="auditor">
                            <div class="radio-content">
                                <strong>Auditor</strong>
                                <small>Attend classes without grades or credits</small>
                            </div>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="enrollment_type" value="observer">
                            <div class="radio-content">
                                <strong>Observer</strong>
                                <small>Observe classes for learning purposes</small>
                            </div>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        </div>
    </div>
    
    <script src="requests/js/logout.js"></script>
    <script src="requests/js/student_dashboard.js"></script>
</body>
</html>