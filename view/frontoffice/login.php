<?php
require_once(__DIR__ . "/../../controller/userController.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Check credentials (this is a placeholder, implement your own logic)
        $userController = new userController();
        $user = $userController->getUserByUsername($username); // Assuming username is the ID for simplicity

        if ($user['password'] === $password) { // Replace with hashed password check
            $_SESSION['user'] = $user;
            if ($_SESSION['user']['Role'] === 'admin') {
                header("Location: ../backoffice/chatbox.php"); // Redirect to admin dashboard
            } else {
                header("Location: contact.php"); // Redirect to dashboard or home page
            }
            
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
}





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Login Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-container h2 {
            text-align: center;
            margin-bottom: 24px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .login-button:hover {
            background-color: #0056b3;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 16px;
        }
        
        .forgot-password a {
            color: #666;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-button">Login</button>
            <div class="forgot-password">
                <a href="#">Forgot your password?</a>
            </div>
        </form>
    </div>
</body>
</html>