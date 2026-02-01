// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    
    // ====================================
    // FILTER EVENTS
    // ====================================
    const filterSelect = document.querySelector('.filter-select');
    const eventCards = document.querySelectorAll('.event-card');
    
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const filterValue = this.value;
            
            eventCards.forEach(card => {
                if (filterValue === 'all') {
                    card.style.display = 'flex';
                } else {
                    // Check if card has the status class
                    if (card.classList.contains(filterValue)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    }
    
    // ====================================
    // ANIMATE NUMBERS (Count Up Effect)
    // ====================================
    const animateValue = (element, start, end, duration) => {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.textContent = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    };
    
    // Animate stat numbers on load
    document.querySelectorAll('.stat-content h3').forEach(element => {
        const value = parseInt(element.textContent);
        if (!isNaN(value)) {
            element.textContent = '0';
            animateValue(element, 0, value, 1000);
        }
    });
    
    // ====================================
    // SMOOTH SCROLL TO SECTIONS
    // ====================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // ====================================
    // SEARCH FUNCTIONALITY
    // ====================================
    const searchInput = document.querySelector('.search-bar input');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            eventCards.forEach(card => {
                const eventName = card.querySelector('.event-info h4').textContent.toLowerCase();
                const eventLocation = card.querySelector('.event-location').textContent.toLowerCase();
                
                if (eventName.includes(searchTerm) || eventLocation.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    console.log('✅ Client Dashboard loaded successfully!');
});