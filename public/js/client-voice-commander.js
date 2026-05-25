document.addEventListener('DOMContentLoaded', function() {
    
    const sidebarVoiceBtn = document.getElementById('sidebarVoiceBtn');
    const voiceCommanderModal = document.getElementById('voiceCommanderModal');
    const voiceCloseBtn = document.getElementById('voiceCloseBtn');
    const voiceToggleBtn = document.getElementById('voiceToggleBtn');
    const voiceStatus = document.getElementById('voiceStatus');
    const voiceTranscript = document.getElementById('voiceTranscript');
    const voiceIconPulse = document.getElementById('voiceIconPulse');
    
    let recognition = null;
    let isListening = false;

    // Check for browser support
    if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();
        
        recognition.continuous = false;
        recognition.interimResults = true;
        recognition.lang = 'en-US';
    }

    // Open modal from sidebar
    if (sidebarVoiceBtn) {
        sidebarVoiceBtn.addEventListener('click', () => {
            voiceCommanderModal.classList.add('active');
        });
    }

    // Close modal
    if (voiceCloseBtn) {
        voiceCloseBtn.addEventListener('click', () => {
            voiceCommanderModal.classList.remove('active');
            if (isListening) stopListening();
        });
    }

    // Click outside to close
    voiceCommanderModal?.addEventListener('click', (e) => {
        if (e.target === voiceCommanderModal || e.target.classList.contains('voice-modal-overlay')) {
            voiceCommanderModal.classList.remove('active');
            if (isListening) stopListening();
        }
    });

    // Toggle listening
    voiceToggleBtn?.addEventListener('click', () => {
        if (!recognition) {
            alert('Voice recognition is not supported in your browser. Please use Chrome, Edge, or Safari.');
            return;
        }
        
        if (isListening) {
            stopListening();
        } else {
            startListening();
        }
    });

    function startListening() {
        isListening = true;
        voiceStatus.textContent = 'Listening... Speak now!';
        voiceToggleBtn.innerHTML = '<i class="fas fa-stop"></i> Stop Listening';
        voiceToggleBtn.style.background = 'linear-gradient(135deg, #D0021B, #A00116)';
        voiceIconPulse.classList.add('listening');
        voiceTranscript.innerHTML = '';
        
        recognition.start();
    }

    function stopListening() {
        isListening = false;
        voiceStatus.textContent = 'Click the button to start';
        voiceToggleBtn.innerHTML = '<i class="fas fa-microphone"></i> Start Listening';
        voiceToggleBtn.style.background = 'var(--gradient-coral)';
        voiceIconPulse.classList.remove('listening');
        
        if (recognition) {
            recognition.stop();
        }
    }

    // Speech recognition events
    if (recognition) {
        recognition.onresult = (event) => {
            let interimTranscript = '';
            let finalTranscript = '';
            
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += transcript + ' ';
                } else {
                    interimTranscript += transcript;
                }
            }
            
            voiceTranscript.innerHTML = `
                <div class="transcript-final">${finalTranscript}</div>
                <div class="transcript-interim">${interimTranscript}</div>
            `;
            
            if (finalTranscript) {
                processCommand(finalTranscript.trim().toLowerCase());
            }
        };

        recognition.onerror = (event) => {
            console.error('Speech recognition error:', event.error);
            voiceStatus.textContent = 'Error: ' + event.error;
            stopListening();
        };

        recognition.onend = () => {
            if (isListening) {
                recognition.start(); // Restart if still supposed to be listening
            }
        };
    }

    function processCommand(command) {
        console.log('Processing command:', command);
        
        voiceStatus.textContent = 'Processing: "' + command + '"';
        
        // Command patterns
        if (command.includes('show') && command.includes('event')) {
            setTimeout(() => {
                window.location.href = '/client/events';
            }, 500);
        } else if (command.includes('create') && (command.includes('event') || command.includes('new'))) {
            setTimeout(() => {
                window.location.href = '/client/events/create';
            }, 500);
        } else if (command.includes('dashboard') || command.includes('home')) {
            setTimeout(() => {
                window.location.href = '/client/dashboard';
            }, 500);
        } else if (command.includes('guest')) {
            setTimeout(() => {
                window.location.href = '/client/guests';
            }, 500);
        } else if (command.includes('profile')) {
            setTimeout(() => {
                window.location.href = '/client/profile';
            }, 500);
        } else if (command.includes('settings')) {
            setTimeout(() => {
                window.location.href = '/client/settings';
            }, 500);
        } else {
            voiceStatus.textContent = 'Command not recognized. Try again.';
        }
    }

    console.log('✅ Voice Commander loaded!');
});