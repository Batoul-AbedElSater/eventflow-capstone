<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Plano-eve - Event Planning</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
</head>
<body>

  <nav>
    <div class="nav-container" style="padding: 10px 0;">
        <h1 class="nav-logo">Plano-eve</h1>
        <div class="nav-buttons">
            <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">Log In</a>
            <a href="{{ route('register') }}" class="nav-btn nav-btn-primary">Sign Up</a>
        </div>
    </div>
</nav>

<div class="page-wrapper" style="padding-top: 0;">

    <section class="hero" style="padding-top: 10px;">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
            <div class="hero-wrapper">
                <div class="hero-image-side">
                    <div class="image-container">
                        {{-- Images are in public/images/ --}}
                        <img src="{{ asset('images/p.jpeg') }}" alt="Event Planning" />
                        <div class="accent-circle accent-1"></div>
                        <div class="accent-circle accent-2"></div>
                    </div>
                </div>
                <div class="hero-text-side">
                    <div class="accent-line"></div>
                    <p class="hero-overline">Transform Your Vision</p>
                    <h1 class="hero-title">
                        <span class="title-line-1">Plan Your</span>
                        <span class="title-line-2">Perfect Event</span>
                        <span class="title-line-3">Effortlessly</span>
                    </h1>
                    <p class="hero-description">
                        Every celebration deserves <strong>elegance and precision</strong>. From intimate henna ceremonies to grand celebrations, we orchestrate every detail with <strong>luxury and care</strong>. Your perfect event is just moments away.
                    </p>
                    <div class="cta-buttons">
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            Create Event
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="community-section">
            <div class="community-inner">
                <div class="comm-header reveal">
                    <span class="comm-overline">Our Community</span>
                    <h2 class="comm-title">
                        Your event, planned <span>beautifully</span><br />in half the time
                    </h2>
                    <p class="comm-subtitle">
                        We've built a community of seasoned planners, curated vendors, and creative minds — all working together so you never have to do it alone.
                    </p>
                </div>

                <div class="carousel-scene" id="carouselScene">
                    <div class="orbit-stage">
                        <div class="magic-axis" aria-hidden="true">
                            <div class="axis-core"></div>
                            <span class="spark"></span><span class="spark"></span><span class="spark"></span><span class="spark"></span>
                            <span class="spark"></span><span class="spark"></span><span class="spark"></span><span class="spark"></span>
                            <span class="spark"></span><span class="spark"></span><span class="spark"></span><span class="spark"></span>
                            <span class="spark"></span><span class="spark"></span><span class="spark"></span><span class="spark"></span>
                        </div>
                        <div class="spin-card" data-card-index="0">
                            <div class="card-icon icon-coral">&#9201;</div>
                            <p class="card-num">01 - Speed</p>
                            <h3 class="card-title">Plan in Hours, Not Weeks</h3>
                            <p class="card-text">Our intelligent workflow cuts planning time by up to 70%. Answer a few questions and we map out your entire event structure instantly.</p>
                            <span class="card-pill">Save 40+ hours</span>
                        </div>
                        <div class="spin-card" data-card-index="1">
                            <div class="card-icon icon-berry">&#10003;</div>
                            <p class="card-num">02 - Precision</p>
                            <h3 class="card-title">Every Detail, Accounted For</h3>
                            <p class="card-text">From guest lists to timelines, our smart checklists adapt to your event - weddings, galas, birthdays, and more.</p>
                            <span class="card-pill">Zero missed details</span>
                        </div>
                        <div class="spin-card" data-card-index="2">
                            <div class="card-icon icon-coral">&#128176;</div>
                            <p class="card-num">03 - Budget</p>
                            <h3 class="card-title">Stay on Budget, Always</h3>
                            <p class="card-text">Real-time budget tracking with intelligent suggestions. Spend wisely without sacrificing elegance.</p>
                            <span class="card-pill">Smart spending</span>
                        </div>
                        <div class="spin-card" data-card-index="3">
                            <div class="card-icon icon-green">&#128172;</div>
                            <p class="card-num">04 - Support</p>
                            <h3 class="card-title">Expert Help, On Demand</h3>
                            <p class="card-text">Chat live with experienced planners. Get answers in minutes, because your timeline never waits.</p>
                            <span class="card-pill">24/7 guidance</span>
                        </div>
                        <div class="spin-card" data-card-index="4">
                            <div class="card-icon icon-berry">&#10024;</div>
                            <p class="card-num">05 - Magic</p>
                            <h3 class="card-title">Moments That Last Forever</h3>
                            <p class="card-text">Mood boards, decor inspiration, and styling curation tailored to your unique vision and taste.</p>
                            <span class="card-pill">Unforgettable</span>
                        </div>
                    </div>
                </div>
                <div class="carousel-dots" id="carouselDots" aria-label="Carousel navigation"></div>

                <div class="memory-transition reveal">
                    <span class="memory-caption">Your moments, revealed</span>
                    <span class="memory-thread"></span>
                    <span class="memory-spark"></span>
                    <span class="memory-spark"></span>
                    <span class="memory-spark"></span>
                    <span class="memory-spark"></span>
                    <span class="memory-spark"></span>
                </div>

                <div class="photo-grid memory-reveal reveal">
                    {{-- All images are in public/images/ --}}
                    <div class="photo-item tall">
                        <img src="{{ asset('images/wedding.jpg') }}" alt="Wedding reception" />
                        <div class="photo-overlay"></div>
                        <span class="photo-label">Weddings</span>
                    </div>
                    <div class="photo-item short">
                        <img src="{{ asset('images/gra.jpg') }}" alt="Graduation celebration" />
                        <div class="photo-overlay"></div>
                        <span class="photo-label">Graduations</span>
                    </div>
                    <div class="photo-item short">
                        <img src="{{ asset('images/birthday.jpeg') }}" alt="Birthday celebration" />
                        <div class="photo-overlay"></div>
                        <span class="photo-label">Birthdays</span>
                    </div>
                    <div class="photo-item short">
                        <img src="{{ asset('images/image.png') }}" alt="Corporate dinner table" />
                        <div class="photo-overlay"></div>
                        <span class="photo-label">Corporate</span>
                    </div>
                    <div class="photo-item short">
                        <img src="{{ asset('images/gender.jpeg') }}" alt="Gender reveal decor" />
                        <div class="photo-overlay"></div>
                        <span class="photo-label">Gender Reveals</span>
                    </div>
                    <div class="photo-item short">
                        <img src="{{ asset('images/flowers.jpg') }}" alt="Anniversary garden event" />
                        <div class="photo-overlay"></div>
                        <span class="photo-label">Anniversaries</span>
                    </div>
                </div>

                <div class="stats-row reveal">
                    <div class="stat-block"><div class="stat-num" data-count-target="8400" data-count-suffix="+">8,400+</div><div class="stat-label">Events Planned</div></div>
                    <div class="stat-block"><div class="stat-num" data-count-target="12000" data-count-format="compact">12K</div><div class="stat-label">Verified Vendors</div></div>
                    <div class="stat-block"><div class="stat-num" data-count-target="98" data-count-suffix="%">98%</div><div class="stat-label">Satisfaction Rate</div></div>
                    <div class="stat-block"><div class="stat-num" data-count-target="70" data-count-suffix="%">70%</div><div class="stat-label">Less Time Spent</div></div>
                </div>

                <div class="comm-cta-strip reveal">
                    <div class="cta-strip-text">
                        <h3>Your next celebration starts here.</h3>
                        <p>Join thousands of hosts who planned their perfect day with our community.</p>
                    </div>
                    <div class="cta-strip-buttons">
                        <a href="{{ route('register') }}" class="btn-cta btn-cta-fill">
                            Start Planning
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>