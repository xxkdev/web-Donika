<?php 
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception; 

require_once 'phpmailer/src/Exception.php'; 
require_once 'phpmailer/src/PHPMailer.php'; 
require_once 'phpmailer/src/SMTP.php'; 

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = 'localhost';
$db = 'user_signups'; // Change to your database name
$user = 'root'; // Change to your DB username
$pass = ''; // Change to your DB password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$mail = new PHPMailer(true); 
$alert = ''; 

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'signup') {
        // Signup logic
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security

        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $alert = "<div class='alert-error'><span>An account with this email already exists.</span></div>";
        } else {
            // Insert data into database
            try {
                $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (:firstname, :lastname, :email, :password)");
                $stmt->bindParam(':firstname', $firstname);
                $stmt->bindParam(':lastname', $lastname);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $password);
                
                if ($stmt->execute()) {
                    // Send confirmation email
                    $mail->isSMTP(); 
                    $mail->Host = 'smtp.gmail.com'; 
                    $mail->SMTPAuth = true; 
                    $mail->Username = 'leotrimthaqi605@gmail.com'; // Your email
                    $mail->Password = 'kvoftbhafsalszfh'; // Your email password
                    $mail->SMTPSecure = 'tls'; 
                    $mail->Port = '587'; 

                    $mail->setFrom('leotrimthaqi605@gmail.com'); 
                    $mail->addAddress($email); // Send to user's email

                    $mail->isHTML(true); 
                    $mail->Subject = 'Welcome to Spookify, ' . $firstname; 
                    $mail->Body = "Hello $firstname, <br>Thank you for signing up! We’re thrilled to have you."; 

                    $mail->send(); 

                    // Redirect to login page with success message
                    header("Location: login.php?signup_success=1");
                    exit();
                } else {
                    $alert = "<div class='alert-error'><span>Failed to insert data into the database.</span></div>";
                }
            } catch (Exception $e) {
                $alert = "<div class='alert-error'><span>Failed to sign up: " . $e->getMessage() . "</span></div>"; 
            } 
        }
    } elseif ($action == 'login') {
        // Login logic
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct
            $alert = "<div class='alert-success'><span>Login Successful! Welcome back, " . $user['firstname'] . ".</span></div>";
        } else {
            // User does not exist or password is incorrect
            if (!$user) {
                $alert = "<div class='alert-error'><span>No account found with this email. Please sign up first.</span></div>";
            } else {
                $alert = "<div class='alert-error'><span>Incorrect password. Please try again.</span></div>";
            }
        }
    }
}
?>
