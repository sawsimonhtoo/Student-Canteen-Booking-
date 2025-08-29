<?php
session_start();
include('database-connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: adminlogin.php');
    exit;
}

// Get dashboard stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM order_details")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(Price * Quantity) FROM order_details")->fetchColumn() ?: 0;

// Get recent orders
$stmt = $pdo->query("
    SELECT o.*, u.Name 
    FROM order_details o 
    LEFT JOIN users u ON o.UserID = u.UserID 
    ORDER BY o.OrderID DESC 
    LIMIT 5
");
$recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f8f9fa; color: #333; }
        .navbar { background-color: #343a40; padding: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { color: white; font-size: 1.5rem; font-weight: bold; text-decoration: none; }
        .navbar-nav { display: flex; list-style: none; gap: 1.5rem; }
        .nav-link { color: #adb5bd; text-decoration: none; padding: 0.5rem 0; transition: color 0.3s; }
        .nav-link:hover, .nav-link.active { color: white; }
        .logout-btn { background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .logout-btn:hover { background: #c82333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .page-title { font-size: 2rem; margin-bottom: 1.5rem; color: #343a40; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card.primary { border-left: 5px solid #007bff; }
        .stat-card.success { border-left: 5px solid #28a745; }
        .stat-card.info { border-left: 5px solid #17a2b8; }
        .stat-title { font-size: 1rem; color: #6c757d; margin-bottom: 0.5rem; }
        .stat-value { font-size: 2.5rem; font-weight: 300; }
        .card { background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header { background: #f8f9fa; padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6; }
        .card-title { font-size: 1.25rem; color: #343a40; }
        .card-body { padding: 1.5rem; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #dee2e6; }
        .table th { background-color: #f8f9fa; font-weight: 600; }
        .table tbody tr:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div style="display: flex; align-items: center;">
            <a href="index.php" class="navbar-brand">Admin Panel</a>
            <ul class="navbar-nav">
                <li><a href="adminpannel.php" class="nav-link">Dashboard</a></li>
                <li><a href="adminuser.php" class="nav-link active">Users</a></li>
                <li><a href="adminorder.php" class="nav-link">Orders</a></li>
            </ul>
        </div>
        <a href="open.php?action=logout" class="logout-btn">Logout</a>
    </nav>

    <div class="container">
        <h1 class="page-title">Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-title">Total Users</div>
                <div class="stat-value"><?php echo $totalUsers; ?></div>
            </div>
            <div class="stat-card success">
                <div class="stat-title">Total Orders</div>
                <div class="stat-value"><?php echo $totalOrders; ?></div>
            </div>
            <div class="stat-card info">
                <div class="stat-title">Total Revenue</div>
                <div class="stat-value">$<?php echo number_format($totalRevenue, 2); ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Orders</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?php echo $order['OrderID']; ?></td>
                            <td><?php echo $order['CustomerName'] ?: $order['Name']; ?></td>
                            <td><?php echo $order['ItemName']; ?></td>
                            <td><?php echo $order['Quantity']; ?></td>
                            <td>$<?php echo number_format($order['Price'] * $order['Quantity'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>