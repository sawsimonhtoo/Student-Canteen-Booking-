<?php
session_start();

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'canteen_db';
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process registration when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($name) > 50) { 
        $error = "Name must be less than 50 characters";
    } elseif (strlen($email) > 100) { 
        $error = "Email must be less than 100 characters";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT UserID FROM users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already registered";
        } else {
            // Store plain text password (not secure!)
            $stmt = $conn->prepare("INSERT INTO users (Name, Email, Password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $password);
            
            if ($stmt->execute()) {
                // Registration successful, reload the page
                $_SESSION['registration_success'] = "Registration successful! Welcome!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $error = "Registration failed: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Student Registration</title>
    <link rel="stylesheet" href="login.css" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .header {
            width: 100%;
            text-align: center;
            margin-bottom: 30px;
        }
        .canteen {
            color: #2c3e50;
            font-size: 2.5rem;
            margin: 0;
            padding: 20px 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .login-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h3 {
            color: #34495e;
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 600;
        }
        form input {
            width: 100%;
            padding: 14px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        form input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background: #2980b9;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #ecf0f1;
            color: #34495e;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background 0.3s;
        }
        .back-link:hover {
            background: #bdc3c7;
        }
        .error-message {
            background: #ffeaea;
            color: #e74c3c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #e74c3c;
            text-align: left;
        }
        .success-message {
            background: #eafaf1;
            color: #27ae60;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #27ae60;
            text-align: left;
        }
        @media (max-width: 480px) {
            .login-box { padding: 20px; }
            h3 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 class="canteen">MIT Student Canteen</h2>
    </div>
    
    <div class="login-box">
        <h3>Create New Account</h3>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['registration_success'])): ?>
            <div class="success-message"><?php echo $_SESSION['registration_success']; ?></div>
            <?php unset($_SESSION['registration_success']); ?>
        <?php endif; ?>
        
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name (max 20 chars)" required maxlength="20">
            <input type="email" name="email" placeholder="Email Address (max 30 chars)" required maxlength="30">
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit">Register</button>
        </form>
        
        <a href="Student login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>
