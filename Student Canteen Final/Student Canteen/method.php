<?php
session_start();
include('database-connection.php');

// Define total early
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += floatval($item['price']) * intval($item['quantity']);
    }
}

$errorMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $customerName   = trim($_POST['customer_name'] ?? '');
    $customerPhone  = trim($_POST['customer_phone'] ?? '');
    $deliveryMethod = trim($_POST['delivery_method'] ?? '');

    if (empty($customerName) || empty($customerPhone) || empty($deliveryMethod)) {
        $errorMessage = "⚠️ Please fill all required fields!";
    } elseif (empty($_SESSION['cart'])) {
        $errorMessage = "⚠️ Your cart is empty!";
    } else {
        try {
            $paymentMethod = "cash";

            $conn->begin_transaction();

            foreach ($_SESSION['cart'] as $item) {
                $stmt = $conn->prepare("
                    INSERT INTO order_details
                    (ItemID, ItemName, Quantity, Price, PaymentMethod, DeliveryMethod, CustomerName, CustomerPhone)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");

                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }

                $stmt->bind_param(
                    "issidsss",
                    $item['id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price'],
                    $paymentMethod,
                    $deliveryMethod,
                    $customerName,
                    $customerPhone
                );

                if (!$stmt->execute()) {
                    throw new Exception("Insert order item failed: " . $stmt->error);
                }
            }

            $conn->commit();
            $_SESSION['cart'] = [];

            header("Location: success.php");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $errorMessage = "❌ Error processing order: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Payment & Delivery - MIT Canteen</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      background-color:#632024;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 800px;
      margin: 40px auto;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
   .header {
      text-align: left;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 1px solid #050301ff;
   }
    .canteen {
      color: #94e382ff;
      margin-bottom: 10px;
      margin-right :100px;
      font-size: 28px;
    }
    .subtitle {
      color: #72e37bff;
      font-size: 18px;
      
    }
    .cart-summary {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 30px;
    }
    .cart-summary h3 {
      margin-top: 0;
      color: #333;
      border-bottom: 2px solid #6d4949;
      padding-bottom: 10px;
    }
    .cart-item {
      display: flex;
      color: #333;
      justify-content: space-between;
      margin-bottom: 10px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }
    .cart-item:last-child {
      border-bottom: none;
      font-weight: bold;
      color: #333;
      font-size: 18px;
    }
    .form-section {
      margin-bottom: 30px;
    }
    .form-section h3 {
      margin-bottom: 15px;
      color: #333;
      border-bottom: 2px solid #6d4949;
      padding-bottom: 5px;
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
    .form-group input, .form-group select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      box-sizing: border-box;
    }
    .payment-method {
      background-color: #f0f8f0;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      border: 1px solid #c3e6c3;
    }
    .payment-method i {
      font-size: 30px;
      color: #4CAF50;
      margin-right: 10px;
    }
    .payment-method span {
      font-size: 18px;
      font-weight: bold;
      color: #2e7d32;
    }
    .delivery-options {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 10px;
    }
    .delivery-option {
      border: 1px solid #070707ff;
      color: #6d4949;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .delivery-option:hover {
      border-color: #6d4949;
      box-shadow: 0 0 5px rgba(109, 73, 73, 0.3);
    }
    .delivery-option input {
      display: none;
    }
    .delivery-option.selected {
      border-color: #6d4949;
      background-color: #f9f0f0;
    }
    .delivery-option i {
      font-size: 30px;
      color: #6d4949;
      margin-bottom: 10px;
    }
    .submit-btn {
      background-color: #f3f3f3ff;
      color: white;
      padding: 15px 30px;
      border: none;
      border-radius: 5px;
      font-size: 18px;
      cursor: pointer;
      display: block;
      width: 100%;
      margin-top: 20px;
      transition: background-color 0.3s ease;
    }
    .submit-btn:hover {
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
    .back-btn {
      display: inline-block;
      margin-top: 20px;
      color: #6d4949;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
  <div class="container">
    <div class="header">
      <h2 class="canteen">MIT Student Canteen</h2>
      <p class="subtitle">Payment & Delivery Information</p>
    </div>
    
    <?php if (isset($errorMessage)): ?>
      <div class="error"><?= $errorMessage ?></div>
    <?php endif; ?>
    
    <div class="cart-summary">
      <h3>Your Order</h3>
      <?php foreach ($_SESSION['cart'] as $item): ?>
        <div class="cart-item">
          <span><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></span>
          <span><?= number_format($item['price'] * $item['quantity'], 2) ?> kyats</span>
        </div>
      <?php endforeach; ?>
      <div class="cart-item">
        <span>Total</span>
        <span><?= number_format($total, 2) ?> kyats</span>
      </div>
    </div>
    
    <form method="post">
      <!-- Payment Method Section - Fixed to Cash on Delivery -->
      <div class="form-section">
        <h3>Payment Method</h3>
        <div class="payment-method">
          <i class="fas fa-money-bill-wave"></i>
          <span>Cash on Delivery</span>
        </div>
        <input type="hidden" name="payment_method" value="cash">
      </div>
      
      <div class="form-section">
        <h3>Customer Information</h3>
        <div class="form-group">
          <label for="customer_name">Full Name:</label>
          <input type="text" id="customer_name" name="customer_name" required>
        </div>
        <div class="form-group">
          <label for="customer_phone">Phone Number:</label>
          <input type="tel" id="customer_phone" name="customer_phone" required>
        </div>
      </div>
      
      <div class="form-section">
        <h3>Select Delivery Method</h3>
        <div class="delivery-options">
          <div class="delivery-option" onclick="selectDelivery('pickup')">
            <input type="radio" name="delivery_method" value="pickup" id="pickup">
            <i class="fas fa-store"></i>
            <label for="pickup">Pickup at Canteen</label>
          </div>
          <div class="delivery-option" onclick="selectDelivery('classroom')">
            <input type="radio" name="delivery_method" value="classroom" id="classroom">
            <i class="fas fa-school"></i>
            <label for="classroom">Deliver to Classroom</label>
          </div>
          <div class="delivery-option" onclick="selectDelivery('library')">
            <input type="radio" name="delivery_method" value="library" id="library">
            <i class="fas fa-book"></i>
            <label for="library">Deliver to Library</label>
          </div>
          <div class="delivery-option" onclick="selectDelivery('hostel')">
            <input type="radio" name="delivery_method" value="hostel" id="hostel">
            <i class="fas fa-bed"></i>
            <label for="hostel">Deliver to Hostel</label>
          </div>
        </div>
      </div>
      
      <button type="submit" class="submit-btn" href="success.php">Place Order</button>
      
    </form>
    
    <a href="Dessert.php" class="back-btn">← Back to Cart</a>
  </div>
  
  <script>
    function selectDelivery(method) {
      // Remove selected class from all delivery options
      document.querySelectorAll('.delivery-option').forEach(option => {
        option.classList.remove('selected');
      });
      
      // Add selected class to clicked option
      event.currentTarget.classList.add('selected');
      
      // Check the radio button
      document.getElementById(method).checked = true;
    }
  </script>
</body>
</html>