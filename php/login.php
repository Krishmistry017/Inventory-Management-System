<?php
sessierror_reporting(E_ALL);
ini_set('display_errors', 1);on_start();    

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'your_username');
define('DB_PASSWORD', 'your_password');
define('DB_NAME', 'your_database');

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username = $_POST['username'];
$password = $_POST['password'];

// Prepare and execute query
$stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify password (in production, use password_verify() with hashed passwords)
    if ($password === $user['password']) {
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    }
}

// If login fails
header("Location: index.html?error=1");
exit;
?>