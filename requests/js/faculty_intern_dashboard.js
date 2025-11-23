// faculty_intern_dashboard.js - UPDATED to show assignment status

console.log('=== Faculty Intern Dashboard Script Loaded ===');

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM Loaded - Initializing ===');
    setupNavigation();
    loadCourses();
});

function setupNavigation() {
    console.log('Setting up navigation...');
    
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.dashboard-section');
    
    console.log('Found:', navLinks.length, 'nav links,', sections.length, 'sections');
    
    navLinks.forEach((link, index) => {
        const sectionName = link.getAttribute('data-section');
        console.log(`  Button ${index}: "${sectionName}"`);
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('data-section');
            
            console.log('>>> Button clicked! Section:', section);
            
            // Update buttons
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Update sections
            sections.forEach(s => s.classList.remove('active'));
            
            const targetId = `${section}-section`;
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                targetSection.classList.add('active');
                console.log('  Showing:', targetId);
            } else {
                console.error('  ERROR: Section not found:', targetId);
            }
            
            // Load data
            switch(section) {
                case 'courses': loadCourses(); break;
                case 'sessions': loadSessions(); break;
                case 'reports': loadReports(); break;
            }
        });
    });
    
    console.log('Navigation setup complete');
}

async function loadCourses() {
    console.log('=== loadCourses() called ===');
    
    const container = document.getElementById('courseList');
    if (!container) {
        console.error('ERROR: courseList container not found!');
        return;
    }
    
    container.innerHTML = '<p class="loading">Loading courses...</p>';
    
    try {
        const url = 'get_courses.php';
        console.log('Fetching:', url);
        
        const response = await fetch(url);
        console.log('Response status:', response.status);
        
        const text = await response.text();
        console.log('Response (first 300 chars):', text.substring(0, 300));
        
        const data = JSON.parse(text);
        console.log('Parsed data:', data);
        console.log('Number of courses:', data.courses ? data.courses.length : 0);
        
        if (data.success && data.courses && data.courses.length > 0) {
            console.log(`SUCCESS: Displaying ${data.courses.length} courses`);
            
            container.innerHTML = data.courses.map(course => {
                // Determine button state and text
                let buttonHtml = '';
                let assignmentBadge = '';
                
                if (course.is_my_course == 1) {
                    // This intern is already assigned
                    assignmentBadge = '<span class="badge badge-regular" style="margin-top: 10px;">✓ You are assigned to this course</span>';
                    buttonHtml = `<button class="btn btn-sm btn-secondary" disabled>Already Assigned</button>`;
                } else if (course.intern_id && course.intern_id != null) {
                    // Another intern is assigned
                    assignmentBadge = `<span class="badge" style="margin-top: 10px; background: #6c757d; color: white;">Intern: ${esc(course.intern_first_name)} ${esc(course.intern_last_name)}</span>`;
                    buttonHtml = `<button class="btn btn-sm btn-secondary" disabled>Already Has Intern</button>`;
                } else {
                    // No intern assigned - can join
                    buttonHtml = `
                        <button class="btn btn-sm btn-primary join-course-btn" 
                                data-course-id="${course.course_id}"
                                data-course-code="${esc(course.course_code)}">
                            Join as Faculty Intern
                        </button>
                    `;
                }
                
                return `
                    <div class="course-item ${course.is_my_course == 1 ? 'my-course' : ''}">
                        <h4>${esc(course.course_code)} - ${esc(course.course_name)}</h4>
                        <p>${esc(course.description || 'No description provided')}</p>
                        <div class="course-meta">
                            <small><strong>Faculty:</strong> ${esc(course.faculty_first_name)} ${esc(course.faculty_last_name)}</small>
                            <small><strong>Enrolled Students:</strong> ${course.enrolled_count || 0}</small>
                            ${course.credit_hours ? `<small><strong>Credits:</strong> ${course.credit_hours}</small>` : ''}
                        </div>
                        ${assignmentBadge}
                        ${buttonHtml}
                    </div>
                `;
            }).join('');
            
            // Attach join buttons
            document.querySelectorAll('.join-course-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const courseId = this.getAttribute('data-course-id');
                    const courseCode = this.getAttribute('data-course-code');
                    console.log('Join button clicked - Course ID:', courseId);
                    joinCourse(courseId, courseCode);
                });
            });
            
        } else {
            console.log('No courses found');
            container.innerHTML = '<p>No courses available at the moment.</p>';
        }
    } catch (error) {
        console.error('ERROR in loadCourses:', error);
        console.error('Error stack:', error.stack);
        container.innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
    }
}

async function joinCourse(courseId, courseCode) {
    console.log('=== joinCourse() called ===');
    console.log('  Course ID:', courseId);
    console.log('  Course Code:', courseCode);
    
    const result = await Swal.fire({
        title: 'Join as Faculty Intern?',
        html: `
            <p>Join <strong>${courseCode}</strong> as Faculty Intern?</p>
            <p style="color: #666; font-size: 14px; margin-top: 10px;">
                You will be assigned to assist with this course and can mark attendance.
            </p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Join',
        confirmButtonColor: '#722f37'
    });
    
    if (!result.isConfirmed) {
        console.log('User cancelled');
        return;
    }
    
    try {
        console.log('Sending join request...');
        
        const response = await fetch('join_course_intern.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ course_id: parseInt(courseId) })
        });
        
        const text = await response.text();
        console.log('Response:', text);
        
        const data = JSON.parse(text);
        console.log('Parsed:', data);
        
        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Assigned!',
                text: data.message,
                timer: 2000
            });
            loadCourses(); // Reload to show updated status
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: data.message,
                confirmButtonColor: '#722f37'
            });
        }
    } catch (error) {
        console.error('ERROR:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message,
            confirmButtonColor: '#722f37'
        });
    }
}

function loadSessions() {
    console.log('>>> loadSessions() called');
    const container = document.getElementById('sessionList');
    if (!container) {
        console.error('ERROR: sessionList not found');
        return;
    }
    
    container.innerHTML = `
        <p>Sessions feature coming soon. You will be able to:</p>
        <ul>
            <li>View all course sessions</li>
            <li>Mark student attendance</li>
            <li>View session details</li>
        </ul>
    `;
}

function loadReports() {
    console.log('>>> loadReports() called');
    const container = document.getElementById('reportList');
    if (!container) {
        console.error('ERROR: reportList not found');
        return;
    }
    
    container.innerHTML = `
        <p>Reports feature coming soon. You will be able to:</p>
        <ul>
            <li>Create attendance reports</li>
            <li>Create performance reports</li>
            <li>View report history</li>
        </ul>
    `;
}

function esc(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
}

// Add CSS for assigned courses
const style = document.createElement('style');
style.textContent = `
    .course-item.my-course {
        border-color: #28a745;
        background: #f0fff4;
    }
    
    .course-item.my-course h4 {
        color: #28a745;
    }
`;
document.head.appendChild(style);

console.log('=== Faculty Intern Dashboard Script Complete ===');