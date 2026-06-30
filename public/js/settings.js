/* ============================================
   HAFLET FLOW - SETTINGS SYSTEM JAVASCRIPT
   ============================================ */

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Show success notification
 */
function showSuccessNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification notification-success';
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">✓</span>
            <p>${message}</p>
        </div>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Show error notification
 */
function showErrorNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification notification-error';
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">✕</span>
            <p>${message}</p>
        </div>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Format form data
 */
function formatFormData(formElement) {
    const formData = new FormData(formElement);
    const data = {};

    for (let [key, value] of formData.entries()) {
        if (key.includes('[]')) {
            const arrayKey = key.replace('[]', '');
            if (!data[arrayKey]) {
                data[arrayKey] = [];
            }
            data[arrayKey].push(value);
        } else {
            data[key] = value;
        }
    }

    return data;
}

/**
 * Get CSRF token
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
           document.querySelector('input[name="_token"]')?.value;
}

// ============================================
// PROFILE UPDATE FUNCTIONS
// ============================================

/**
 * Update profile information
 */
function updateProfile(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('profileForm'));
    const csrfToken = getCsrfToken();

    fetch(e.target.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessNotification(data.message || 'Profile updated successfully!');
            if (data.completion) {
                updateCompletionBar(data.completion);
            }
        } else {
            showErrorNotification(data.message || 'Error updating profile');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorNotification('An error occurred. Please try again.');
    });
}

/**
 * Update completion bar
 */
function updateCompletionBar(percentage) {
    const bar = document.querySelector('.completion-fill');
    if (bar) {
        bar.style.width = percentage + '%';
        const text = document.querySelector('.strength-text');
        if (text) {
            text.textContent = `${percentage}% Complete`;
        }
    }
}

/**
 * Change password
 */
function changePassword(e) {
    e.preventDefault();
    const currentPassword = document.querySelector('input[name="current_password"]').value;
    const newPassword = document.querySelector('input[name="new_password"]').value;
    const confirmPassword = document.querySelector('input[name="new_password_confirmation"]').value;

    if (newPassword !== confirmPassword) {
        showErrorNotification('Passwords do not match!');
        return;
    }

    if (newPassword.length < 8) {
        showErrorNotification('Password must be at least 8 characters!');
        return;
    }

    const formData = new FormData(document.getElementById('passwordForm'));
    const csrfToken = getCsrfToken();

    fetch(e.target.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessNotification(data.message || 'Password changed successfully!');
            document.getElementById('passwordForm').reset();
        } else {
            showErrorNotification(data.message || 'Error changing password');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorNotification('An error occurred. Please try again.');
    });
}

// ============================================
// SETTINGS UPDATE FUNCTIONS
// ============================================

/**
 * Update settings (generic)
 */
function updateSettings(e, routeName) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const csrfToken = getCsrfToken();

    fetch(form.action || `/${routeName}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessNotification(data.message || 'Settings updated successfully!');
        } else {
            showErrorNotification(data.message || 'Error updating settings');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorNotification('An error occurred. Please try again.');
    });
}

/**
 * Update notifications
 */
function updateNotifications(e) {
    updateSettings(e, 'notifications');
}

/**
 * Update appearance
 */
function updateAppearance(e) {
    e.preventDefault();
    updateSettings(e, 'appearance');
    // Reload after a delay to apply theme changes
    setTimeout(() => location.reload(), 500);
}

/**
 * Update business information
 */
function updateBusiness(e) {
    updateSettings(e, 'business');
}

/**
 * Update vendor preferences
 */
function updateVendorPreferences(e) {
    updateSettings(e, 'vendors');
}

/**
 * Update skills
 */
function updateSkills(e) {
    updateSettings(e, 'skills');
}

/**
 * Update availability
 */
function updateAvailability(e) {
    updateSettings(e, 'availability');
}

// ============================================
// FILE UPLOAD FUNCTIONS
// ============================================

/**
 * Handle photo upload
 */
function handlePhotoUpload(inputId, routeUrl) {
    const input = document.getElementById(inputId);
    if (!input || !input.files.length) return;

    const file = input.files[0];

    // Validate file
    if (!file.type.startsWith('image/')) {
        showErrorNotification('Please select an image file');
        return;
    }

    if (file.size > 5 * 1024 * 1024) { // 5MB
        showErrorNotification('File size must be less than 5MB');
        return;
    }

    const formData = new FormData();
    formData.append('photo', file);
    const csrfToken = getCsrfToken();

    fetch(routeUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessNotification(data.message || 'Photo uploaded successfully!');
            const img = document.querySelector(`#${inputId.replace('Input', 'Img')}`);
            if (img && data.url) {
                img.src = data.url;
            }
            input.value = '';
        } else {
            showErrorNotification(data.message || 'Error uploading photo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorNotification('An error occurred. Please try again.');
    });
}

