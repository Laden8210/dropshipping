<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - LuzViMinDrop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --gold: #f39c12;
            --light-bg: #f8f9fa;
            --dark-bg: #1a252f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--primary-color);
            scroll-behavior: smooth;
        }

        /* Sticky Navigation */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color)) !important;
            backdrop-filter: blur(20px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: rgba(44, 62, 80, 0.95) !important;
            padding: 0.5rem 0;
        }

        .navbar-brand img {
            max-height: 40px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: #ffd700 !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #ffd700;
            border-radius: 2px;
        }

        /* Hero Section - General Information */
        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #fff, #ffd700);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(1.1rem, 3vw, 1.4rem);
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
            color: rgba(255, 255, 255, 0.8);
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            40% {
                transform: translateX(-50%) translateY(-10px);
            }

            60% {
                transform: translateX(-50%) translateY(-5px);
            }
        }

        /* Project Info Section */
        .project-info {
            padding: 100px 0;
            background: white;
            position: relative;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.15);
        }

        .info-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .info-icon.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
        }

        .info-icon.secondary {
            background: linear-gradient(135deg, var(--secondary-color), #c0392b);
            color: white;
        }

        .info-icon.success {
            background: linear-gradient(135deg, var(--success-color), #2ecc71);
            color: white;
        }

        .info-icon.gold {
            background: linear-gradient(135deg, var(--gold), #e67e22);
            color: white;
        }

        /* Sticky Stacking Sections for Proponents */
        .proponents-section {
            background: var(--light-bg);
            position: relative;
        }

        .proponent-stack {
            position: sticky;
            top: 80px;
            height: 100vh;
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 10;
        }

        .proponent-stack:nth-child(1) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .proponent-stack:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .proponent-stack:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .proponent-stack:nth-child(4) {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .proponent-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 50px;
            margin: 0 auto;
            max-width: 900px;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .proponent-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
            margin: 0 auto 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .proponent-avatar::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%) translateY(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) translateY(100%) rotate(45deg);
            }
        }

        .role-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--gold), #e67e22);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .contact-info {
            background: var(--light-bg);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .feature-item {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--accent-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .proponent-card {
                padding: 30px;
                margin: 20px;
            }

            .proponent-avatar {
                width: 120px;
                height: 120px;
                font-size: 3rem;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .navbar-nav {
                text-align: center;
            }

            .proponent-stack {
                height: auto;
                min-height: 100vh;
                padding: 40px 20px;
            }
        }

        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Parallax Effect */
        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Sticky Navigation -->
    <nav class="navbar navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <img src="assets/img/logo.png" alt="LuzViMinDrop" class="img-fluid">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section - General Information -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content" data-aos="fade-right">
                        <h1 class="hero-title">LuzViMinDrop</h1>
                        <p class="hero-subtitle">AI-Enhanced Dropshipping & E-Commerce Platform for the Philippines</p>
                        <p class="lead mb-4">Empowering Filipino entrepreneurs with centralized control over store management, product sourcing, and order tracking while providing consumers with a seamless shopping experience.</p>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="#project" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-rocket me-2"></i>Learn More
                            </a>
                            <a href="#team" class="btn btn-light btn-lg">
                                <i class="fas fa-users me-2"></i>Meet the Team
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="floating-animation" data-aos="fade-left">
                        <div style="background: rgba(255,255,255,0.1); border-radius: 20px; padding: 40px; backdrop-filter: blur(20px);">
                            <i class="fas fa-laptop-code" style="font-size: 8rem; color: rgba(255,255,255,0.8);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down fa-2x"></i>
        </div>
    </section>

    <!-- Project Information Section -->
    <section class="project-info" id="project">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold mb-3">Project Overview</h2>
                <p class="lead text-muted">Comprehensive information about LuzViMinDrop system</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="info-card">
                        <div class="info-icon primary">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h4 class="mb-3">Application Details</h4>
                        <p><strong>Name:</strong> LuzViMinDrop</p>
                        <p><strong>Platform:</strong> Web Portal and Android Mobile Application</p>
                        <p><strong>Purpose:</strong> AI-enhanced dropshipping and e-commerce platform designed to cater online business operations in the Philippines.</p>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="info-card">
                        <div class="info-icon secondary">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="mb-3">Core Users</h4>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Business Owners</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Suppliers</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Consumers</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Courier Services</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="info-card">
                        <div class="info-icon success">
                            <i class="fas fa-target"></i>
                        </div>
                        <h4 class="mb-3">Target Beneficiaries</h4>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-arrow-right text-primary me-2"></i><strong>Business Owners:</strong> Easier way to run online stores without complex logistics</li>
                            <li><i class="fas fa-arrow-right text-primary me-2"></i><strong>Consumers:</strong> Secure, reliable, and transparent online shopping experience</li>
                            <li><i class="fas fa-arrow-right text-primary me-2"></i><strong>PH E-commerce Industry:</strong> Promotes digital business adoption and local dropshipping growth</li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="info-card">
                        <div class="info-icon gold">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h4 class="mb-3">Key Features</h4>
                        <div class="row">
                            <div class="col-6">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Portal Module</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Product Search & Import</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Supplier Management</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Inventory Management</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Order Management</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Reports Module</li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Forex Conversion</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Order Tracking</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Ratings & Feedback</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>SMS Notifications</li>
                                    <li><i class="fas fa-dot-circle text-primary me-2"></i>Customer Support</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Proponents Section with Sticky Stacking -->
    <section class="proponents-section" id="team">
        <!-- Proponent 1: Arfe May I. Bancua -->
        <div class="proponent-stack">
            <div class="proponent-card" data-aos="zoom-in">
                <div class="text-center">
                    <div class="proponent-avatar">
                        <img src="public/images/Arfe.png" alt="Arfe May I. Bancua" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="role-badge">UI/UX Designer & Documentation</div>
                    <h3 class="fw-bold mb-3">Arfe May I. Bancua</h3>
                    <h5 class="text-muted mb-4">BS Information Technology</h5>
                    <p class="lead mb-4">Passionate about creativity and enjoys exploring art and design. Highly motivated to keep learning and improving skills. Responsible for note-taking during meetings and sessions, documentation, and UI/UX design.</p>
                    <div class="contact-info">
                        <p class="mb-0"><i class="fas fa-envelope text-primary me-2"></i>amibancua@bpsu.edu.ph</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proponent 2: Mansour J. Asha -->
        <div class="proponent-stack">
            <div class="proponent-card" data-aos="zoom-in">
                <div class="text-center">
                    <div class="proponent-avatar">
                        <img src="public/images/Mansour.png" alt="Mansour J. Asha" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="role-badge">Web Programmer</div>
                    <h3 class="fw-bold mb-3">Mansour J. Asha</h3>
                    <h5 class="text-muted mb-4">BS Information Technology</h5>
                    <p class="lead mb-4">A working student striving to finish studies despite age. Interested in science, technology, and learning more about cybersecurity and business. Family is the greatest motivation and strength, inspiring a drive to succeed and help bring them back on top.</p>
                    <div class="contact-info">
                        <p class="mb-0"><i class="fas fa-envelope text-primary me-2"></i>mjasha@bpsu.edu.ph</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proponent 3: Denand S. Garcia -->
        <div class="proponent-stack">
            <div class="proponent-card" data-aos="zoom-in">
                <div class="text-center">
                    <div class="proponent-avatar">
                        <img src="public/images/Denand.png" alt="Denand S. Garcia" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="role-badge">Documentation & UI/UX Designer</div>
                    <h3 class="fw-bold mb-3">Denand S. Garcia</h3>
                    <h5 class="text-muted mb-4">BS Information Technology</h5>
                    <p class="lead mb-4">An IT student with skills in troubleshooting and system design. Driven by a passion for learning new technologies and improving system efficiency. Contributes to both documentation and UI/UX design aspects of the project.</p>
                    <div class="contact-info">
                        <p class="mb-0"><i class="fas fa-envelope text-primary me-2"></i>dsgarcia@bpsu.edu.ph</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Proponent 4: Axel Edwin Moncillo -->
        <div class="proponent-stack">
            <div class="proponent-card" data-aos="zoom-in">
                <div class="text-center">
                    <div class="proponent-avatar">
                        <img src="public/images/Axel.png" alt="Axel Edwin Moncillo" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="role-badge">Mobile App Programmer & Documentation</div>
                    <h3 class="fw-bold mb-3">Axel Edwin Moncillo</h3>
                    <h5 class="text-muted mb-4">BS Information Technology</h5>
                    <p class="lead mb-4">Has a strong interest in development and enjoys solving technical problems. Motivated by a desire to build useful systems and continuously grow skills in programming and user-centered design.</p>
                    <div class="contact-info">
                        <p class="mb-0"><i class="fas fa-envelope text-primary me-2"></i>amoncillo@bpsu.edu.ph</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="project-info" id="features" style="background: white;">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold mb-3">System Features</h2>
                <p class="lead text-muted">Comprehensive modules designed for efficient e-commerce management</p>
            </div>

            <div class="features-grid">
                <div class="feature-item" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Product Search & Import</h5>
                    <p>Advanced AI-powered product discovery and seamless import from various suppliers.</p>
                </div>

                <div class="feature-item" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Inventory Management</h5>
                    <p>Real-time inventory tracking with automated stock alerts and management tools.</p>
                </div>

                <div class="feature-item" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Analytics & Reports</h5>
                    <p>Comprehensive reporting dashboard with sales analytics and performance metrics.</p>
                </div>

                <div class="feature-item" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Order Tracking</h5>
                    <p>End-to-end order tracking with real-time updates and customer notifications.</p>
                </div>

                <div class="feature-item" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Ratings & Reviews</h5>
                    <p>Built-in feedback system to maintain quality and build customer trust.</p>
                </div>

                <div class="feature-item" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-icon">
                        <i class="fas fa-sms"></i>
                    </div>
                    <h5 class="fw-bold mb-3">SMS Notifications</h5>
                    <p>Automated SMS updates for orders, deliveries, and important system notifications.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Hide loading overlay when page loads
        window.addEventListener('load', function() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.opacity = '0';
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 500);
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const navbarHeight = document.querySelector('.navbar').offsetHeight;
                    const targetPosition = target.offsetTop - navbarHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Update active navigation link on scroll
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero-section');
            if (hero) {
                hero.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.info-card, .feature-item').forEach(el => {
            observer.observe(el);
        });

        // Add custom CSS animation classes
        const style = document.createElement('style');
        style.textContent = `
            .animate__animated {
                animation-duration: 0.8s;
                animation-fill-mode: both;
            }
            
            .animate__fadeInUp {
                animation-name: fadeInUp;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translate3d(0, 40px, 0);
                }
                to {
                    opacity: 1;
                    transform: translate3d(0, 0, 0);
                }
            }
        `;
        document.head.appendChild(style);

        // Typing animation for hero title
        function typeWriter(element, text, speed = 100) {
            let i = 0;
            element.innerHTML = '';

            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }

        // Initialize typing animation when hero comes into view
        const heroObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const titleElement = entry.target.querySelector('.hero-title');
                    if (titleElement && !titleElement.classList.contains('typed')) {
                        titleElement.classList.add('typed');
                        typeWriter(titleElement, 'LuzViMinDrop', 150);
                    }
                }
            });
        });

        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            heroObserver.observe(heroSection);
        }

        // Add sound effects (optional)
        function playClickSound() {
            // Create audio context for click sounds
            const audioContext = new(window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        }

        // Add click sound to navigation links
        document.querySelectorAll('.nav-link, .btn').forEach(element => {
            element.addEventListener('click', playClickSound);
        });

        // Mobile menu improvements
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');

        if (navbarToggler) {
            navbarToggler.addEventListener('click', function() {
                this.classList.toggle('active');
            });
        }

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            });
        });

        // Add loading progress indicator
        function updateProgress() {
            const scrollTop = window.pageYOffset;
            const docHeight = document.body.scrollHeight - window.innerHeight;
            const scrollPercent = (scrollTop / docHeight) * 100;

            let progressBar = document.getElementById('progress-bar');
            if (!progressBar) {
                progressBar = document.createElement('div');
                progressBar.id = 'progress-bar';
                progressBar.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: ${scrollPercent}%;
                    height: 3px;
                    background: linear-gradient(90deg, #3498db, #e74c3c);
                    z-index: 9999;
                    transition: width 0.1s ease;
                `;
                document.body.appendChild(progressBar);
            } else {
                progressBar.style.width = scrollPercent + '%';
            }
        }

        window.addEventListener('scroll', updateProgress);
        updateProgress(); // Initialize

        // Easter egg: Konami code
        let konamiCode = [];
        const konamiSequence = [
            'ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown',
            'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight',
            'KeyB', 'KeyA'
        ];

        document.addEventListener('keydown', function(e) {
            konamiCode.push(e.code);
            if (konamiCode.length > konamiSequence.length) {
                konamiCode.shift();
            }

            if (JSON.stringify(konamiCode) === JSON.stringify(konamiSequence)) {
                // Easter egg activated!
                document.body.style.animation = 'rainbow 2s infinite';
                setTimeout(() => {
                    document.body.style.animation = '';
                    alert('🎉 Easter egg activated! You found the secret code! 🎉');
                }, 2000);
                konamiCode = [];
            }
        });

        // Add rainbow animation for easter egg
        const rainbowStyle = document.createElement('style');
        rainbowStyle.textContent = `
            @keyframes rainbow {
                0% { filter: hue-rotate(0deg); }
                100% { filter: hue-rotate(360deg); }
            }
        `;
        document.head.appendChild(rainbowStyle);

        // Performance optimization: Throttle scroll events
        let ticking = false;

        function updateOnScroll() {
            updateProgress();

            // Update navbar
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            ticking = false;
        }

        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateOnScroll);
                ticking = true;
            }
        }

        window.addEventListener('scroll', requestTick);
    </script>
</body>

</html>