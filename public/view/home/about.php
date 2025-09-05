<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - LuzViMinDrop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --light-bg: #f8f9fa;
            --gold: #f39c12;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--primary-color);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            max-height: 40px;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: #ffd700 !important;
            transform: translateY(-2px);
        }

        .hero-about {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 120px 0 80px;
            text-align: center;
        }

        .hero-about h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-about p {
            font-size: 1.3rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .story-section {
            padding: 80px 0;
            background: white;
        }

        .mission-vision {
            padding: 80px 0;
            background: var(--light-bg);
        }

        .mission-card, .vision-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: 100%;
            transition: all 0.3s ease;
        }

        .mission-card:hover, .vision-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .mission-card {
            border-left: 5px solid var(--secondary-color);
        }

        .vision-card {
            border-left: 5px solid var(--accent-color);
        }

        .values-section {
            padding: 80px 0;
            background: white;
        }

        .value-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 15px;
            background: var(--light-bg);
            transition: all 0.3s ease;
            height: 100%;
        }

        .value-card:hover {
            background: white;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }

        .value-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .value-card:nth-child(1) .value-icon { color: var(--secondary-color); }
        .value-card:nth-child(2) .value-icon { color: var(--accent-color); }
        .value-card:nth-child(3) .value-icon { color: var(--success-color); }
        .value-card:nth-child(4) .value-icon { color: var(--gold); }

        .team-section {
            padding: 80px 0;
            background: var(--light-bg);
        }

        .team-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .team-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            color: white;
        }

        .timeline-section {
            padding: 80px 0;
            background: white;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--accent-color);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
            padding-left: 50px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 10px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--accent-color);
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--accent-color);
        }

        .timeline-year {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--accent-color);
        }

        .cta-about {
            background: linear-gradient(135deg, var(--secondary-color), #c0392b);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .btn-primary-custom {
            background: white;
            color: var(--secondary-color);
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background: var(--light-bg);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .footer {
            background: #1a252f;
            color: white;
            padding: 40px 0 20px;
        }

        .footer a {
            color: #bdc3c7;
            text-decoration: none;
        }

        .footer a:hover {
            color: white;
        }

        @media (max-width: 768px) {
            .hero-about h1 {
                font-size: 2.5rem;
            }
            
            .hero-about p {
                font-size: 1.1rem;
            }
            
            .timeline {
                padding-left: 20px;
            }
            
            .timeline-item {
                padding-left: 40px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <img src="assets/img/logo.png" alt="LuzViMinDrop" class="img-fluid">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                         <li class="nav-item">
                        <a class="nav-link" href="home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="home#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about">About</a>
                    </li>
               
                    <li class="nav-item ms-3">
                        <a class="btn btn-outline-light" href="login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container">
            <h1>About LuzViMinDrop</h1>
            <p>Revolutionizing e-commerce across the Philippines with AI-powered dropshipping solutions that empower every Filipino entrepreneur.</p>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="story-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-4 fw-bold mb-4">Our Story</h2>
                    <p class="lead">Born from the vision of democratizing e-commerce across the Philippine archipelago, LuzViMinDrop emerged in 2024 as a response to the challenges faced by Filipino entrepreneurs in the digital marketplace.</p>
                    <p>We recognized that while e-commerce was booming globally, many Filipinos struggled with complex logistics, limited supplier networks, and the technical barriers of starting an online business. Our founders, a team of Filipino tech entrepreneurs and e-commerce veterans, set out to build a platform that would level the playing field.</p>
                    <p>Today, LuzViMinDrop stands as the Philippines' premier AI-powered dropshipping platform, connecting sellers from Batanes to Tawi-Tawi with customers worldwide, all while maintaining our commitment to Filipino values of <em>bayanihan</em> and community support.</p>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Philippines Business" class="img-fluid rounded-3 shadow-lg">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary opacity-10 rounded-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="mission-vision">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="mission-card">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-bullseye fa-3x text-danger me-3"></i>
                            <h3 class="mb-0">Our Mission</h3>
                        </div>
                        <p class="lead">To empower every Filipino entrepreneur with AI-driven tools and seamless logistics, making e-commerce accessible from Luzon to Mindanao.</p>
                        <p>We believe that geography should not limit opportunity. Through our platform, we're building bridges between suppliers and sellers, between islands and markets, between dreams and reality. Our mission is to ensure that whether you're in bustling Metro Manila or remote Sulu, you have the same access to world-class e-commerce tools and opportunities.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="vision-card">
                        <div class="d-flex align-items-center mb-4">
                            <i class="fas fa-eye fa-3x text-primary me-3"></i>
                            <h3 class="mb-0">Our Vision</h3>
                        </div>
                        <p class="lead">To be the leading force in Philippine e-commerce, creating a thriving ecosystem where every Filipino can build a successful online business.</p>
                        <p>We envision a Philippines where entrepreneurship knows no boundaries, where technology serves as the great equalizer, and where our rich cultural diversity becomes a strength in the global marketplace. By 2030, we aim to have enabled over 1 million Filipino entrepreneurs to build sustainable online businesses.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="values-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-4 fw-bold mb-3">Our Core Values</h2>
                    <p class="lead text-muted">The principles that guide everything we do</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <h4 class="mb-3">Bayanihan</h4>
                        <p>We believe in the Filipino spirit of community cooperation. Success is shared, and we lift each other up through collaboration and mutual support.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4 class="mb-3">Innovation</h4>
                        <p>We continuously push the boundaries of what's possible in e-commerce, leveraging cutting-edge AI and technology to solve real problems.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4 class="mb-3">Integrity</h4>
                        <p>Trust is the foundation of commerce. We operate with complete transparency, honesty, and ethical business practices in everything we do.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="mb-3">Excellence</h4>
                        <p>We strive for excellence in every product feature, customer interaction, and business outcome. Good enough is never enough for our community.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Team -->
    <!-- <section class="team-section">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-4 fw-bold mb-3">Meet Our Leadership</h2>
                    <p class="lead text-muted">The visionaries driving LuzViMinDrop forward</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="fw-bold">Maria Santos-Cruz</h5>
                        <p class="text-muted mb-3">Chief Executive Officer</p>
                        <p>Former McKinsey consultant with 15 years in tech startups across Southeast Asia. Maria's vision for inclusive e-commerce drives our mission to democratize online business in the Philippines.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="#" class="text-primary"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-primary"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="fw-bold">Carlos Dela Rosa</h5>
                        <p class="text-muted mb-3">Chief Technology Officer</p>
                        <p>MIT-trained AI engineer and former Google software architect. Carlos leads our technical innovation, ensuring our platform stays at the forefront of e-commerce technology.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="#" class="text-primary"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-primary"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="fw-bold">Ana Reyes-Lim</h5>
                        <p class="text-muted mb-3">Chief Operations Officer</p>
                        <p>20+ years in logistics and supply chain management across the Philippines. Ana ensures our nationwide operations run seamlessly from Luzon to Mindanao.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="#" class="text-primary"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-primary"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Company Timeline -->
    <!-- <section class="timeline-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="display-4 fw-bold mb-4">Our Journey</h2>
                    <p class="lead mb-5">From startup to the Philippines' leading dropshipping platform</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-year">2024</div>
                            <h5>Company Founded</h5>
                            <p>LuzViMinDrop was established with a mission to democratize e-commerce across the Philippine archipelago. Initial seed funding of $2M secured from local and international investors.</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-year">Q2 2024</div>
                            <h5>Platform Beta Launch</h5>
                            <p>Launched beta version with 100 selected sellers across Metro Manila, Cebu, and Davao. Initial product catalog of 10,000+ items from verified suppliers.</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-year">Q3 2024</div>
                            <h5>AI Integration</h5>
                            <p>Introduced advanced AI algorithms for product recommendations, pricing optimization, and demand forecasting. Seller success rates improved by 40%.</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-year">Q4 2024</div>
                            <h5>Nationwide Expansion</h5>
                            <p>Expanded operations to all major cities across Luzon, Visayas, and Mindanao. Reached 10,000+ active sellers and 100,000+ products.</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-year">Q1 2025</div>
                            <h5>Series A Funding</h5>
                            <p>Raised $15M in Series A funding led by prominent Southeast Asian VCs. Achieved 50,000+ active sellers milestone.</p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-year">Present</div>
                            <h5>Market Leadership</h5>
                            <p>Now the #1 dropshipping platform in the Philippines with 50,000+ sellers, 1M+ products, and operations across all 7,641 islands.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Call to Action -->
    <section class="cta-about">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-4 fw-bold mb-4">Ready to Join Our Community?</h2>
                    <p class="lead mb-5">Become part of the LuzViMinDrop family and start building your dream business today. Together, we're reshaping the future of Philippine e-commerce.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="register.php" class="btn btn-primary-custom btn-lg">
                            <i class="fas fa-rocket me-2"></i>Start Your Journey
                        </a>
                        <a href="#contact" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-envelope me-2"></i>Get in Touch
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">LuzViMinDrop</h5>
                    <p class="mb-3">Empowering Filipino entrepreneurs with AI-powered dropshipping solutions across the Philippines.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-decoration-none">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                        <a href="#" class="text-decoration-none">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="text-decoration-none">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                        <a href="#" class="text-decoration-none">
                            <i class="fab fa-linkedin fa-lg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Platform</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.html#features">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">API</a></li>
                        <li><a href="#">Integrations</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Press</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Community</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                        <li><a href="#">GDPR</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: #34495e;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 LuzViMinDrop. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <!-- <p class="mb-0">Made with <i class="fas fa-heart text-danger"></i> in the Philippines</p> -->
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for navigation links
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

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.backgroundColor = 'rgba(44, 62, 80, 0.95)';
            } else {
                navbar.style.backgroundColor = 'transparent';
            }
        });

        // Animate timeline items on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateX(0)';
                }
            });
        }, observerOptions);

        // Observe timeline items
        document.querySelectorAll('.timeline-item').forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-50px)';
            item.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(item);
        });

        // Animate value cards on scroll
        document.querySelectorAll('.value-card, .team-card').forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(50px)';
            card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });
    </script>
</body>
</html>