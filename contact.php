<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spookify - Halloween Decor</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .popup-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 350px;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(400px);
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-popup {
            background-color: #4CAF50;
            color: white;
            border-left: 5px solid #388E3C;
        }

        .error-popup {
            background-color: #f44336;
            color: white;
            border-left: 5px solid #d32f2f;
        }

        .popup-container.show {
            transform: translateX(0);
        }

        .popup-progress-bar {
            height: 100%;
            width: 100%;
            background: rgba(255, 255, 255, 0.7);
            transform-origin: left;
            animation: progress 3s linear forwards;
        }

        @keyframes progress {
            100% {
                transform: scaleX(0);
            }
        }
    </style>
</head>
<body>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function showMessage($type, $message) {
    $icon = $type === 'success' ? '✓' : '✕';
    echo "
    <div class='popup-container {$type}-popup' id='popup'>
        <div class='popup-icon'>{$icon}</div>
        <div class='popup-content'>
            <p class='popup-message'>{$message}</p>
        </div>
        <button class='popup-close' onclick='closePopup()'>×</button>
        <div class='popup-progress'>
            <div class='popup-progress-bar'></div>
        </div>
    </div>
    ";
}

// Check for messages
if (isset($_SESSION['contact_error'])) {
    showMessage('error', $_SESSION['contact_error']);
    unset($_SESSION['contact_error']);
}
if (isset($_SESSION['contact_success'])) {
    showMessage('success', $_SESSION['contact_success']);
    unset($_SESSION['contact_success']);
}
?>


<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Add your email sending logic here
    // For example:
    $to = "your-email@example.com";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    try {
        if(mail($to, $subject, $message, $headers)) {
            $_SESSION['contact_success'] = "Message sent successfully!";
        } else {
            $_SESSION['contact_error'] = "Error sending message. Please try again.";
        }
    } catch (Exception $e) {
        $_SESSION['contact_error'] = "Error sending message. Please try again.";
    }
    
    // Redirect back to contact page
    header("Location: contact.php");
    exit();
}
?>
<script>
// Make sure this script runs after the popup is added to the DOM
document.addEventListener('DOMContentLoaded', function() {
    showPopup();
});

function showPopup() {
    const popup = document.getElementById('popup');
    if (popup) {
        // Small delay to ensure transition works
        setTimeout(() => {
            popup.classList.add('show');
        }, 100);
        
        // Auto close after 3 seconds
        setTimeout(() => {
            closePopup();
        }, 3000);
    }
}

function closePopup() {
    const popup = document.getElementById('popup');
    if (popup) {
        popup.classList.remove('show');
        setTimeout(() => {
            popup.remove();
        }, 500);
    }
}

// Close popup on click outside
document.addEventListener('click', function(event) {
    const popup = document.getElementById('popup');
    if (popup && !popup.contains(event.target)) {
        closePopup();
    }
});

document.querySelectorAll('.faq-question').forEach(item => {
            item.addEventListener('click', () => {
                const answer = item.nextElementSibling;
                answer.style.display = answer.style.display === 'block' ? 'none' : 'block';
            });
        });

</script>
<body>
    <header>
        <div class="navbar">
            <h1 class="logo">Spookify</h1>
            <nav>
                <ul class="center-links">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About Us</a></li>
                    <li><a href="blog.html">Blog</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
                <ul class="right-link">
                    <li><a href="signup.php" class="btn-primary">Sign Up</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="content">
            <h2>Contact <span>Us</span></h2>
            <p>Reach out for more eerie details or help with your Halloween decor needs. Our contact details and address are below!</p>
            <div class="buttons">
                <a href="https://www.horror-shop.com/" target="_blank" class="btn-primary">Shop the Scare</a>
                <a href="https://www.history.com/topics/halloween/history-of-halloween" target="_blank" class="btn-secondary">More Details</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="images/skeleton.png" alt="Halloween Skeleton with Witch Hat">
        </div>
    </section>
    <hr>

    <!-- Contact Form Section -->
    <section class="contact-section">
        <h2 class="section-title">Get in Touch</h2>
        <form class="contact-form" action="send.php" method="post">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" placeholder="Subject" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="4" placeholder="Write your message here..." required></textarea>

            <button type="submit" class="btn-primary">Send Message</button>
        </form>
    </section>


     <!-- New FAQ Section -->
     <section class="faq-section">
        <h2 class="faq-title">Frequently Asked Questions</h2>
        <div class="faq-item">
            <div class="faq-question">What types of Halloween decor do you offer?</div>
            <div class="faq-answer">We offer a wide variety of decorations including spooky lights, creepy crawlies, and themed props to transform your space.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question">How can I place an order?</div>
            <div class="faq-answer">You can place an order through our website or visit our store during business hours for more personalized assistance.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question">Do you offer customization on decorations?</div>
            <div class="faq-answer">Yes, we provide customization options for many of our products to fit your unique Halloween theme.</div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <ul class="footer-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="blog.html">Blog</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
            <div class="social-icons">
                <a href="#"><img src="images/facebook.png" alt="Facebook"></a>
                <a href="#"><img src="images/instagram.png" alt="Instagram"></a>
                <a href="#"><img src="images/linkedin.png" alt="LinkedIn"></a>
            </div>
            <p class="footer-text">© 2024 Spookify. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
