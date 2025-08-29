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
  <title>Snacks - MIT Canteen</title>
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
      <button class="shadow-button" onclick="window.location.href='Snack.php'" style="background-color: #6d4949;"> Snacks</button>
      <button class="shadow-button" onclick="window.location.href='Dessert.php'">Dessert</button>
    </div>
    <div class="menu-container">
      <div class="menu-items">
        <div class="item" data-name="Fried Vegetables" data-price="9000">
          <img src="Traditional Snacks Photo/Fried Vegetables.jpeg" alt="Crispy fried vegetables">
          <h4>Fried Vegetables</h4>
          <p>Price - 9000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="FV01">
            <input type="hidden" name="itemName" value="Fried Vegetables">
            <input type="hidden" name="price" value="9000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Tea Leaf Salad" data-price="5000">
          <img src="Traditional Snacks Photo/Tea Leaf Salad.jpeg" alt="Traditional Myanmar tea leaf salad">
          <h4>Tea Leaf Salad</h4>
          <p>Price - 5000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="TLS01">
            <input type="hidden" name="itemName" value="Tea Leaf Salad">
            <input type="hidden" name="price" value="5000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Mont Lin Mayar" data-price="3000">
          <img src="Traditional Snacks Photo/Mont Lin Mayar.jpeg" alt="Traditional Myanmar snack">
          <h4>Mont Lin Mayar</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MLM01">
            <input type="hidden" name="itemName" value="Mont Lin Mayar">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Shan Snacks" data-price="6000">
          <img src="Traditional Snacks Photo/Shan Snacks.jpeg" alt="Traditional snacks from Shan state">
          <h4>Shan Snacks</h4>
          <p>Price - 6000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="SS01">
            <input type="hidden" name="itemName" value="Shan Snacks">
            <input type="hidden" name="price" value="6000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Mont Let Kaut" data-price="2000">
          <img src="Traditional Snacks Photo/Mont Let Kaut.jpeg" alt="Traditional Myanmar rice cake">
          <h4>Mont Let Kaut</h4>
          <p>Price - 2000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MLK01">
            <input type="hidden" name="itemName" value="Mont Let Kaut">
            <input type="hidden" name="price" value="2000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Fried Tofu" data-price="3500">
          <img src="Traditional Snacks Photo/Fried Tofu.jpeg" alt="Crispy fried tofu cubes">
          <h4>Fried Tofu</h4>
          <p>Price - 3500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="FT01">
            <input type="hidden" name="itemName" value="Fried Tofu">
            <input type="hidden" name="price" value="3500">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Fried Sa Mu Zar" data-price="2000">
          <img src="Traditional Snacks Photo/Fried Sa Mu Zar.jpeg" alt="Traditional Myanmar fried snack">
          <h4>Fried Sa Mu Zar</h4>
          <p>Price - 2000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="FSZ01">
            <input type="hidden" name="itemName" value="Fried Sa Mu Zar">
            <input type="hidden" name="price" value="2000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Mont Si Kyaw" data-price="2000">
          <img src="Traditional Snacks Photo/Mont Si Kyaw.jpeg" alt="Traditional Myanmar sweet snack">
          <h4>Mont Si Kyaw</h4>
          <p>Price - 2000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="MSK01">
            <input type="hidden" name="itemName" value="Mont Si Kyaw">
            <input type="hidden" name="price" value="2000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Fried Banana" data-price="5000">
          <img src="Traditional Snacks Photo/Fried Banana.jpeg" alt="Crispy fried banana slices">
          <h4>Fried Banana</h4>
          <p>Price - 5000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="FB01">
            <input type="hidden" name="itemName" value="Fried Banana">
            <input type="hidden" name="price" value="5000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Kout Nyin Kyi Tout" data-price="3000">
          <img src="Traditional Snacks Photo/Kout Nyin Kyi Tout.jpeg" alt="Traditional Myanmar rice snack">
          <h4>Kout Nyin Kyi Tout</h4>
          <p>Price - 3000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="KNKT01">
            <input type="hidden" name="itemName" value="Kout Nyin Kyi Tout">
            <input type="hidden" name="price" value="3000">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Thingyan Snacks" data-price="2500">
          <img src="Traditional Snacks Photo/Thingyan Snacks.jpeg" alt="Traditional Myanmar festival snacks">
          <h4>Thingyan Snacks</h4>
          <p>Price - 2500 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="TS01">
            <input type="hidden" name="itemName" value="Thingyan Snacks">
            <input type="hidden" name="price" value="2500">
            <button type="submit" name="add_item"><b>Add</b></button>
          </form>
        </div>
        <div class="item" data-name="Halwa" data-price="2000">
          <img src="Traditional Snacks Photo/Halwa.jpeg" alt="Sweet semolina dessert">
          <h4>Halwa</h4>
          <p>Price - 2000 kyats</p>
          <form method="post">
            <input type="hidden" name="itemID" value="HL01">
            <input type="hidden" name="itemName" value="Halwa">
            <input type="hidden" name="price" value="2000">
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