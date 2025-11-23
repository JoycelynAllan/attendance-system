// faculty_dashboard.js - UPDATED with Faculty Intern display

console.log('Faculty dashboard script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Faculty Dashboard');
    setupNavigation();
    loadMyCourses();
    loadEnrollmentRequests();
    setupModals();
});

function setupNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.dashboard-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const sectionName = this.dataset.section;
            console.log('Navigating to:', sectionName);
            
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            sections.forEach(s => s.classList.remove('active'));
            const targetSection = document.getElementById(`${sectionName}-section`);
            if (targetSection) targetSection.classList.add('active');
            
            switch(sectionName) {
                case 'courses': loadMyCourses(); break;
                case 'enrollment': loadEnrollmentRequests(); break;
                case 'sessions': loadSessions(); break;
                case 'reports': break;
            }
        });
    });
}

function setupModals() {
    const modal = document.getElementById('createCourseModal');
    const btn = document.getElementById('createCourseBtn');
    const span = modal.querySelector('.close');
    const form = document.getElementById('createCourseForm');
    
    if (btn) btn.onclick = () => modal.style.display = 'block';
    if (span) span.onclick = () => modal.style.display = 'none';
    
    window.onclick = (e) => {
        if (e.target == modal) modal.style.display = 'none';
    };
    
    if (form) form.addEventListener('submit', handleCreateCourse);
}

async function loadMyCourses() {
    console.log('Loading faculty courses...');
    
    try {
        const response = await fetch('get_courses.php');
        const text = await response.text();
        const data = JSON.parse(text);
        const container = document.getElementById('myCourses');
        
        if (!container) return;
        
        if (data.success && data.courses && data.courses.length > 0) {
            container.innerHTML = data.courses.map(course => {
                // Build intern display
                let internInfo = '';
                if (course.intern_first_name && course.intern_last_name) {
                    internInfo = `<small><strong>Faculty Intern:</strong> ${esc(course.intern_first_name)} ${esc(course.intern_last_name)}</small>`;
                } else {
                    internInfo = `<small style="color: #999;"><em>No faculty intern assigned yet</em></small>`;
                }
                
                return `
                    <div class="course-item">
                        <h4>${esc(course.course_code)} - ${esc(course.course_name)}</h4>
                        <p>${esc(course.description || 'No description')}</p>
                        <div class="course-meta">
                            <small><strong>Credit Hours:</strong> ${course.credit_hours || 'N/A'}</small>
                            <small><strong>Enrolled Students:</strong> ${course.enrolled_count || 0}</small>
                            ${course.semester ? `<small><strong>Semester:</strong> ${esc(course.semester)}</small>` : ''}
                            ${internInfo}
                        </div>
                    </div>
                `;
            }).join('');
        } else {
            container.innerHTML = '<p>You have not created any courses yet. Click "Create New Course" to get started!</p>';
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('myCourses').innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
    }
}

async function loadEnrollmentRequests() {
    console.log('Loading enrollment requests...');
    
    try {
        const response = await fetch('manage_enrollment.php');
        const text = await response.text();
        const data = JSON.parse(text);
        const container = document.getElementById('enrollmentRequests');
        
        if (!container) return;
        
        if (data.success && data.requests && data.requests.length > 0) {
            container.innerHTML = data.requests.map(request => `
                <div class="request-item">
                    <div class="request-info">
                        <h4>${esc(request.first_name)} ${esc(request.last_name)}</h4>
                        <p><strong>Course:</strong> ${esc(request.course_code)} - ${esc(request.course_name)}</p>
                        <div class="course-meta">
                            <small><strong>Email:</strong> ${esc(request.email)}</small>
                            <small><strong>Username:</strong> ${esc(request.username)}</small>
                            <small><strong>Type:</strong> <span class="badge badge-${request.enrollment_type}">${request.enrollment_type}</span></small>
                            <small><strong>Requested:</strong> ${new Date(request.requested_at).toLocaleString()}</small>
                        </div>
                    </div>
                    <div class="request-actions">
                        <button class="btn btn-sm btn-success approve-btn" data-enrollment-id="${request.enrollment_id}">
                            Approve
                        </button>
                        <button class="btn btn-sm btn-danger reject-btn" data-enrollment-id="${request.enrollment_id}">
                            Reject
                        </button>
                    </div>
                </div>
            `).join('');
            
            document.querySelectorAll('.approve-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    handleEnrollmentAction(this.getAttribute('data-enrollment-id'), 'approve');
                });
            });
            
            document.querySelectorAll('.reject-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    handleEnrollmentAction(this.getAttribute('data-enrollment-id'), 'reject');
                });
            });
        } else {
            container.innerHTML = '<p>No pending enrollment requests.</p>';
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('enrollmentRequests').innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
    }
}

function loadSessions() {
    const container = document.getElementById('sessionList');
    if (container) {
        container.innerHTML = `
            <p>Session management coming soon. You will be able to:</p>
            <ul>
                <li>Create class sessions</li>
                <li>View session attendance</li>
                <li>Manage session schedule</li>
            </ul>
        `;
    }
}

async function handleCreateCourse(e) {
    e.preventDefault();
    console.log('Creating course...');
    
    const formData = {
        course_code: document.getElementById('course_code').value.trim(),
        course_name: document.getElementById('course_name').value.trim(),
        description: document.getElementById('description').value.trim(),
        credit_hours: document.getElementById('credit_hours').value
    };
    
    try {
        const response = await fetch('create_course.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Course Created!',
                text: data.message,
                timer: 2000
            });
            
            document.getElementById('createCourseModal').style.display = 'none';
            document.getElementById('createCourseForm').reset();
            await loadMyCourses();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    }
}

async function handleEnrollmentAction(enrollmentId, action) {
    console.log(`Handling enrollment ${action}...`);
    
    try {
        const result = await Swal.fire({
            title: `${action.charAt(0).toUpperCase() + action.slice(1)} Request?`,
            text: `Are you sure you want to ${action} this enrollment request?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
            confirmButtonText: `Yes, ${action}`
        });
        
        if (!result.isConfirmed) return;
        
        const response = await fetch('manage_enrollment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                enrollment_id: parseInt(enrollmentId),
                action: action
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 2000
            });
            
            await loadEnrollmentRequests();
            await loadMyCourses();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: data.message
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message
        });
    }
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