/**
 * Handle cover photo upload
 */
function handleCoverPhotoUpload(inputId, routeUrl) {
    const input = document.getElementById(inputId);
    if (!input || !input.files.length) return;

    const file = input.files[0];

    // Validate file
    if (!file.type.startsWith('image/')) {
        showErrorNotification('Please select an image file');
        return;
    }

    if (file.size > 10 * 1024 * 1024) { // 10MB
        showErrorNotification('File size must be less than 10MB');
        return;
    }

    const formData = new FormData();
    formData.append('cover_photo', file);
    const csrfToken = getCsrfToken();

    fetch(routeUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessNotification(data.message || 'Cover photo updated!');
            if (data.url) {
                location.reload();
            }
        } else {
            showErrorNotification(data.message || 'Error uploading cover photo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorNotification('An error occurred. Please try again.');
    });
}

// ============================================
// PHOTO UPLOAD EVENT LISTENERS
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Profile photo upload
    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const route = this.getAttribute('data-route') || '/profile/photo';
            handlePhotoUpload('photoInput', route);
        });
    }

    // Cover photo upload
    const coverInput = document.getElementById('coverInput');
    if (coverInput) {
        coverInput.addEventListener('change', function() {
            const route = this.getAttribute('data-route') || '/profile/cover';
            handleCoverPhotoUpload('coverInput', route);
        });
    }
});

// ============================================
// QUIET HOURS TOGGLE
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const quietHoursCheckbox = document.querySelector('input[name="enable_quiet_hours"]');
    const quietTimesSection = document.getElementById('quietTimesSection');

    if (quietHoursCheckbox && quietTimesSection) {
        quietHoursCheckbox.addEventListener('change', function() {
            quietTimesSection.style.display = this.checked ? 'grid' : 'none';
        });

        // Initialize
        if (quietHoursCheckbox.checked) {
            quietTimesSection.style.display = 'grid';
        }
    }
});

// ============================================
// RATING SLIDER
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const ratingSlider = document.getElementById('ratingThreshold');
    const ratingValue = document.getElementById('ratingValue');

    if (ratingSlider && ratingValue) {
        ratingSlider.addEventListener('input', function(e) {
            ratingValue.textContent = e.target.value;
        });
    }
});

// ============================================
// PREVIEW UPDATES
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Theme mode preview
    document.querySelectorAll('input[name="theme_mode"]').forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById('previewTheme');
            if (preview) {
                preview.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });
    });

    // Color scheme preview
    document.querySelectorAll('input[name="color_scheme"]').forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById('previewColor');
            if (preview) {
                preview.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });
    });

    // Font size preview
    document.querySelectorAll('input[name="font_size"]').forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById('previewFont');
            if (preview) {
                preview.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });
    });

    // Dashboard layout preview
    document.querySelectorAll('input[name="dashboard_layout"]').forEach(input => {
        input.addEventListener('change', function() {
            const preview = document.getElementById('previewLayout');
            if (preview) {
                preview.textContent = this.value.charAt(0).toUpperCase() + this.value.slice(1);
            }
        });
    });
});

// ============================================
// CERTIFICATIONS MANAGEMENT
// ============================================

/**
 * Add certification
 */
function addCertification() {
    const input = document.getElementById('certInput');
    const value = input.value.trim();

    if (!value) {
        showErrorNotification('Please enter a certification');
        return;
    }

    if (value.length < 3) {
        showErrorNotification('Certification must be at least 3 characters');
        return;
    }

    const list = document.querySelector('.certifications-list');
    if (!list) {
        showErrorNotification('Certifications list not found');
        return;
    }

    const item = document.createElement('div');
    item.className = 'cert-item';
    item.innerHTML = `
        <span>${value}</span>
        <button type="button" class="cert-remove" onclick="removeCert(this)">×</button>
    `;
    list.appendChild(item);
    input.value = '';
    input.focus();
}

