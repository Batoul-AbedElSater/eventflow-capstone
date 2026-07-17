{{-- resources/views/landing.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to EVENT FLOW</title>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.0/dist/confetti.browser.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --garden-green: #475B35;
            --amnesiac-white: #F5F9E5;
            --coral-haze: #E19184;
            --calypso-berry: #C63E4E;
            --vampire-hunter: #620607;
            --cream: #EFE7DA;
        }
        html, body { width: 100%; height: 100%; margin: 0; padding: 0; overflow: hidden; }
        body {
            font-family: 'Raleway', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--amnesiac-white) 0%, var(--cream) 50%, var(--amnesiac-white) 100%);
            display: flex; flex-direction: column; height: 100vh;
        }
        .floating-bg { position: fixed; inset: 0; z-index: 0; width: 100%; height: 100%; }
        .circle { position: absolute; border-radius: 50%; opacity: 0.1; animation: float 8s ease-in-out infinite; filter: blur(40px); }
        .circle1 { width: 400px; height: 400px; background: var(--coral-haze); top: -150px; left: 5%; animation-duration: 8s; }
        .circle2 { width: 350px; height: 350px; background: var(--calypso-berry); bottom: -100px; right: 10%; animation-duration: 10s; animation-direction: reverse; }
        .circle3 { width: 300px; height: 300px; background: var(--garden-green); top: 50%; right: 5%; animation-duration: 12s; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(40px); } }
        .hero { flex: 1; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; padding: 40px; }
        .creative-shape {
            position: relative; width: 700px; height: 700px;
            background: linear-gradient(135deg, var(--cream) 0%, var(--amnesiac-white) 100%);
            border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 40px 100px rgba(198, 62, 78, 0.2), inset 0 0 50px rgba(225, 145, 132, 0.15);
            animation: morphWelcome 10s ease-in-out infinite; backdrop-filter: blur(10px); z-index: 10;
        }
        @keyframes morphWelcome {
            0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; transform: translateY(0px) rotateZ(-3deg); }
            25% { border-radius: 30% 60% 70% 40% / 40% 60% 30% 70%; transform: translateY(-30px) rotateZ(3deg); }
            50% { border-radius: 40% 60% 70% 30% / 40% 50% 30% 60%; transform: translateY(0px) rotateZ(-3deg); }
            75% { border-radius: 70% 30% 40% 60% / 30% 30% 60% 70%; transform: translateY(30px) rotateZ(3deg); }
        }
        .shape-accent { position: absolute; border-radius: 50%; opacity: 0.2; filter: blur(25px); }
        .accent-1 { width: 250px; height: 250px; background: var(--coral-haze); top: -80px; right: -80px; animation: floatAccent1 8s ease-in-out infinite; }
        .accent-2 { width: 200px; height: 200px; background: var(--calypso-berry); bottom: -60px; left: -60px; animation: floatAccent2 10s ease-in-out infinite; }
        @keyframes floatAccent1 { 0%, 100% { transform: translateY(0px) translateX(0px); } 50% { transform: translateY(-50px) translateX(40px); } }
        @keyframes floatAccent2 { 0%, 100% { transform: translateY(0px) translateX(0px); } 50% { transform: translateY(50px) translateX(-40px); } }
        .shape-content { position: relative; z-index: 2; text-align: center; padding: 60px; max-width: 600px; }
        .accent-line { width: 60px; height: 4px; background: linear-gradient(90deg, var(--coral-haze) 0%, var(--calypso-berry) 100%); margin: 0 auto 24px; border-radius: 2px; animation: expandLine 1.2s ease-out 0.3s both; }
        @keyframes expandLine { from { width: 0; } to { width: 60px; } }
        .welcome-overline { font-size: 12px; font-weight: 700; letter-spacing: 3px; color: var(--coral-haze); text-transform: uppercase; margin-bottom: 20px; animation: fadeInDown 1s ease-out 0.2s both; }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        .welcome-title { font-family: 'Playfair Display', serif; font-size: 72px; font-weight: 900; line-height: 1; margin-bottom: 20px; letter-spacing: -2px; animation: fadeInDown 1s ease-out 0.3s both; }
        .title-line1 { color: var(--garden-green); display: block; margin-bottom: 10px; }
        .title-line2 { display: block; background: linear-gradient(135deg, var(--coral-haze), var(--calypso-berry)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 10px; }
        .title-line3 { color: var(--vampire-hunter); display: block; font-weight: 400; font-size: 56px; }
        .welcome-description { font-size: 16px; font-weight: 300; color: #555; line-height: 1.8; margin-bottom: 40px; animation: fadeInDown 1s ease-out 0.4s both; }
        .welcome-description strong { font-weight: 600; color: var(--calypso-berry); }
        .btn-get-started {
            font-weight: 700; font-size: 16px; padding: 16px 50px; border-radius: 50px; border: none; cursor: pointer;
            background: linear-gradient(135deg, var(--coral-haze), var(--calypso-berry));
            color: white; box-shadow: 0 15px 40px rgba(193, 62, 78, 0.4);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: inline-flex; align-items: center; gap: 10px; text-decoration: none;
            animation: fadeInUp 1s ease-out 0.5s both;
        }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .btn-get-started:hover { transform: translateY(-6px); box-shadow: 0 25px 60px rgba(193, 62, 78, 0.5); }
        .btn-get-started svg { width: 20px; height: 20px; transition: transform 0.3s ease; }
        .btn-get-started:hover svg { transform: translateX(5px); }
        canvas { position: fixed; inset: 0; z-index: 50; pointer-events: none; }
        @media (max-width: 1024px) { .creative-shape { width: 550px; height: 550px; } .shape-content { padding: 40px; } .welcome-title { font-size: 52px; } .title-line3 { font-size: 42px; } }
        @media (max-width: 768px) { .hero { padding: 20px; } .creative-shape { width: 450px; height: 450px; } .shape-content { padding: 30px; } .welcome-title { font-size: 40px; } .title-line3 { font-size: 32px; } .btn-get-started { padding: 14px 40px; font-size: 15px; } }
        @media (max-width: 480px) { .creative-shape { width: 100%; max-width: 380px; height: auto; aspect-ratio: 1; } .shape-content { padding: 25px; } .welcome-title { font-size: 32px; } .title-line3 { font-size: 26px; } }
    </style>
</head>
<body>
    <div class="floating-bg">
        <div class="circle circle1"></div>
        <div class="circle circle2"></div>
        <div class="circle circle3"></div>
    </div>

    <section class="hero">
        <div class="creative-shape">
            <div class="shape-accent accent-1"></div>
            <div class="shape-accent accent-2"></div>
            <div class="shape-content">
                <div class="accent-line"></div>
                <p class="welcome-overline">Welcome to Event FLOW</p>
                <h1 class="welcome-title">
                    <span class="title-line1">Plan Your</span>
                    <span class="title-line2">Perfect</span>
                    <span class="title-line3">Celebration</span>
                </h1>
                <p class="welcome-description">
                    From intimate henna ceremonies to <strong>grand celebrations</strong>, we orchestrate every detail with <strong>elegance and precision</strong>. Your perfect event awaits.
                </p>
              <a href="{{ route('landing') }}" class="btn-get-started" id="getStartedBtn">
                    Get Started
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <canvas id="confetti"></canvas>

    <script>
        document.getElementById('getStartedBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 }, colors: ['#E19184', '#C63E4E', '#475B35', '#F5F9E5'] });
            setTimeout(() => { confetti({ particleCount: 60, spread: 100, origin: { x: 0.1, y: 0.5 }, colors: ['#E19184', '#C63E4E', '#475B35'] }); }, 150);
            setTimeout(() => { confetti({ particleCount: 60, spread: 100, origin: { x: 0.9, y: 0.5 }, colors: ['#E19184', '#C63E4E', '#475B35'] }); }, 300);
            setTimeout(() => { window.location.href = href; }, 1500);
        });
    </script>
</body>
</html>