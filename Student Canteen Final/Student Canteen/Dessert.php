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
  <title>Dessert - MIT Canteen</title>
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
      <button class="shadow-button" onclick="window.location.href='Beverages.php'">Beverages</button>
      <button class="shadow-button" onclick="window.location.href='Snack.php'"> Snacks</button>
      <button class="shadow-button" onclick="window.location.href='Dessert.php'" style="background-color: #6d4949;">Dessert</button>
    </div>
    <div class="menu-container">
      <div class="menu-items">
        <div class="item" data-name="Shwe Yin Aye" data-price="2500">
          <img src="Dessert Photo/Shwe Yin Aye.jpeg" alt="Traditional Myanmar sweet dessert">
          <h4>Shwe Yin Aye</h4>
          <p>Price - 2500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="SYA01">
            <input type="hidden" name="itemName" value="Shwe Yin Aye">
            <input type="hidden" name="price" value="2500">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Mont lat saung" data-price="4000">
          <img src="Dessert Photo/Mont lat saung.jpeg" alt="Traditional Myanmar coconut dessert">
          <h4>Mont lat saung</h4>
          <p>Price - 4000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MLS01">
            <input type="hidden" name="itemName" value="Mont lat saung">
            <input type="hidden" name="price" value="4000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="mango" data-price="2000">
          <img src="Dessert Photo/mango.jpeg" alt="Fresh sliced mango">
          <h4>Mango</h4>
          <p>Price - 2000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MG01">
            <input type="hidden" name="itemName" value="Mango">
            <input type="hidden" name="price" value="2000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Strawberrys" data-price="3000">
          <img src="Dessert Photo/Strawberry.jpeg" alt="Fresh strawberries">
          <h4>Strawberry</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="SB01">
            <input type="hidden" name="itemName" value="Strawberry">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Dragon Fruit" data-price="2000">
          <img src="Dessert Photo/Dragon Fruit.jpeg" alt="Fresh dragon fruit slices">
          <h4>Dragon Fruit</h4>
          <p>Price - 2000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="DF01">
            <input type="hidden" name="itemName" value="Dragon Fruit">
            <input type="hidden" name="price" value="2000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Orange" data-price="2000">
          <img src="Dessert Photo/Orange.jpeg" alt="Fresh orange segments">
          <h4>Orange</h4>
          <p>Price - 2000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="OR01">
            <input type="hidden" name="itemName" value="Orange">
            <input type="hidden" name="price" value="2000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Triple Chocolate Cake" data-price="4000">
          <img src="Dessert Photo/Triple Chocolate Cake.jpeg" alt="Rich chocolate cake">
          <h4>Triple Chocolate Cake</h4>
          <p>Price - 4000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="TCC01">
            <input type="hidden" name="itemName" value="Triple Chocolate Cake">
            <input type="hidden" name="price" value="4000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Strawberry Ice Cream" data-price="3000">
          <img src="Dessert Photo/Strawberry Ice Cream.jpeg" alt="Creamy strawberry ice cream">
          <h4>Strawberry Ice Cream</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="SIC01">
            <input type="hidden" name="itemName" value="Strawberry Ice Cream">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Chocolate Sundae" data-price="2000">
          <img src="Dessert Photo/Chocolate Sundae.jpeg" alt="Delicious chocolate sundae">
          <h4>Chocolate Sundae</h4>
          <p>Price - 2000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="CS01">
            <input type="hidden" name="itemName" value="Chocolate Sundae">
            <input type="hidden" name="price" value="2000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Mini Lemon Cheesecakes" data-price="3000">
          <img src="Dessert Photo/Mini Lemon Cheesecakes.jpeg" alt="Tangy lemon cheesecakes">
          <h4>Mini Lemon Cheesecakes</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MLC01">
            <input type="hidden" name="itemName" value="Mini Lemon Cheesecakes">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Cocnut Jelly" data-price="3500">
          <img src="Dessert Photo/Cocnut Jelly.jpeg" alt="Sweet coconut jelly dessert">
          <h4>Coconut Jelly</h4>
          <p>Price - 3500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="CJ01">
            <input type="hidden" name="itemName" value="Coconut Jelly">
            <input type="hidden" name="price" value="3500">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Cake Ball" data-price="2500">
          <img src="Dessert Photo/Cake Ball.jpeg" alt="Sweet cake balls">
          <h4>Cake Ball</h4>
          <p>Price - 2500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="CB01">
            <input type="hidden" name="itemName" value="Cake Ball">
            <input type="hidden" name="price" value="2500">
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