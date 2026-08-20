<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// 1. Get cart items from DB
$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(empty($cart_items)){
    header("Location: cart.php");
    exit;
}

// 2. Calculate total
$total = 0;
foreach($cart_items as $item){
    $total += $item['price'] * $item['quantity'];
}

// 3. Place Order
if(isset($_POST['place_order'])){
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    try {
        // Start transaction
        $conn->beginTransaction();

        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, name, phone, address, total_amount, order_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user_id, $name, $phone, $address, $total]);
        $order_id = $conn->lastInsertId();
        
        // Insert each item into order_items table
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?)");
        foreach($cart_items as $item){
            $stmt_item->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
        }
        
        // Clear cart after order
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Commit and redirect to success page
        $conn->commit();
        header("Location: order_success.php");
        exit();
        
    } catch(Exception $e){
        $conn->rollBack();
        $error = "Error placing order: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Online Store</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin:0; background:#f4f6f9; }
        .header { background:#003366; color:white; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; }
        .header a { color:white; text-decoration:none; font-weight:bold; }
        .container { width:90%; max-width:700px; margin:30px auto; background:white; padding:30px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align:center; color:#003366; margin-bottom:20px; }
        .order-summary { background:#e9f7ef; padding:15px; border-radius:8px; margin-bottom:20px; }
        .total { font-size:24px; color:#28a745; text-align:center; font-weight:bold; }
        .form-group { margin-bottom:15px; }
        label { font-weight:bold; display:block; margin-bottom:5px; color:#333; }
        input, textarea { width:100%; padding:12px; border:1px solid #ddd; border-radius:5px; font-size:14px; box-sizing:border-box; }
        button { background:#28a745; color:white; padding:15px; border:none; width:100%; border-radius:5px; font-size:16px; font-weight:bold; cursor:pointer; margin-top:10px; }
        button:hover { background:#218838; }
        .error { background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center; }
        .back-link { display:block; text-align:center; margin-top:15px; color:#007bff; text-decoration:none; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Checkout</h2>
        <a href="cart.php">← Back to Cart</a>
    </div>

    <div class="container">
        <h2>Delivery Details</h2>

        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach($cart_items as $item): ?>
                <p><?= $item['name']; ?> x <?= $item['quantity']; ?> = ₹<?= number_format($item['price'] * $item['quantity'],2); ?></p>
            <?php endforeach; ?>
            <hr>
            <p class="total">Total Amount: ₹<?= number_format($total,2); ?></p>
        </div>

        <form method="POST">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number *</label>
                <input type="text" name="phone" required pattern="[0-9]{10}" title="Enter 10 digit number">
            </div>
            
            <div class="form-group">
                <label>Delivery Address *</label>
                <textarea name="address" rows="4" required></textarea>
            </div>
            
            <button type="submit" name="place_order">Place Order</button>
        </form>

        <a href="cart.php" class="back-link">← Back to Cart</a>
    </div>

</body>
</html>