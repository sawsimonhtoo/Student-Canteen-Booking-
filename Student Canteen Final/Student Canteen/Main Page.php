<?php
session_start();
include('database-connection.php');

// Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $itemID = $_POST['itemID'];
    $itemName = $_POST['itemName'];
    $price = (float)$_POST['price']; // Convert to float for calculations
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if item already exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $itemID) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $itemID,
            'name' => $itemName,
            'price' => $price, // Store as float
            'quantity' => 1
        ];
    }
    
    // Redirect to prevent form resubmission on refresh
    header("Location: " . basename(__FILE__));
    exit();
}

// Handle Remove from Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_item'])) {
    $itemID = $_POST['itemID'];
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $itemID) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        // Re-index the array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    
    // Redirect to prevent form resubmission on refresh
    header("Location: " . basename(__FILE__));
    exit();
}

// Handle Order Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_order'])) {
    if (!empty($_SESSION['cart'])) {
        try {
            $conn->begin_transaction();
            
            foreach ($_SESSION['cart'] as $item) {
                $stmt = $conn->prepare("INSERT INTO orders (ItemID, ItemName, Price, Quantity) VALUES (?, ?, ?, ?)");
                if ($stmt === false) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("ssdi", $item['id'], $item['name'], $item['price'], $item['quantity']);
                if (!$stmt->execute()) {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
            }
            
            $conn->commit();
            $_SESSION['cart'] = [];
            $_SESSION['successMessage'] = "Order submitted successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['errorMessage'] = "Error submitting order: " . $e->getMessage();
        }
    } else {
        $_SESSION['errorMessage'] = "Your cart is empty!";
    }
    
    // Redirect to prevent form resubmission on refresh
    header("Location: " . basename(__FILE__));
    exit();
}

// Check for session messages
$successMessage = isset($_SESSION['successMessage']) ? $_SESSION['successMessage'] : '';
$errorMessage = isset($_SESSION['errorMessage']) ? $_SESSION['errorMessage'] : '';

