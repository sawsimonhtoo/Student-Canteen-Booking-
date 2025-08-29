<?php
session_start();
include('database-connection.php');

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Get admin user from database
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        // Verify password (in a real app, use password_hash and password_verify)
        if ($password === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: adminpannel.php");
            exit();
        }
    }
    
    $login_error = "Invalid username or password";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login - MIT Canteen</title>
  <style>
    body {
      background-color: #f5f5f5;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }
    .login-container {
      background-color: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .canteen {
      color: #6d4949;
      margin-bottom: 10px;
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
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      box-sizing: border-box;
    }
    .login-btn {
      background-color: #6d4949;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }
    .login-btn:hover {
      background-color: #5a3c3c;
    }
    .error {
      color: #d9534f;
      background-color: #f9f2f2;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h2 class="canteen">MIT Canteen</h2>
      <h3>Admin Login</h3>
    </div>
    
    <?php if (isset($login_error)): ?>
      <div class="error"><?= $login_error ?></div>
    <?php endif; ?>
    
    <form method="post">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="login-btn">Login</button>
    </form>
  </div>
</body>
</html>