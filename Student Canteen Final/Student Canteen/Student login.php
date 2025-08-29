<?php
session_start();

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'canteen_db';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login when form is submitted
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Email and password are required";
    } else {
        $stmt = $conn->prepare("SELECT UserID, Name, Password FROM users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Plain-text password check
            if ($password === $user['Password']) {
                $message = "Login successful! Welcome, " . $user['Name'];
            } else {
                $message = "Invalid password";
            }
        } else {
            $message = "Email not found";
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
  <title>Login</title>
  <link rel="stylesheet" href="login.css" />
  <style>
    .additional-links {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      gap: 15px;
    }
    .additional-links a {
      display: inline-block;
      padding: 8px 15px;
      background-color: #f0f0f0;
      color: #333;
      text-decoration: none;
      border-radius: 4px;
      transition: background-color 0.3s;
      flex: 1;
      max-width: 150px;
      text-align: center;
    }
    .additional-links a:hover {
      background-color: #e0e0e0;
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
    .error-message {
      background: #fdecea;
      color: #e74c3c;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      border-left: 4px solid #e74c3c;
      text-align: left;
    }
    
    /* Button styling */
    .login-btn {
      width: 100%;
      padding: 15px 20px;
      font-size: 16px;
      font-weight: bold;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 10px;
    }
    
    .login-btn:hover {
      background-color: #2980b9;
    }
    
    .back-btn {
      width: 100%;
      padding: 10px 15px;
      font-size: 14px;
      background-color: #ecf0f1;
      color: #34495e;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 15px;
    }
    
    .back-btn:hover {
      background-color: #bdc3c7;
    }
    
    /* Input field styling */
    .login-box input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      box-sizing: border-box;
    }
    
    .login-box input:focus {
      border-color: #3498db;
      outline: none;
      box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
    }
  </style>
</head>
<body>
  <div class="header">
    <h2 class="canteen">MIT Student Canteen</h2>
  </div>
  <div class="login-box">
    <h3>User Login</h3>
    <?php if (isset($_SESSION['registration_success'])): ?>
      <div class="success-message"><?php echo $_SESSION['registration_success']; ?></div>
      <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <input type="email" name="email" placeholder="user@gmail.com" required /><br>
      <input type="password" name="password" placeholder="Password" required /><br>
      <button type="submit" class="login-btn" onclick="window.location.href='Main Page.php'">Login</button>
    </form>
    <div class="additional-links">
      <a href="adminlogin.php">Admin Login</a>
      <a href="register.php">Register New Account</a>
    </div>
    <button class="back-btn" onclick="window.location.href='open.php'">Back</button>
  </div>
</body>
</html>