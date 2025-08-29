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
  <title>Beverages - MIT Canteen</title>
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
      <button class="shadow-button" onclick="window.location.href='Main Page.php'">Main-Course</button>
      <button class="shadow-button" onclick="window.location.href='Beverages.php'" style="background-color: #6d4949;">Beverages</button>
      <button class="shadow-button" onclick="window.location.href='Snack.php'"> Snacks</button>
      <button class="shadow-button" onclick="window.location.href='Dessert.php'">Dessert</button>
    </div>
    <div class="menu-container">
      <div class="menu-items">
        <div class="item" data-name="Strawberry" data-price="3000">
          <img src="Beverages Page Photo/Strawberry Juice.jpeg" alt="Fresh strawberry juice">
          <h4>Strawberry</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="SJ01">
            <input type="hidden" name="itemName" value="Strawberry">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Orange Juice" data-price="3000">
          <img src="Beverages Page Photo/Orange Juice.jpeg" alt="Fresh orange juice">
          <h4>Orange Juice</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="OJ01">
            <input type="hidden" name="itemName" value="Orange Juice">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Mango Juice" data-price="3000">
          <img src="Beverages Page Photo/Mango Juice.jpeg" alt="Fresh mango juice">
          <h4>Mango Juice</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MJ01">
            <input type="hidden" name="itemName" value="Mango Juice">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Lemon Juice" data-price="3000">
          <img src="Beverages Page Photo/Lemon Juice.jpeg" alt="Fresh lemon juice">
          <h4>Lemon Juice</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="LJ01">
            <input type="hidden" name="itemName" value="Lemon Juice">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Iced Coffee" data-price="2500">
          <img src="Beverages Page Photo/Iced Coffee.jpeg" alt="Chilled iced coffee">
          <h4>Iced Coffee</h4>
          <p>Price - 2500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="IC01">
            <input type="hidden" name="itemName" value="Iced Coffee">
            <input type="hidden" name="price" value="2500">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Watermelon Juice" data-price="3000">
          <img src="Beverages Page Photo/Watermelon Juice.jpeg" alt="Fresh watermelon juice">
          <h4>Watermelon Juice</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="WJ01">
            <input type="hidden" name="itemName" value="Watermelon Juice">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Avocado Juice" data-price="4500">
          <img src="Beverages Page Photo/Avocado Juice.jpeg" alt="Creamy avocado juice">
          <h4>Avocado Juice</h4>
          <p>Price - 4500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="AJ01">
            <input type="hidden" name="itemName" value="Avocado Juice">
            <input type="hidden" name="price" value="4500">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Berry Bliss Smoothie" data-price="5000">
          <img src="Beverages Page Photo/Berry Bliss Smoothie.jpeg" alt="Mixed berry smoothie">
          <h4>Berry Bliss Smoothie</h4>
          <p>Price - 5000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="BBS01">
            <input type="hidden" name="itemName" value="Berry Bliss Smoothie">
            <input type="hidden" name="price" value="5000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Papaya Juice" data-price="3500">
          <img src="Beverages Page Photo/Papaya Juice.jpeg" alt="Fresh papaya juice">
          <h4>Papaya Juice</h4>
          <p>Price - 3500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="PJ01">
            <input type="hidden" name="itemName" value="Papaya Juice">
            <input type="hidden" name="price" value="3500">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Passion Fruit Juice" data-price="3000">
          <img src="Beverages Page Photo/Passion Fruit Juice.jpeg" alt="Tropical passion fruit juice">
          <h4>Passion Fruit Juice</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="PFJ01">
            <input type="hidden" name="itemName" value="Passion Fruit Juice">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Pineapple Juice" data-price="4500">
          <img src="Beverages Page Photo/Pineapple Juice.jpeg" alt="Fresh pineapple juice">
          <h4>Pineapple Juice</h4>
          <p>Price - 4500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="PNJ01">
            <input type="hidden" name="itemName" value="Pineapple Juice">
            <input type="hidden" name="price" value="4500">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Grape Juice" data-price="3000">
          <img src="Beverages Page Photo/Grape Juice.jpeg" alt="Sweet grape juice">
          <h4>Grape Juice</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="GJ01">
            <input type="hidden" name="itemName" value="Grape Juice">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
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