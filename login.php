<?php include 'send.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="css/login.css">
</head>
    <style>
        /* Success Message Style */
.alert-success {
    background-color: #d4edda; /* Light green background */
    border: 1px solid #c3e6cb; /* Slightly darker green border */
    color: #155724; /* Dark green text */
    padding: 15px;
    border-radius: 5px;
    font-size: 16px;
    font-family: Arial, sans-serif;
    width: 80%; /* Adjust width as needed */
    margin: 10px auto; /* Center the alert */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Slight shadow for depth */
    text-align: center;
}
.alert-success span {
    font-weight: bold;
}

    </style>

<body>
    <div class="login-box">

        <h1>Login</h1>
        <h4>Login in your account in minutes</h4>

         <!-- Success Message -->
         <?php if (isset($_GET['signup_success'])): ?>
            <div class="alert-success">
                <span>Account created successfully! You can now log in.</span>
            </div>
        <?php endif; ?>


        <form action="send.php" method="POST">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="client@testing.com" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>
    </div>
    <button type="submit" class="submit-btn">Login</button>
</form>

        <div class="para-2">
        </div>
    </div>

    <!-- Alert Message Display -->
    <?php if ($alert): ?>
        <div class="alert"><?= $alert ?></div>
    <?php endif; ?>

    <p class="para-2">Don't have an account? <a href="signup.php">Sign Up</a></p>
    <p class="para-2">Back to <a href="index.html">Homepage</a> for more information </p>
    
</body>

</html>