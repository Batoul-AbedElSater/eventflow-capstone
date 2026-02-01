document.addEventListener('DOMContentLoaded', function() {
    
    // ====================================
    // TAB SWITCHING
    // ====================================
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked button and corresponding pane
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // ====================================
    // AUTO-SCROLL TO TAB FROM URL HASH
    // ====================================
    const hash = window.location.hash.substring(1);
    if (hash) {
        const tabButton = document.querySelector(`[data-tab="${hash}"]`);
        if (tabButton) {
            tabButton.click();
        }
    }
    
    console.log('✅ Event Details page loaded!');
});