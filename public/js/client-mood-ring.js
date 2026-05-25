document.addEventListener('DOMContentLoaded', function() {
    
    const moodRing = document.getElementById('moodRing');
    const moodMessage = document.getElementById('moodMessage');
    
    if (!moodRing || !moodMessage) return;

    const moods = [
        {
            color: 'linear-gradient(135deg, #7ED321, #5FA318)',
            message: 'Your events are looking fantastic!',
            icon: 'fa-smile-beam'
        },
        {
            color: 'linear-gradient(135deg, #4A90E2, #357ABD)',
            message: 'Everything is on track!',
            icon: 'fa-smile'
        },
        {
            color: 'linear-gradient(135deg, #E19184, #C63E4E)',
            message: 'Some events need attention',
            icon: 'fa-meh'
        },
        {
            color: 'linear-gradient(135deg, #F5A623, #E68619)',
            message: 'Time to plan your next event!',
            icon: 'fa-grin-stars'
        },
        {
            color: 'linear-gradient(135deg, #C63E4E, #620607)',
            message: 'Check your pending events',
            icon: 'fa-frown'
        }
    ];

    // Calculate mood based on event status
    function calculateMood() {
        // This would normally check event statuses from the page
        // For now, we'll use a random mood as demo
        const randomIndex = Math.floor(Math.random() * moods.length);
        return moods[randomIndex];
    }

    function updateMood() {
        const mood = calculateMood();
        
        moodRing.style.background = mood.color;
        moodMessage.textContent = mood.message;
        
        const icon = moodRing.querySelector('.mood-icon i');
        icon.className = `fas ${mood.icon}`;
        
        // Add pulse animation
        moodRing.style.animation = 'none';
        setTimeout(() => {
            moodRing.style.animation = 'moodPulse 3s ease-in-out infinite';
        }, 10);
    }

    // Update mood on load
    updateMood();
    
    // Update mood every 10 seconds
    setInterval(updateMood, 10000);

    console.log('✅ Mood Ring loaded!');
});