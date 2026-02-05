document.addEventListener('DOMContentLoaded', function() {
    
    // ====================================
    // TAB SWITCHING
    // ====================================
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Auto-scroll to tab from URL hash
    const hash = window.location.hash.substring(1);
    if (hash) {
        const tabButton = document.querySelector(`[data-tab="${hash}"]`);
        if (tabButton) tabButton.click();
    }
    
    // ====================================
    // GUEST MANAGEMENT
    // ====================================
    const guestModal = document.getElementById('guest-modal');
    const deleteModal = document.getElementById('delete-modal');
    const guestForm = document.getElementById('guest-form');
    
    let currentGuestId = null;
    let deleteGuestId = null;
    
    // Open Add Guest Modal
    const addGuestBtn = document.getElementById('add-guest-btn');
    const addFirstGuestBtn = document.getElementById('add-first-guest-btn');
    
    if (addGuestBtn) {
        addGuestBtn.addEventListener('click', openAddGuestModal);
    }
    
    if (addFirstGuestBtn) {
        addFirstGuestBtn.addEventListener('click', openAddGuestModal);
    }
    
    function openAddGuestModal() {
        document.getElementById('modal-title').textContent = 'Add Guest';
        document.getElementById('form-method').value = 'POST';
        currentGuestId = null;
        guestForm.reset();
        guestModal.classList.add('active');
    }
    
    // Open Edit Guest Modal
    document.querySelectorAll('.edit-guest').forEach(btn => {
        btn.addEventListener('click', function() {
            currentGuestId = this.dataset.guestId;
            document.getElementById('modal-title').textContent = 'Edit Guest';
            document.getElementById('form-method').value = 'PUT';
            
            // Fill form with guest data
            document.getElementById('guest-id').value = currentGuestId;
            document.getElementById('guest-name').value = this.dataset.guestName;
            document.getElementById('guest-email').value = this.dataset.guestEmail;
            document.getElementById('guest-phone').value = this.dataset.guestPhone || '';
            document.getElementById('guest-dietary').value = this.dataset.guestDietary || '';
            document.getElementById('guest-plus-one').checked = this.dataset.guestPlusOne === 'true';
            document.getElementById('guest-plus-one-name').value = this.dataset.guestPlusOneName || '';
            document.getElementById('guest-notes').value = this.dataset.guestNotes || '';
            
            // Show/hide plus one name field
            const plusOneGroup = document.getElementById('plus-one-name-group');
            plusOneGroup.style.display = this.dataset.guestPlusOne === 'true' ? 'block' : 'none';
            
            guestModal.classList.add('active');
        });
    });
    
    // Close Modals
    document.getElementById('close-modal').addEventListener('click', () => {
        guestModal.classList.remove('active');
    });
    
    document.getElementById('cancel-btn').addEventListener('click', () => {
        guestModal.classList.remove('active');
    });
    
    document.getElementById('close-delete-modal').addEventListener('click', () => {
        deleteModal.classList.remove('active');
    });
    
    document.getElementById('cancel-delete-btn').addEventListener('click', () => {
        deleteModal.classList.remove('active');
    });
    
    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function() {
            this.parentElement.classList.remove('active');
        });
    });
    
    // Plus One Checkbox Toggle
    document.getElementById('guest-plus-one').addEventListener('change', function() {
        const plusOneGroup = document.getElementById('plus-one-name-group');
        plusOneGroup.style.display = this.checked ? 'block' : 'none';
    });
    
    // Submit Guest Form (Add or Edit)
    guestForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const method = document.getElementById('form-method').value;
        const formData = {
            name: document.getElementById('guest-name').value,
            email: document.getElementById('guest-email').value,
            phone: document.getElementById('guest-phone').value || null,
            dietary_restrictions: document.getElementById('guest-dietary').value || null,
            plus_one_allowed: document.getElementById('guest-plus-one').checked,
            plus_one_name: document.getElementById('guest-plus-one-name').value || null,
            notes: document.getElementById('guest-notes').value || null,
        };
        
        let url = `/client/events/${EVENT_ID}/guests`;
        if (method === 'PUT') {
            url += `/${currentGuestId}`;
        }
        
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success message
                showToast(data.message, 'success');
                
                // Close modal
                guestModal.classList.remove('active');
                
                // Reload page to show updated guest list
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.message || 'An error occurred', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to save guest', 'error');
        }
    });
    
    // Delete Guest
    document.querySelectorAll('.delete-guest').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteGuestId = this.dataset.guestId;
            document.getElementById('delete-guest-name').textContent = this.dataset.guestName;
            deleteModal.classList.add('active');
        });
    });
    
    document.getElementById('confirm-delete-btn').addEventListener('click', async function() {
        if (!deleteGuestId) return;
        
        try {
            const response = await fetch(`/client/events/${EVENT_ID}/guests/${deleteGuestId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                deleteModal.classList.remove('active');
                
                // Reload page
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.message || 'Failed to delete guest', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to delete guest', 'error');
        }
    });
    
    // Search Guests
    const searchInput = document.getElementById('search-guests');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.guest-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    // Filter by RSVP Status
    const filterSelect = document.getElementById('filter-rsvp');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const filterValue = this.value;
            const rows = document.querySelectorAll('.guest-table tbody tr');
            
            rows.forEach(row => {
                if (filterValue === 'all') {
                    row.style.display = '';
                } else {
                    const rsvpStatus = row.dataset.rsvp;
                    row.style.display = rsvpStatus === filterValue ? '' : 'none';
                }
            });
        });
    }
    
    // Toast Notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }


    // ====================================
    // SEND INVITATIONS
    // ====================================
    const invitationModal = document.getElementById('invitation-modal');
    const sendInvitationsBtn = document.getElementById('send-invitations-btn');
    
    if (sendInvitationsBtn) {
        sendInvitationsBtn.addEventListener('click', function() {
            invitationModal.classList.add('active');
        });
    }
    
    document.getElementById('close-invitation-modal').addEventListener('click', () => {
        invitationModal.classList.remove('active');
    });
    
    document.getElementById('cancel-invitation-btn').addEventListener('click', () => {
        invitationModal.classList.remove('active');
    });
    
    document.getElementById('confirm-send-invitations-btn').addEventListener('click', async function() {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        
        try {
            const response = await fetch(`/client/events/${EVENT_ID}/invitations/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                },
                body: JSON.stringify({
                    send_to_all: true
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                invitationModal.classList.remove('active');
                
                // Reload page after 2 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showToast(data.message || 'Failed to send invitations', 'error');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-paper-plane"></i> Send to All Pending';
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to send invitations', 'error');
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-paper-plane"></i> Send to All Pending';
        }
    });
    
    console.log('✅ Event Details with Guest Management loaded!');
});

