<?php
session_start();
include('database-connection.php'); // Make sure this file defines $pdo

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: adminlogin.php');
    exit;
}

// Fetch all recent orders (latest 20)
try {
    $orders = $pdo->query("
        SELECT OrderID, CustomerName, ItemName, Quantity, Price, PaymentMethod, DeliveryMethod
        FROM order_details
        ORDER BY DetailID DESC
        LIMIT 20
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("❌ Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Management</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { background-color: #f8f9fa; color: #333; }
    .navbar { background-color: #343a40; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
    .navbar-brand { color: white; font-size: 1.5rem; font-weight: bold; text-decoration: none; }
    .navbar-nav { display: flex; list-style: none; gap: 1.5rem; }
    .nav-link { color: #adb5bd; text-decoration: none; padding: 0.5rem 0; transition: color 0.3s; }
    .nav-link:hover, .nav-link.active { color: white; }
    .logout-btn { background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; text-decoration: none; }
    .logout-btn:hover { background: #c82333; }
    .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
    .page-title { font-size: 2rem; margin-bottom: 1.5rem; color: #343a40; }
    .card { background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 2rem; }
    .card-header { background: #f8f9fa; padding: 1rem 1.5rem; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-size: 1.25rem; color: #343a40; }
    .card-body { padding: 1.5rem; }
    .table { width: 100%; border-collapse: collapse; }
    .table th, .table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #dee2e6; }
    .table th { background-color: #f8f9fa; font-weight: 600; }
    .table tbody tr:hover { background-color: #f1f3f5; }
    .btn { display: inline-block; padding: 0.5rem 1rem; background: #007bff; color: white; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; transition: background 0.3s; }
    .btn:hover { background: #0069d9; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    .btn-info { background: #17a2b8; }
    .btn-info:hover { background: #138496; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #c82333; }
</style>
</head>
<body>
<nav class="navbar">
    <div style="display: flex; align-items: center;">
        <a href="index.php" class="navbar-brand">Admin Panel</a>
        <ul class="navbar-nav">
            <li><a href="adminpannel.php" class="nav-link">Dashboard</a></li>
            <li><a href="adminuser.php" class="nav-link">Users</a></li>
            <li><a href="adminorder.php" class="nav-link active">Orders</a></li>
        </ul>
    </div>
    <a href="open.php?action=logout" class="logout-btn">Logout</a>
</nav>

<div class="container">
    <h1 class="page-title">Order Management</h1>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">All Orders</h3>
            <button class="btn">Add New Order</button>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Delivery</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['OrderID']) ?></td>
                            <td><?= htmlspecialchars($order['CustomerName']) ?></td>
                            <td><?= htmlspecialchars($order['ItemName']) ?></td>
                            <td><?= $order['Quantity'] ?></td>
                            <td><?= number_format($order['Price'],2) ?> MMK</td>
                            <td><?= number_format($order['Price'] * $order['Quantity'],2) ?> MMK</td>
                            <td><?= htmlspecialchars($order['PaymentMethod']) ?></td>
                            <td><?= htmlspecialchars($order['DeliveryMethod']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-info">Edit</button>
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" style="text-align:center;">No orders yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
