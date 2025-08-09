<?php 
require_once 'config.php';

// Get settings from database
$stmt = $pdo->query("SELECT * FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Get top students
$top_students = $pdo->query("SELECT * FROM top_students ORDER BY id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Get training videos
$training_videos = $pdo->query("SELECT * FROM videos WHERE type = 'training' AND status = 'active' ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get recorded lectures
$recorded_lectures = $pdo->query("SELECT * FROM videos WHERE type = 'recorded' AND status = 'active' ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Check if user is logged in to show video access
$user_videos = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT v.id 
        FROM videos v 
        JOIN assigned_videos av ON v.id = av.video_id 
        WHERE av.user_id = ? AND av.status = 'active' AND av.expiry_date > NOW()
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user_videos = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id');
}

// Get approved testimonials
$testimonials = $pdo->query("SELECT * FROM testimonials WHERE status = 'approved' ORDER BY id DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_title'] ?? 'GT Online Class'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- WhatsApp Help Button -->
    <a href="https://wa.me/<?php echo $settings['whatsapp_number']; ?>?text=Hello, I need help regarding GT Online Class" 
       class="whatsapp-float" target="_blank" title="Need Help? Chat with us">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-graduation-cap"></i>
                <span>GT Online Class</span>
            </div>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#students">Top Students</a>
                <a href="#trainings">Trainings</a>
                <a href="#admission">Admission</a>
                <a href="#testimonials">Reviews</a>
                <a href="#lectures">Lectures</a>
                <a href="user/login.php" class="btn btn-primary">Login</a>
            </div>
            <div class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->
    <section id="home" class="hero">
        <div class="hero-background">
            <div class="floating-icons">
                <i class="fas fa-chalkboard-teacher" title="Online Classes"></i>
                <i class="fas fa-users" title="Students"></i>
                <i class="fas fa-laptop" title="E-Learning"></i>
                <i class="fas fa-graduation-cap" title="Education"></i>
                <i class="fas fa-book-reader" title="Study"></i>
                <i class="fas fa-video" title="Video Lectures"></i>
                <i class="fas fa-certificate" title="Certification"></i>
                <i class="fas fa-brain" title="Learning"></i>
            </div>
        </div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title animate-fade-up"><?php echo $settings['welcome_message']; ?></h1>
                <p class="hero-subtitle animate-fade-up" style="animation-delay: 0.2s">
                    Empowering minds through quality education and innovative learning experiences
                </p>
                <div class="hero-buttons animate-fade-up" style="animation-delay: 0.4s">
                    <a href="#trainings" class="btn btn-primary">Start Learning</a>
                    <a href="#admission" class="btn btn-secondary">Join Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Top 10 Students Section -->
    <section id="students" class="students-section">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-trophy"></i>
                TOP 10 Students of the Year
            </h2>
            <div class="students-grid">
                <?php foreach ($top_students as $student): ?>
                    <div class="student-card animate-scale">
                        <div class="student-image">
                            <img src="<?php echo $student['image']; ?>" alt="<?php echo $student['name']; ?>">
                            <div class="student-overlay">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <h3><?php echo $student['name']; ?></h3>
                        <p><?php echo $student['description']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Training Videos Section -->
    <section id="trainings" class="section">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-play-circle"></i>
                New Launched Trainings & Activities
            </h2>
            <div class="videos-grid">
                <?php foreach ($training_videos as $video): ?>
                    <div class="video-card animate-fade-up">
                        <div class="video-thumbnail">
                            <video controls preload="metadata">
                                <source src="uploads/<?php echo $video['filename']; ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="video-info">
                            <h3><?php echo $video['title']; ?></h3>
                            <p><?php echo $video['description']; ?></p>
                            <div class="video-meta">
                                <span class="type-badge" style="background: var(--success-color); color: white; padding: var(--spacing-1) var(--spacing-2); border-radius: var(--spacing-1); font-size: var(--font-size-xs);">
                                    <i class="fas fa-play"></i> Free Training
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Admission Form Section -->
    <section id="admission" class="admission-section">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-user-plus"></i>
                Admission Form - Next Offline Batch
            </h2>
            <div class="form-container">
                <form id="admissionForm" class="admission-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fullName"><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" id="fullName" name="fullName" required>
                        </div>
                        <div class="form-group">
                            <label for="mobile"><i class="fas fa-phone"></i> Mobile Number</label>
                            <input type="tel" id="mobile" name="mobile" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="address"><i class="fas fa-map-marker-alt"></i> Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="schoolName"><i class="fas fa-school"></i> School Name</label>
                            <input type="text" id="schoolName" name="schoolName" required>
                        </div>
                        <div class="form-group">
                            <label for="standard"><i class="fas fa-graduation-cap"></i> Standard</label>
                            <input type="text" id="standard" name="standard" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="timeSlot"><i class="fas fa-clock"></i> Time Slot (Optional)</label>
                        <input type="text" id="timeSlot" name="timeSlot" placeholder="Leave blank if no preference">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fab fa-whatsapp"></i>
                        Submit via WhatsApp
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-star"></i>
                Student Reviews & Ratings
            </h2>
            <div class="testimonials-grid">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="testimonial-card animate-fade-up">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="testimonial-info">
                                <h4><?php echo $testimonial['name']; ?></h4>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <p class="testimonial-text"><?php echo substr($testimonial['review'], 0, 200) . '...'; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="testimonial-actions">
                <button class="btn btn-primary" onclick="openTestimonialForm()">
                    <i class="fas fa-edit"></i>
                    Write a Testimonial
                </button>
            </div>
        </div>
    </section>

    <!-- Recorded Lectures Section -->
    <section id="lectures" class="lectures-section">
        <div class="container">
            <h2 class="section-title">
                <i class="fas fa-video"></i>
                Recorded Lecture Videos
            </h2>
            <div class="lectures-grid">
                <?php foreach ($recorded_lectures as $lecture): ?>
                    <?php $hasAccess = in_array($lecture['id'], $user_videos); ?>
                    <div class="lecture-card animate-fade-up">
                        <div class="lecture-thumbnail">
                            <?php if (!$hasAccess): ?>
                                <div class="locked-overlay">
                                    <i class="fas fa-lock"></i>
                                    <span>Premium Content</span>
                                </div>
                            <?php endif; ?>
                            <img src="https://images.pexels.com/photos/5905709/pexels-photo-5905709.jpeg?auto=compress&cs=tinysrgb&w=400&h=250&fit=crop" alt="<?php echo $lecture['title']; ?>">
                        </div>
                        <div class="lecture-info">
                            <h3><?php echo $lecture['title']; ?></h3>
                            <p><?php echo $lecture['description']; ?></p>
                            <div class="lecture-price">
                                <span class="price">₹<?php echo number_format($lecture['price']); ?></span>
                                <?php if ($hasAccess): ?>
                                    <a href="user/dashboard.php" class="btn" style="background: var(--success-color); color: white;">
                                        <i class="fas fa-play"></i> Watch Now
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-unlock" onclick="initiatePayment(<?php echo $lecture['id']; ?>)">
                                        <i class="fas fa-unlock"></i>
                                        Unlock @ ₹<?php echo number_format($lecture['price']); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <i class="fas fa-graduation-cap"></i>
                    <span>GT Online Class</span>
                </div>
                <div class="footer-links">
                    <a href="#home">Home</a>
                    <a href="#students">Students</a>
                    <a href="#trainings">Trainings</a>
                    <a href="#admission">Admission</a>
                    <a href="admin/login.php">Admin</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> All Rights Reserved - GTAi.in</p>
            </div>
        </div>
    </footer>

    <!-- Testimonial Modal -->
    <div id="testimonialModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Write Your Testimonial</h2>
            <form id="testimonialForm">
                <div class="form-group">
                    <label for="testimonialName">Your Name</label>
                    <input type="text" id="testimonialName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="testimonialMobile">Mobile Number</label>
                    <input type="tel" id="testimonialMobile" name="mobile" required>
                </div>
                <div class="form-group">
                    <label for="testimonialReview">Your Review (Max 500 words)</label>
                    <textarea id="testimonialReview" name="review" maxlength="500" rows="8" placeholder="Share your experience with GT Online Class..." required></textarea>
                    <div class="character-count">0/500</div>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePaymentModal()">&times;</span>
            <h2><i class="fas fa-credit-card"></i> Complete Payment</h2>
            <div id="paymentContent">
                <!-- Payment content will be loaded here -->
            </div>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
    <script>
    // Admission form submission
    document.getElementById('admissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        // Create WhatsApp message
        const message = `🎓 GT Online Class - Admission Form

👤 Name: ${data.fullName}
📱 Mobile: ${data.mobile}
📧 Email: ${data.email}
📍 Address: ${data.address}
🏫 School: ${data.schoolName}
📚 Standard: ${data.standard}
⏰ Preferred Time: ${data.timeSlot || 'No preference'}

Please confirm my admission for the next offline batch.`;

        // Get WhatsApp number from settings or use default
        const whatsappNumber = '<?php echo htmlspecialchars($settings["whatsapp_number"] ?? "+919876543210"); ?>';
        const whatsappURL = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
        window.open(whatsappURL, '_blank');
    });

    // Testimonial form functionality
    const testimonialModal = document.getElementById('testimonialModal');
    const testimonialForm = document.getElementById('testimonialForm');
    const testimonialReview = document.getElementById('testimonialReview');
    const characterCount = document.querySelector('.character-count');

    window.openTestimonialForm = function() {
        testimonialModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    };

    // Close modal when clicking the X
    document.querySelector('#testimonialModal .close').addEventListener('click', function() {
        testimonialModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    // Close modal when clicking outside
    testimonialModal.addEventListener('click', function(e) {
        if (e.target === testimonialModal) {
            testimonialModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // Character count for testimonial
    testimonialReview.addEventListener('input', function() {
        const count = this.value.length;
        characterCount.textContent = `${count}/500`;
        
        if (count > 450) {
            characterCount.style.color = '#dc2626';
        } else {
            characterCount.style.color = '#6b7280';
        }
    });

    // Testimonial form submission
    testimonialForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('submit_testimonial.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Thank you! Your testimonial has been submitted and is pending approval.');
                testimonialModal.style.display = 'none';
                document.body.style.overflow = 'auto';
                testimonialForm.reset();
                characterCount.textContent = '0/500';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    });

    // Payment functionality
    window.initiatePayment = function(videoId) {
        // Show loading
        const paymentModal = document.getElementById('paymentModal');
        const paymentContent = document.getElementById('paymentContent');
        
        paymentContent.innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem;"></i><p>Loading payment details...</p></div>';
        paymentModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Fetch payment details
        fetch('payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `video_id=${videoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showPaymentDetails(data);
            } else {
                alert('Error: ' + data.message);
                closePaymentModal();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            closePaymentModal();
        });
    };
    
    function showPaymentDetails(data) {
        const paymentContent = document.getElementById('paymentContent');
        
        // Start 5-minute timer
        let timeLeft = 300; // 5 minutes in seconds
        
        paymentContent.innerHTML = `
            <div class="payment-info">
                <h3>${data.video_title}</h3>
                <div class="payment-amount">₹${data.amount}</div>
                
                <div class="timer-section" style="background: #fef3c7; padding: var(--spacing-4); border-radius: var(--spacing-2); margin: var(--spacing-4) 0; text-align: center;">
                    <h4 style="color: #92400e; margin-bottom: var(--spacing-2);">
                        <i class="fas fa-clock"></i> Payment Timer
                    </h4>
                    <div id="paymentTimer" style="font-size: var(--font-size-2xl); font-weight: 700; color: #92400e;">
                        05:00
                    </div>
                    <p style="color: #92400e; font-size: var(--font-size-sm); margin-top: var(--spacing-2);">
                        Complete payment within this time
                    </p>
                </div>
                
                <div class="upi-info">
                    <h4><i class="fas fa-mobile-alt"></i> Pay via UPI</h4>
                    <div class="upi-id">${data.upi_id}</div>
                    <div style="margin: var(--spacing-4) 0;">
                        <div id="qrcode" style="display: flex; justify-content: center; margin: var(--spacing-4) 0;"></div>
                        <p style="color: var(--gray-600); font-size: var(--font-size-sm); text-align: center;">
                            Scan QR code or use UPI ID above
                        </p>
                    </div>
                </div>
                
                <div class="payment-steps">
                    <h4 style="color: #dc2626;"><i class="fas fa-exclamation-triangle"></i> Important Instructions:</h4>
                    <ol>
                        <li><strong>Make payment of ₹${data.amount} within 5 minutes</strong></li>
                        <li><strong>Take screenshot of payment confirmation</strong></li>
                        <li><strong>Share screenshot to admin via WhatsApp</strong></li>
                        <li><strong>Get video access soon in your user account</strong></li>
                        <li><strong>Register now with your ID for instant access</strong></li>
                    </ol>
                </div>
                
                <div class="payment-buttons">
                    <a href="${data.upi_url}" class="btn btn-primary">
                        <i class="fas fa-mobile-alt"></i> Pay ₹${data.amount}
                    </a>
                    <a href="https://wa.me/${data.whatsapp_number.replace('+', '')}?text=${encodeURIComponent(`🎓 GT Online Class Payment\n\n✅ Payment Completed: ₹${data.amount}\n📹 Video: ${data.video_title}\n🆔 Payment ID: ${data.payment_id}\n\n📸 Screenshot attached\n⏰ Please activate my access`)}" 
                       class="btn btn-secondary" target="_blank">
                        <i class="fab fa-whatsapp"></i> Share Screenshot
                    </a>
                </div>
                
                <div style="text-align: center; margin-top: var(--spacing-6); padding: var(--spacing-4); background: var(--gray-50); border-radius: var(--spacing-2);">
                    <h4 style="color: var(--primary-color); margin-bottom: var(--spacing-3);">
                        <i class="fas fa-user-plus"></i> Don't have an account?
                    </h4>
                    <p style="margin-bottom: var(--spacing-3); color: var(--gray-600);">
                        Register now with your ID for instant video access!
                    </p>
                    <a href="user/register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register Now
                    </a>
                </div>
                
                <p style="margin-top: var(--spacing-4); font-size: var(--font-size-sm); color: var(--gray-600);">
                    <i class="fas fa-shield-alt"></i> Secure payment powered by UPI | <i class="fas fa-headset"></i> 24/7 Support available
                </p>
            </div>
        `;
        
        // Generate QR Code for UPI payment
        generateQRCode(data.upi_url);
        
        // Start countdown timer
        const timerInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('paymentTimer').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                document.getElementById('paymentTimer').textContent = 'EXPIRED';
                document.getElementById('paymentTimer').style.color = '#dc2626';
                alert('Payment time expired. Please try again.');
                closePaymentModal();
            }
            timeLeft--;
        }, 1000);
    }
    
    function generateQRCode(upiUrl) {
        // Simple QR code generation using Google Charts API
        const qrContainer = document.getElementById('qrcode');
        const qrSize = 200;
        const qrImg = document.createElement('img');
        qrImg.src = `https://chart.googleapis.com/chart?chs=${qrSize}x${qrSize}&cht=qr&chl=${encodeURIComponent(upiUrl)}`;
        qrImg.alt = 'UPI Payment QR Code';
        qrImg.style.border = '2px solid var(--primary-color)';
        qrImg.style.borderRadius = 'var(--spacing-2)';
        qrContainer.appendChild(qrImg);
    }
    
    window.closePaymentModal = function() {
        const paymentModal = document.getElementById('paymentModal');
        paymentModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };

    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    mobileMenuToggle.addEventListener('click', function() {
        navLinks.classList.toggle('active');
        this.classList.toggle('active');
    });

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
                // Close mobile menu if open
                navLinks.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
            }
        });
    });

    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, observerOptions);

    // Observe all animated elements
    document.querySelectorAll('.animate-fade-up, .animate-scale').forEach(el => {
        observer.observe(el);
    });
    </script>
</body>
</html>