/**
 * Remove certification
 */
function removeCert(btn) {
    btn.parentElement.remove();
}

/**
 * Enter key support for cert input
 */
document.addEventListener('DOMContentLoaded', function() {
    const certInput = document.getElementById('certInput');
    if (certInput) {
        certInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCertification();
            }
        });
    }
});

// ============================================
// TEAM MEMBER MANAGEMENT
// ============================================

/**
 * Add team member
 */
function addTeamMember(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('addTeamForm'));
    const csrfToken = getCsrfToken();

    fetch(e.target.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessNotification(data.message || 'Team member added!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showErrorNotification(data.message || 'Error adding team member');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorNotification('An error occurred. Please try again.');
    });
}

/**
 * Remove team member
 */
function removeMember(memberId) {
    if (confirm('Are you sure you want to remove this team member?')) {
        const csrfToken = getCsrfToken();

        fetch(`/planner/team/${memberId}/remove`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessNotification(data.message || 'Team member removed!');
                setTimeout(() => location.reload(), 1000);
            } else {
                showErrorNotification(data.message || 'Error removing team member');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorNotification('An error occurred. Please try again.');
        });
    }
}

/**
 * Edit team member
 */
function editMember(memberId) {
    // Implement edit functionality
    console.log('Edit member:', memberId);
}

// ============================================
// VENDOR MANAGEMENT
// ============================================

/**
 * Remove favorite vendor
 */
function removeFavoriteVendor(vendorId) {
    if (confirm('Remove this vendor from favorites?')) {
        const csrfToken = getCsrfToken();

        fetch(`/planner/vendors/${vendorId}/remove-favorite`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessNotification(data.message || 'Vendor removed from favorites!');
                setTimeout(() => location.reload(), 1000);
            } else {
                showErrorNotification(data.message || 'Error removing vendor');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorNotification('An error occurred. Please try again.');
        });
    }
}

// ============================================
// DATA EXPORT
// ============================================

/**
 * Export analytics as PDF
 */
function exportAnalyticsPDF() {
    const csrfToken = getCsrfToken();

    fetch('/planner/analytics/export-pdf', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'analytics.pdf';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();
        showSuccessNotification('Analytics exported as PDF!');
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorNotification('Error exporting analytics');
    });
}

/**
 * Export analytics as Excel
 */
function exportAnalyticsExcel() {
    const csrfToken = getCsrfToken();

    fetch('/planner/analytics/export-excel', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'analytics.xlsx';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove();
        showSuccessNotification('Analytics exported as Excel!');
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorNotification('Error exporting analytics');
    });
}

// ============================================
// FORM VALIDATION
// ============================================

/**
 * Validate form before submission
 */
function validateForm(formElement) {
    const inputs = formElement.querySelectorAll('input, textarea, select');
    let isValid = true;

    inputs.forEach(input => {
        if (input.hasAttribute('required') && !input.value.trim()) {
            input.style.borderColor = '#ff6b6b';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });

    return isValid;
}

// ============================================
// LOCAL STORAGE FOR DRAFT SAVING
// ============================================

/**
 * Save form draft
 */
function saveDraft(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    const data = new FormData(form);
    const draft = {};

    for (let [key, value] of data.entries()) {
        draft[key] = value;
    }

    localStorage.setItem(`draft_${formId}`, JSON.stringify(draft));
    showSuccessNotification('Draft saved!');
}

/**
 * Load form draft
 */
function loadDraft(formId) {
    const saved = localStorage.getItem(`draft_${formId}`);
    if (!saved) return;

    const form = document.getElementById(formId);
    if (!form) return;

    const draft = JSON.parse(saved);

    for (let [key, value] of Object.entries(draft)) {
        const input = form.elements[key];
        if (input) {
            input.value = value;
        }
    }
}

/**
 * Clear form draft
 */
function clearDraft(formId) {
    localStorage.removeItem(`draft_${formId}`);
    showSuccessNotification('Draft cleared!');
}

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Settings system initialized');
});