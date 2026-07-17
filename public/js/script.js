(function() {
    const revealEls = document.querySelectorAll('.reveal');
    const revealObs = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    revealEls.forEach((el) => revealObs.observe(el));

    const statsRows = document.querySelectorAll('.stats-row');

    function formatStatNumber(value, format, suffix) {
        const rounded = Math.round(value);
        if (format === 'compact') {
            const compact = rounded / 1000;
            const label = compact >= 10
                ? String(Math.round(compact))
                : String(Math.round(compact * 10) / 10);
            return `${label}K${suffix}`;
        }
        return `${rounded.toLocaleString()}${suffix}`;
    }

    function animateStatNumber(el) {
        const target = Number(el.dataset.countTarget || 0);
        const suffix = el.dataset.countSuffix || '';
        const format = el.dataset.countFormat || '';
        const duration = 1350;
        const startTime = performance.now();

        function tick(now) {
            const progress = Math.min((now - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = formatStatNumber(target * eased, format, suffix);
            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.textContent = formatStatNumber(target, format, suffix);
            }
        }

        requestAnimationFrame(tick);
    }

    const statsObs = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.querySelectorAll('[data-count-target]').forEach(animateStatNumber);
            statsObs.unobserve(entry.target);
        });
    }, { threshold: 0.35 });

    statsRows.forEach((row) => statsObs.observe(row));

    const scene = document.getElementById('carouselScene');
    const stage = scene ? scene.querySelector('.orbit-stage') : null;
    const cards = scene ? Array.from(scene.querySelectorAll('.spin-card')) : [];
    const dotsContainer = document.getElementById('carouselDots');
    const N = cards.length;

    if (!scene || !stage || N === 0 || !dotsContainer) return;

    dotsContainer.innerHTML = '';
    const dots = cards.map((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'carousel-dot';
        dot.setAttribute('aria-label', `Go to card ${i + 1}`);
        dot.addEventListener('click', () => jumpToCard(i));
        dotsContainer.appendChild(dot);
        return dot;
    });

    const maxScrollStep = Math.max(N - 1, 1);
    let scrollProgress = 0;
    let targetProgress = 0;
    let activeIndex = -1;
    let rafId = null;
    let lastScrollY = window.scrollY;

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function getOrbitSettings() {
        const w = window.innerWidth;
        if (w <= 768) return { radiusX: 170, radiusZ: 210, verticalStep: 64, tilt: 30, focusX: 0 };
        if (w <= 1024) return { radiusX: 300, radiusZ: 370, verticalStep: 84, tilt: 34, focusX: 20 };
        return { radiusX: 470, radiusZ: 560, verticalStep: 106, tilt: 38, focusX: 0 };
    }

    function getStageOffset() {
        if (window.innerWidth <= 768) return 68;
        if (window.innerWidth <= 1024) return 70;
        return 74;
    }

    function updateStagePosition() {
        const rect = scene.getBoundingClientRect();
        const maxY = Math.max(0, scene.offsetHeight - stage.offsetHeight);
        const y = clamp(-rect.top + getStageOffset(), 0, maxY);
        stage.style.transform = `translate3d(0, ${y.toFixed(2)}px, 0)`;
    }

    function updateCards() {
        updateStagePosition();

        const settings = getOrbitSettings();
        const angleStep = (Math.PI * 2) / N;
        const scrollPosition = scrollProgress * maxScrollStep;
        const currentIndex = clamp(Math.round(scrollPosition), 0, N - 1);

        cards.forEach((card, i) => {
            const diff = i - scrollPosition;
            const angle = diff * angleStep;
            const x = settings.focusX + Math.sin(angle) * settings.radiusX;
            const z = Math.cos(angle) * settings.radiusZ;
            const y = diff * settings.verticalStep;
            const closeness = clamp(1 - Math.abs(diff) * 0.42, 0, 1);
            const sidePresence = clamp(1 - Math.abs(diff) * 0.18, 0, 1);
            const scale = 0.7 + closeness * 0.22;
            const rotateY = -Math.sin(angle) * settings.tilt;
            const rotateZ = diff * -0.7;
            const isActive = i === currentIndex && Math.abs(diff) < 0.58;
            const isBehind = z < -settings.radiusZ * 0.16;
            const opacity = isActive ? 1 : (isBehind ? 0.16 : 0.26 + sidePresence * 0.42);

            card.style.transform = `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, ${z.toFixed(2)}px) rotateY(${rotateY.toFixed(2)}deg) rotateZ(${rotateZ.toFixed(2)}deg) scale(${scale.toFixed(3)})`;
            card.style.opacity = String(clamp(opacity, 0, 1).toFixed(3));
            card.style.zIndex = String(Math.round(1000 + z));
            card.classList.toggle('active', isActive);
            card.classList.toggle('behind', isBehind);
        });

        if (currentIndex !== activeIndex) {
            activeIndex = currentIndex;
            dots.forEach((dot, j) => dot.classList.toggle('active-dot', j === currentIndex));
        }
    }

    function animate() {
        scrollProgress += (targetProgress - scrollProgress) * 0.09;
        updateCards();

        if (Math.abs(targetProgress - scrollProgress) > 0.00035) {
            rafId = requestAnimationFrame(animate);
        } else {
            scrollProgress = targetProgress;
            updateCards();
            rafId = null;
        }
    }

    function onScroll() {
        lastScrollY = window.scrollY;
        const rect = scene.getBoundingClientRect();
        const vh = window.innerHeight;
        const travel = Math.max(1, rect.height - vh * 0.75);
        const entered = -rect.top + vh * 0.18;
        targetProgress = clamp(entered / travel, 0, 1);
        if (!rafId) rafId = requestAnimationFrame(animate);
    }

    function jumpToCard(index) {
        targetProgress = index / maxScrollStep;
        const rect = scene.getBoundingClientRect();
        const sceneTop = lastScrollY + rect.top;
        const vh = window.innerHeight;
        const travel = Math.max(1, rect.height - vh * 0.75);
        const targetY = sceneTop - vh * 0.18 + travel * targetProgress;
        window.scrollTo({ top: targetY, behavior: 'smooth' });
        if (!rafId) rafId = requestAnimationFrame(animate);
    }

    updateCards();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', updateCards, { passive: true });
    onScroll();
})();


// ── Navbar scroll effect
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 40);
});

// ── Mobile hamburger toggle
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobile-menu');
const h1 = document.getElementById('h1');
const h2 = document.getElementById('h2');
const h3 = document.getElementById('h3');
let menuOpen = false;

hamburger.addEventListener('click', () => {
  menuOpen = !menuOpen;
  mobileMenu.classList.toggle('open', menuOpen);
  h1.style.transform = menuOpen ? 'rotate(45deg) translate(0, 4.5px)' : '';
  h2.style.opacity   = menuOpen ? '0' : '1';
  h3.style.transform = menuOpen ? 'rotate(-45deg) translate(0, -4.5px)' : '';
});

// ── Fade-up entrance animations (IntersectionObserver)
const fadeEls = document.querySelectorAll('.fade-up');
const observer = new IntersectionObserver(
  (entries) => entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); }),
  { threshold: 0.15 }
);
fadeEls.forEach(el => observer.observe(el));

