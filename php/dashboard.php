<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.html");
    exit;
}

// Database connection
require 'db_connect.php';

// Get user information
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="page">
    <div class="sidebar">
      <h2>Inventory App</h2>
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="items.php">Items</a>
      <a href="incoming.php">Incoming Items</a>
      <a href="outgoing.php">Outgoing Items</a>
      <a href="reports.php">Reports</a>
      <a href="logout.php">Logout</a>
    </div>

    <main class="main-content">
      <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1>
      <div class="dashboard-stats">
        <!-- Your dashboard content here -->
      </div>
    </main>
  </div>
</body>
</html>