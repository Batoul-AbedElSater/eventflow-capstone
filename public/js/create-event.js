document.addEventListener('DOMContentLoaded', function() {
    
    // ====================================
    // AUTO-CALCULATE SUGGESTED DATES
    // ====================================
    const startDateInput = document.getElementById('start_date');
    const guestLockInput = document.getElementById('guest_list_lock');
    const rsvpDeadlineInput = document.getElementById('rsvp_deadline');
    
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            const startDate = new Date(this.value);
            
            // Suggest guest list lock: 45 days before event
            const guestLockDate = new Date(startDate);
            guestLockDate.setDate(guestLockDate.getDate() - 45);
            if (!guestLockInput.value) {
                guestLockInput.value = guestLockDate.toISOString().split('T')[0];
            }
            
            // Suggest RSVP deadline: 30 days before event
            const rsvpDate = new Date(startDate);
            rsvpDate.setDate(rsvpDate.getDate() - 30);
            if (!rsvpDeadlineInput.value) {
                rsvpDeadlineInput.value = rsvpDate.toISOString().split('T')[0];
            }
        });
    }
    
    // ====================================
    // FORM VALIDATION
    // ====================================
    const form = document.querySelector('.event-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const startDate = new Date(startDateInput.value);
            const guestLock = new Date(guestLockInput.value);
            const rsvpDeadline = new Date(rsvpDeadlineInput.value);
            
            // Validate: guest list lock must be before RSVP deadline
            if (guestLock >= rsvpDeadline) {
                e.preventDefault();
                alert('Guest list lock date must be before RSVP deadline!');
                return false;
            }
            
            // Validate: RSVP deadline must be before event date
            if (rsvpDeadline >= startDate) {
                e.preventDefault();
                alert('RSVP deadline must be before event date!');
                return false;
            }
        });
    }
    
    console.log('✅ Create Event page loaded!');
});