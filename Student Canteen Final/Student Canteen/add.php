<?php
include('database-connection.php');

$username = 'admin';
$password = 'admin123';
$email = 'admin@mitcanteen.edu';

// Hash the password for security
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert the admin user
$stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed_password, $email);

if ($stmt->execute()) {
    echo "Admin account created successfully!";
} else {
    echo "Error creating admin account: " . $conn->error;
}

// Close connection
$conn->close();
?>