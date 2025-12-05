# Attendance System Implementation Guide

## Overview

The attendance system has been fully implemented with the following features:

### For Faculty and Faculty Interns:
1. **Create and Manage Class Sessions**
   - Create sessions with date, time, topic, and location
   - Automatic generation of 6-digit attendance codes
   - Codes expire 2 hours after session end time

2. **Mark Attendance**
   - Manually mark attendance for enrolled students
   - Mark as Present, Late, or Absent
   - View attendance count per session

### For Students:
1. **Check In with Code**
   - Use 6-digit attendance code provided by instructor
   - Automatic detection of late check-ins (after 15 minutes)
   - Real-time feedback on check-in status

2. **View Attendance Reports**
   - **Overall Report**: Shows attendance percentage, total sessions, present/late/absent counts
   - **Daily Report**: Shows attendance for a specific date
   - Detailed session-by-session breakdown

## Database Setup

### Step 1: Run Database Updates

Execute the SQL file to add required fields:

```sql
-- Run database_updates.sql
```

This will add:
- `attendance_code` field to `sessions` table
- `code_expires_at` field to `sessions` table
- `created_by` field to `sessions` table
- `checked_in_at` field to `attendance` table
- `check_in_method` field to `attendance` table

### Step 2: Verify Tables

Make sure these tables exist:
- `sessions` - Stores class sessions
- `attendance` - Stores attendance records
- `courses` - Course information
- `Enrollment` - Student enrollments
- `users` - User accounts

## File Structure

### Backend PHP Files:
- `create_session.php` - Create new sessions
- `get_sessions.php` - Get sessions for a course
- `mark_attendance.php` - Mark attendance manually (faculty/intern)
- `check_in_code.php` - Student self-check-in with code
- `get_attendance_report.php` - Get attendance reports
- `get_enrolled_students.php` - Get enrolled students list

### Frontend JavaScript Files:
- `requests/js/session_management.js` - Session management for faculty/interns
- `requests/js/student_attendance.js` - Student attendance features

### Updated Dashboard Files:
- `faculty_dashboard.php` - Added session management section
- `faculty_intern_dashboard.php` - Added session management section
- `student_dashboard.php` - Added check-in and reports sections

## Usage Guide

### For Faculty/Interns:

1. **Creating a Session:**
   - Go to "Sessions" section in dashboard
   - Select a course from dropdown
   - Click "Create New Session"
   - Fill in date, start time, end time
   - Optionally add topic and location
   - Attendance code is automatically generated

2. **Marking Attendance:**
   - Select a course in Sessions section
   - Click "Mark Attendance" on a session
   - Select status (Present/Late/Absent) for each student
   - Click "Save Attendance"

### For Students:

1. **Checking In with Code:**
   - Go to "Check In with Code" section
   - Click "Check In with Code" button
   - Enter the 6-digit code provided by instructor
   - System automatically marks as Present or Late

2. **Viewing Reports:**
   - Go to "Attendance Reports" section
   - Select a course from dropdown
   - Choose "Overall" for full report or "Today" for daily report
   - View attendance percentage and detailed breakdown

## Features

### Attendance Code System:
- 6-digit random codes generated per session
- Codes expire 2 hours after session end time
- Students can only check in once per session
- Late check-ins (after 15 minutes) are automatically marked as "Late"

### Permission System:
- Faculty can only manage sessions for their own courses
- Faculty Interns can only manage sessions for assigned courses
- Students can only check in for enrolled courses
- All actions are validated against enrollment status

### Reporting:
- Real-time attendance statistics
- Color-coded status indicators (Green=Present, Orange=Late, Red=Absent)
- Session-by-session breakdown
- Overall attendance percentage calculation

## Important Notes

1. **Faculty/Intern Name Display:**
   - When students view enrolled courses, faculty and intern names are already displayed
   - This is handled in `get_courses.php` which joins with users table

2. **Session Management:**
   - Sessions are course-specific
   - Each session generates a unique attendance code
   - Codes are shown to faculty/interns when creating sessions

3. **Attendance Tracking:**
   - Manual marking by faculty/interns
   - Self-check-in by students using codes
   - Both methods are tracked with `check_in_method` field

## Testing

1. **Test Session Creation:**
   - Login as faculty or intern
   - Create a session for a course
   - Verify attendance code is generated

2. **Test Student Check-In:**
   - Login as student
   - Use the attendance code from step 1
   - Verify check-in is recorded

3. **Test Reports:**
   - View overall attendance report
   - View daily attendance report
   - Verify statistics are accurate

## Troubleshooting

**Issue: Attendance code not showing**
- Check if `attendance_code` field exists in `sessions` table
- Run `database_updates.sql` if needed

**Issue: Cannot mark attendance**
- Verify user has permission (faculty/intern for the course)
- Check if student is enrolled and approved

**Issue: Check-in fails**
- Verify code hasn't expired
- Check if student is enrolled in the course
- Ensure code is exactly 6 digits

## Next Steps

After implementing:
1. Run `database_updates.sql` on your database
2. Test session creation as faculty/intern
3. Test student check-in with codes
4. Verify attendance reports display correctly

