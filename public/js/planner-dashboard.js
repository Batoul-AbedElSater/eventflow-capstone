// Planner Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Task checkbox toggle
    document.querySelectorAll('.task-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const taskItem = this.closest('.task-item');
            if (this.checked) {
                taskItem.style.opacity = '0.5';
                taskItem.style.textDecoration = 'line-through';
            } else {
                taskItem.style.opacity = '1';
                taskItem.style.textDecoration = 'none';
            }
        });
    });

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert-success, .alert-danger').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // ============================================
    // VOICE COMMANDER
    // ============================================
    const voiceBtn = document.getElementById('voiceCommanderBtn');
    const voiceModal = document.getElementById('voiceModal');
    const voiceCloseBtn = document.getElementById('voiceCloseBtn');
    const voiceActionBtn = document.getElementById('voiceActionBtn');
    const voiceAnimation = document.querySelector('.voice-animation');
    const voiceStatusText = document.getElementById('voiceStatusText');
    const voiceSubtext = document.getElementById('voiceSubtext');
    const transcriptText = document.getElementById('transcriptText');
    const responseText = document.getElementById('responseText');

    let recognition = null;
    let isListening = false;

    // Check if browser supports speech recognition
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';
    }

    // Open modal
    if (voiceBtn) {
        voiceBtn.addEventListener('click', () => {
            voiceModal.classList.add('active');
        });
    }

    // Close modal
    if (voiceCloseBtn) {
        voiceCloseBtn.addEventListener('click', () => {
            voiceModal.classList.remove('active');
            stopListening();
        });
    }

    // Start/Stop listening
    if (voiceActionBtn) {
        voiceActionBtn.addEventListener('click', () => {
            if (isListening) {
                stopListening();
            } else {
                startListening();
            }
        });
    }

    function startListening() {
        if (!recognition) {
            responseText.textContent = 'Sorry, your browser doesn\'t support voice recognition. Try Chrome or Edge.';
            return;
        }

        isListening = true;
        voiceBtn.classList.add('listening');
        voiceActionBtn.classList.add('listening');
        voiceActionBtn.innerHTML = '<i class="fas fa-stop"></i><span>Stop Listening</span>';
        voiceAnimation.classList.add('listening');
        voiceStatusText.textContent = 'Listening...';
        voiceSubtext.textContent = 'Speak your command now';
        transcriptText.textContent = '—';
        responseText.textContent = '—';

        recognition.start();
    }

    function stopListening() {
        if (recognition && isListening) {
            recognition.stop();
        }
        isListening = false;
        voiceBtn.classList.remove('listening');
        voiceActionBtn.classList.remove('listening');
        voiceActionBtn.innerHTML = '<i class="fas fa-microphone"></i><span>Start Listening</span>';
        voiceAnimation.classList.remove('listening');
        voiceStatusText.textContent = 'Click to start listening...';
        voiceSubtext.textContent = 'Try: "Show today\'s tasks" or "What\'s my schedule?"';
    }

    // Handle speech recognition results
    if (recognition) {
        recognition.onresult = (event) => {
            const command = event.results[0][0].transcript.toLowerCase();
            transcriptText.textContent = command;
            processVoiceCommand(command);
            stopListening();
        };

        recognition.onerror = (event) => {
            console.error('Speech recognition error:', event.error);
            responseText.textContent = 'Oops! I couldn\'t hear you clearly. Please try again.';
            stopListening();
        };
    }

    // Process voice commands
    function processVoiceCommand(command) {
        let response = '';
        let action = null;

        // Command matching
        if (command.includes('task') || command.includes('to do')) {
            response = 'Here are your tasks for today. Redirecting to tasks page...';
            action = () => window.location.href = '/planner/tasks';
        }
        else if (command.includes('schedule') || command.includes('calendar')) {
            response = 'Showing your calendar. Scroll to the calendar section!';
            action = () => {
                voiceModal.classList.remove('active');
                document.querySelector('.calendar-section')?.scrollIntoView({ behavior: 'smooth' });
            };
        }
        else if (command.includes('event') && command.includes('how many')) {
            const count = document.querySelectorAll('.calendar-event').length || 0;
            response = `You have ${count} events this week!`;
        }
        else if (command.includes('request') || command.includes('pending')) {
            response = 'Showing pending event requests. Scroll down!';
            action = () => {
                voiceModal.classList.remove('active');
                document.querySelector('.requests-grid')?.scrollIntoView({ behavior: 'smooth' });
            };
        }
        else if (command.includes('analytic')) {
            response = 'Taking you to analytics page...';
            action = () => window.location.href = '/planner/analytics';
        }
        else if (command.includes('dashboard') || command.includes('home')) {
            response = 'Going to dashboard home...';
            action = () => window.location.href = '/planner/dashboard';
        }
        else {
            response = `I heard "${command}" but I'm not sure what to do. Try commands like "Show today's tasks" or "What's my schedule?"`;
        }

        responseText.textContent = response;

        // Execute action after 2 seconds
        if (action) {
            setTimeout(action, 2000);
        }
    }

    // Command chips click
    document.querySelectorAll('.command-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const command = this.textContent.replace(/"/g, '').toLowerCase();
            transcriptText.textContent = command;
            processVoiceCommand(command);
        });
    });

});