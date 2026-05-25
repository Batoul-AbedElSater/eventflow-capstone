const moodPalettes = {
    productive: {
        primary: '#475B35', secondary: '#E19184', accent: '#C63E4E',
        bgLight: '#F5F9E5', bgDark: '#2C3821',
        heroGradient: 'linear-gradient(135deg, #E19184, #C63E4E)',
        sidebarGradient: 'linear-gradient(180deg, #475B35, #2C3821)',
    },
    energized: {
        primary: '#FF6B00', secondary: '#FFC857', accent: '#FF0080',
        bgLight: '#FFF5E6', bgDark: '#CC5500',
        heroGradient: 'linear-gradient(135deg, #FF6B00, #FF0080)',
        sidebarGradient: 'linear-gradient(180deg, #FF6B00, #CC5500)',
    },
    calm: {
        primary: '#6FBADC', secondary: '#B4E7F8', accent: '#9FD8E5',
        bgLight: '#F0FAFF', bgDark: '#4A90E2',
        heroGradient: 'linear-gradient(135deg, #6FBADC, #4A90E2)',
        sidebarGradient: 'linear-gradient(180deg, #6FBADC, #4A90E2)',
    },
    creative: {
        primary: '#9B59B6', secondary: '#E91E63', accent: '#FF9800',
        bgLight: '#F9F0FF', bgDark: '#8E44AD',
        heroGradient: 'linear-gradient(135deg, #9B59B6, #E91E63)',
        sidebarGradient: 'linear-gradient(180deg, #9B59B6, #8E44AD)',
    },
    stressed: {
        primary: '#7ED321', secondary: '#B4E7F8', accent: '#EFE7DA',
        bgLight: '#F2F9EC', bgDark: '#5FA318',
        heroGradient: 'linear-gradient(135deg, #7ED321, #5FA318)',
        sidebarGradient: 'linear-gradient(180deg, #7ED321, #5FA318)',
    },
    sleepy: {
        primary: '#34495E', secondary: '#95A5A6', accent: '#ECF0F1',
        bgLight: '#F4F6F7', bgDark: '#2C3E50',
        heroGradient: 'linear-gradient(135deg, #34495E, #2C3E50)',
        sidebarGradient: 'linear-gradient(180deg, #34495E, #2C3E50)',
    }
};

let currentMood = localStorage.getItem('selectedMood') || 'productive';

function applyMood(mood) {
    const p = moodPalettes[mood];
    if (!p) return;
    const root = document.documentElement;
    root.style.setProperty('--primary-color', p.primary);
    root.style.setProperty('--secondary-color', p.secondary);
    root.style.setProperty('--accent-color', p.accent);
    root.style.setProperty('--bg-light', p.bgLight);
    root.style.setProperty('--bg-dark', p.bgDark);
    root.style.setProperty('--hero-gradient', p.heroGradient);
    root.style.setProperty('--sidebar-gradient', p.sidebarGradient);
    localStorage.setItem('selectedMood', mood);
    const moodText = document.getElementById('currentMoodText');
    if (moodText) moodText.innerText = mood.charAt(0).toUpperCase() + mood.slice(1);
}

function initMoodRing() {
    const moodBtn = document.getElementById('moodSelectorBtn');
    const moodModal = document.getElementById('moodModal');
    const moodClose = document.getElementById('moodCloseBtn');
    if (moodBtn && moodModal) {
        moodBtn.addEventListener('click', () => moodModal.classList.add('active'));
        if (moodClose) moodClose.addEventListener('click', () => moodModal.classList.remove('active'));
        moodModal.addEventListener('click', (e) => { if (e.target === moodModal) moodModal.classList.remove('active'); });
    }
    document.querySelectorAll('.mood-option').forEach(opt => {
        opt.addEventListener('click', () => {
            const mood = opt.dataset.mood;
            applyMood(mood);
            if (moodModal) moodModal.classList.remove('active');
        });
    });
    applyMood(currentMood);
}

let voiceRecognition = null;
let isListening = false;

function initVoiceCommander() {
    const voiceBtn = document.getElementById('voiceCommanderBtn');
    const voiceModal = document.getElementById('voiceCommanderModal');
    const voiceClose = document.getElementById('voiceCloseBtn');
    const voiceToggle = document.getElementById('voiceToggleBtn');
    const voiceStatus = document.getElementById('voiceStatus');
    const voiceTranscript = document.getElementById('voiceTranscript');
    if (!voiceBtn || !voiceModal) return;
    if (!('webkitSpeechRecognition' in window)) {
        voiceBtn.style.display = 'none';
        return;
    }
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    voiceRecognition = new SpeechRecognition();
    voiceRecognition.continuous = false;
    voiceRecognition.interimResults = true;
    voiceRecognition.lang = 'en-US';
    voiceBtn.addEventListener('click', () => voiceModal.classList.add('active'));
    if (voiceClose) voiceClose.addEventListener('click', () => voiceModal.classList.remove('active'));
    voiceModal.addEventListener('click', (e) => { if (e.target === voiceModal) voiceModal.classList.remove('active'); });
    voiceToggle.addEventListener('click', () => {
        if (isListening) stopListening();
        else startListening();
    });
    function startListening() {
        isListening = true;
        voiceStatus.innerText = 'Listening...';
        voiceToggle.innerHTML = '<i class="fas fa-stop"></i> Stop';
        voiceRecognition.start();
    }
    function stopListening() {
        isListening = false;
        voiceStatus.innerText = 'Click microphone to start';
        voiceToggle.innerHTML = '<i class="fas fa-microphone"></i> Start';
        voiceRecognition.stop();
    }
    voiceRecognition.onresult = (event) => {
        let final = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
            if (event.results[i].isFinal) final += event.results[i][0].transcript;
        }
        voiceTranscript.innerHTML = final;
        if (final) processCommand(final.toLowerCase());
    };
    voiceRecognition.onerror = () => stopListening();
    voiceRecognition.onend = () => { if (isListening) voiceRecognition.start(); };
}

function processCommand(command) {
    const role = window.location.pathname.includes('/planner') ? 'planner' : 'client';
    const routes = {
        dashboard: ['dashboard', 'home', 'main'],
        events: ['events', 'my events', 'event list'],
        create: ['create event', 'new event'],
        messages: ['messages', 'chat', 'inbox'],
        tasks: ['tasks', 'todo', 'to do'],
        analytics: ['analytics', 'stats'],
        profile: ['profile', 'my profile'],
        guests: ['guests', 'guest list'],
        requests: ['requests', 'event requests']
    };
    for (const [route, keywords] of Object.entries(routes)) {
        if (keywords.some(k => command.includes(k))) {
            window.location.href = `/${role}/${route === 'create' ? 'events/create' : route}`;
            return;
        }
    }
    const statusSpan = document.getElementById('voiceStatus');
    if (statusSpan) statusSpan.innerText = 'Command not recognized';
    setTimeout(() => { if (statusSpan) statusSpan.innerText = 'Click microphone to start'; }, 2000);
}

document.addEventListener('DOMContentLoaded', () => {
    initMoodRing();
    initVoiceCommander();
});