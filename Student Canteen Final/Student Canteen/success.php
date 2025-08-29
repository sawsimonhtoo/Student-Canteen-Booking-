
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Order Successful - MIT Canteen</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      background-color: #f5f5f5;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 800px;
      margin: 40px auto;
      padding: 30px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
    }
    .success-icon {
      font-size: 80px;
      color: #4CAF50;
      margin-bottom: 20px;
    }
    .success-title {
      color: #333;
      font-size: 32px;
      margin-bottom: 15px;
    }
    .order-info {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 8px;
      margin: 30px 0;
      text-align: left;
    }
    .order-info h3 {
      margin-top: 0;
      color: #6d4949;
      border-bottom: 2px solid #6d4949;
      padding-bottom: 10px;
    }
    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }
    .info-row:last-child {
      border-bottom: none;
      font-weight: bold;
      font-size: 18px;
    }
    .info-label {
      font-weight: bold;
      color: #555;
    }
    .home-btn {
      display: inline-block;
      background-color: #6d4949;
      color: white;
      padding: 15px 30px;
      border-radius: 5px;
      font-size: 18px;
      text-decoration: none;
      margin-top: 20px;
      transition: background-color 0.3s ease;
    }
    .home-btn:hover {
      background-color: #5a3c3c;
    }
    .footer-note {
      margin-top: 30px;
      color: #666;
      font-size: 14px;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
  <div class="container">
    <i class="fas fa-check-circle success-icon"></i>
    <h1 class="success-title">Order Successful!</h1>
    <p>Thank you for your order. We've received it and will start preparing your food shortly.</p>
    
    
    
    <a href="open.php" class="home-btn">Back to Menu</a>
    
    
  </div>
</body>
</html>