// Clear session messages
unset($_SESSION['successMessage']);
unset($_SESSION['errorMessage']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Main Course - MIT Canteen</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  
  <div class="container">
    <h2 class="canteen">MIT Student Canteen</h2>
    <?php if (!empty($successMessage)): ?>
      <div class="message success"><?= $successMessage ?></div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
      <div class="message error"><?= $errorMessage ?></div>
    <?php endif; ?>
    <div class="menu-buttons">
      <button class="shadow-button" onclick="window.location.href='Main Page.php'" style="background-color: #6d4949;">Main-Course</button>
      <button class="shadow-button" onclick="window.location.href='Beverages.php'">Beverages</button>
      <button class="shadow-button" onclick="window.location.href='Snack.php'">Snacks</button>
      <button class="shadow-button" onclick="window.location.href='Dessert.php'">Dessert</button>
    </div>
    <div class="menu-container">
      <div class="menu-items">
        <!-- Item 1 -->
        <div class="item">
          <img src="Main Course Page Photo/Mohinga.jpeg" alt="Traditional Myanmar rice noodle dish with fish soup">
          <h4>Mong Hin Kha</h4>
          <p>Price - 5000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MHK01">
            <input type="hidden" name="itemName" value="Mong Hin Kha">
            <input type="hidden" name="price" value="5000">
            <button type="submit" name="add_item">Add</button>
          </form>
        </div>
        <!-- Item 2 -->
        <div class="item">
          <img src="Main Course Page Photo/Nan Gyi Thoke.jpeg" alt="Burmese thick rice noodle salad">
          <h4>Nan Gyi Thoke</h4>
          <p>Price - 4000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="NGT01">
            <input type="hidden" name="itemName" value="Nan Gyi Thoke">
            <input type="hidden" name="price" value="4000">
            <button type="submit" name="add_item">Add</button>
          </form>
        </div>
        <!-- Item 3 -->
        <div class="item">
          <img src="Main Course Page Photo/Warm Tofu Noodles.jpeg" alt="Noodles with tofu and vegetables">
          <h4>Tofu Noodles</h4>
          <p>Price - 6000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="TFN01">
            <input type="hidden" name="itemName" value="Tofu Noodles">
            <input type="hidden" name="price" value="6000">
            <button type="submit" name="add_item">Add</button>
          </form>
        </div>
        <!-- Item 4 -->
        <div class="item">
          <img src="Main Course Page Photo/Shan Noodles.jpeg" alt="Traditional noodles from Shan state">
          <h4>Shan Noodles</h4>
          <p>Price - 6000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="SHN01">
            <input type="hidden" name="itemName" value="Shan Noodles">
            <input type="hidden" name="price" value="6000">
            <button type="submit" name="add_item">Add</button>
          </form>
        </div>
        <!-- Item 5 -->
        <div class="item">
          <img src="Main Course Page Photo/Green tea leaf Rice.jpeg" alt="Rice mixed with green tea leaves">
          <h4>Green Tea Rice</h4>
          <p>Price - 3500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="GTR01">
            <input type="hidden" name="itemName" value="Green Tea Rice">
            <input type="hidden" name="price" value="3500">
            <button type="submit" name="add_item">Add</button>
          </form>
        </div>
        <!-- Item 6 -->
        <div class="item">
          <img src="Main Course Page Photo/Seafood Fried Rice.jpeg" alt="Fried rice with mixed seafood">
          <h4>Seafood Fried Rice</h4>
          <p>Price - 7000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="SFR01">
            <input type="hidden" name="itemName" value="Seafood Fried Rice">
            <input type="hidden" name="price" value="7000">
            <button type="submit" name="add_item">Add</button>
          </form>
        </div>
        <!-- Item 7 -->
        <div class="item">
          <img src="Main Course Page Photo/Kyay Oh.jpeg" alt="Burmese noodle soup with pork">
          <h4>Kyay Oh</h4>
          <p>Price - 6000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="KYO01">
            <input type="hidden" name="itemName" value="Kyay Oh">
            <input type="hidden" name="price" value="6000">
            <button type="submit" name="add_item">Add</button>
          </form>
        </div>
        <!-- Item 8 -->
        <div class="item">
          <img src="Main Course Page Photo/Mala Xiang Guo.jpeg" alt="Spicy Chinese stir fry">
          <h4>Mala Xiang Guo</h4>
          <p>Price - 10000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MLX01">
            <input type="hidden" name="itemName" value="Mala Xiang Guo">
            <input type="hidden" name="price" value="10000">
            <button type="submit" name="add_item">Add</button>
          </form>
        </div>
      </div>
      <div class="cart">
        <h3 class="center">Cart</h3>
        <ul>
          <?php if (!empty($_SESSION['cart'])): ?>
            <?php 
            $total = 0; // Initialize total
            foreach ($_SESSION['cart'] as $item): 
              $itemTotal = $item['price'] * $item['quantity'];
              $total += $itemTotal;
            ?>
              <li>
                <?= htmlspecialchars($item['name']) ?> 
                <span>x <?= $item['quantity'] ?> = <?= number_format($itemTotal, 2) ?> kyats</span>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="itemID" value="<?= htmlspecialchars($item['id']) ?>">
                    <button type="submit" name="remove_item" style="color: red; background: none; border: none; cursor: pointer;">Remove</button>
                </form>
              </li>
            <?php endforeach; ?>
            <li style="font-weight: bold; margin-top: 10px;">
              Total: <?= number_format($total, 2) ?> kyats
            </li>
          <?php else: ?>
            <li>No items in cart</li>
          <?php endif; ?>
        </ul>
        
        <?php if (!empty($_SESSION['cart'])): ?>
  <form method="post" action="method.php">
    <button type="submit" class="submit">Submit order</button>
  </form>
<?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>