<?php 
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception; 

require_once 'phpmailer/src/Exception.php'; 
require_once 'phpmailer/src/PHPMailer.php'; 
require_once 'phpmailer/src/SMTP.php'; 

// Start session only if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = 'localhost';
$db = 'user_signups';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create contacts table if it doesn't exist
    $conn->exec("CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$mail = new PHPMailer(true); 
$alert = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Determine if this is a login, signup, or contact form request
    $isSignup = isset($_POST['firstname']) && isset($_POST['lastname']);
    $isContact = isset($_POST['message']) && isset($_POST['subject']);
    
    if ($isContact) {
        // Contact Form Process
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $subject = trim($_POST['subject']);
        $message = trim($_POST['message']);
        
        // Validate contact form input
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $_SESSION['contact_error'] = "All fields are required.";
            header("Location: contact.php#contact-form");
            exit();
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['contact_error'] = "Invalid email format.";
            header("Location: contact.php#contact-form");
            exit();
        }
        
        try {
            // Store in database
            $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $subject, $message])) {
                // Send email notifications
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'leotrimthaqi605@gmail.com';
                    $mail->Password = 'pbcnawmjobxnhuqr';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Send to admin
                    $mail->setFrom('leotrimthaqi605@gmail.com', 'Spookify Contact Form');
                    $mail->addAddress('leotrimthaqi605@gmail.com');
                    $mail->addReplyTo($email, $name);
                    
                    $mail->isHTML(true);
                    $mail->Subject = 'New Contact Form Submission: ' . $subject;
                    $mail->Body = "
                        <h2>New Contact Form Submission</h2>
                        <p><strong>Name:</strong> {$name}</p>
                        <p><strong>Email:</strong> {$email}</p>
                        <p><strong>Subject:</strong> {$subject}</p>
                        <p><strong>Message:</strong></p>
                        <p>{$message}</p>
                    ";

                    $mail->send();
                    
                    // Send confirmation to user
                    $mail->clearAddresses();
                    $mail->addAddress($email);
                    $mail->Subject = 'Thank you for contacting Spookify';
                    $mail->Body = "
                        <h2>Thank you for contacting Spookify!</h2>
                        <p>Dear {$name},</p>
                        <p>We have received your message and will get back to you as soon as possible.</p>
                        <p>Your message details:</p>
                        <p><strong>Subject:</strong> {$subject}</p>
                        <p><strong>Message:</strong></p>
                        <p>{$message}</p>
                        <p>Best regards,<br>The Spookify Team</p>
                    ";
                    
                    $mail->send();
                    $_SESSION['contact_success'] = "Message sent successfully! We'll get back to you soon.";
                    
                } catch (Exception $e) {
                    $_SESSION['contact_error'] = "Failed to send email. Please try again later.";
                    error_log("Failed to send contact email: " . $e->getMessage());
                }
                
                header("Location: contact.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['contact_error'] = "Failed to submit message. Please try again.";
            header("Location: contact.php");
            exit();
        }
    } elseif ($isSignup) {
        // Existing Signup Process
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $password2 = $_POST['password2'];
        
        // Validate input
        if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
            $_SESSION['error'] = "All fields are required.";
            header("Location: signup.php");
            exit();
        } elseif ($password !== $password2) {
            $_SESSION['error'] = "Passwords do not match.";
            header("Location: signup.php");
            exit();
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
            header("Location: signup.php");
            exit();
        } else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $_SESSION['error'] = "An account with this email already exists.";
                header("Location: signup.php");
                exit();
            } else {
                // Insert new user
                try {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
                    
                    if ($stmt->execute([$firstname, $lastname, $email, $hashedPassword])) {
                        // Send welcome email
                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = 'leotrimthaqi605@gmail.com';
                            $mail->Password = 'pbcnawmjobxnhuqr';
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 587;

                            $mail->setFrom('leotrimthaqi605@gmail.com', 'Spookify');
                            $mail->addAddress($email);
                            
                            $mail->isHTML(true);
                            $mail->Subject = "Welcome to Spookify, $firstname!";
                            $mail->Body = "
                                <h2>Welcome to Spookify!</h2>
                                <p>Hi $firstname,</p>
                                <p>Thank you for creating an account with us. We're excited to have you on board!</p>
                                <p>Best regards,<br>The Spookify Team</p>";

                            $mail->send();
                        } catch (Exception $e) {
                            // Continue even if email fails
                            error_log("Failed to send welcome email: " . $e->getMessage());
                        }

                        $_SESSION['success'] = "Account created successfully!";
                        header("Location: login.php?signup_success=1");
                        exit();
                    }
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Registration failed. Please try again.";
                    header("Location: signup.php");
                    exit();
                }
            }
        }
    } else {
        // Existing Login Process
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Please enter both email and password.";
            header("Location: login.php");
            exit();
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['firstname'] = $user['firstname'];
                header("Location: dashboard.html");
                exit();
            } else {
                $_SESSION['error'] = "Invalid email or password.";
                header("Location: login.php");
                exit();
            }
        }
    }
}
?>