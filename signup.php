<?php 
session_start();
include 'send.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/signup.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        .alert-error {
            background-color: #ff4444;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-success {
            background-color: #00C851;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="signup-box">
        <h1>Sign Up</h1>
        <h4>Create your account in minutes</h4>

        <?php
        // Display error message if exists
        if (isset($_SESSION['error'])) {
            echo "<div class='alert-error'><span>" . $_SESSION['error'] . "</span></div>";
            unset($_SESSION['error']); // Clear the error message
        }
        // Display success message if exists
        if (isset($_SESSION['success'])) {
            echo "<div class='alert-success'><span>" . $_SESSION['success'] . "</span></div>";
            unset($_SESSION['success']); // Clear the success message
        }
        ?>

        <form method="POST" action="send.php">
            <div class="form-row">
                <div class="half-width">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Enter first name" required>
                </div>
                <div class="half-width">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" placeholder="Enter last name" required>
                </div>
            </div>

            <div class="form-row" style="animation-delay: 0.1s">
                <div class="half-width">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter email address" required>
                </div>
            </div>

            <div class="form-row" style="animation-delay: 0.2s">
                <div class="half-width">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>
                <div class="half-width">
                    <label for="password2">Confirm Password</label>
                    <input type="password" id="password2" name="password2" placeholder="Confirm password" required>
                </div>
            </div>

            <button type="submit" class="submit-btn">Create Account</button>
        </form>
    </div>

    <p class="para-2">Already have an account? <a href="login.php">Sign in</a></p>
    <p class="para-2">Back to <a href="index.html">Homepage</a> for more information</p>
</body>
</html>