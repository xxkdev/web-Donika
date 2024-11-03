<?php include 'send.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="css/login.css">
</head>

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

    <p class="para-2">Don't have an account? <a href="signup.html">Sign Up</a></p>
    <p class="para-2">Back to <a href="index.html">Homepage</a> for more information </p>
    
</body>

</html>