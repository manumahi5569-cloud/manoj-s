<?php
session_start();
include 'includes/db.php'; // PDO connection file

// Get category_id from URL
$category_id = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch products based on category
if ($category_id != '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = :category_id ORDER BY id DESC");
    $stmt->execute(['category_id' => $category_id]);
} else {
    $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Logout function
if(isset($_POST['logout'])){
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store - Shop Everything</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; margin:0; background:#f4f6f9; color:#333; }
        
        .header-container { 
            display:flex; justify-content:space-between; align-items:center; 
            padding:15px 30px; background:#003366; color:white; 
            position:sticky; top:0; z-index:100;
        }
        .header-container h1 { margin:0; font-size:24px; }
        .header-container nav a { color:white; text-decoration:none; margin:0 12px; font-weight:bold; }
        .header-container nav a:hover { text-decoration:underline; }
        
        .logout-button { background:#dc3545; color:white; border:none; padding:8px 15px; border-radius:5px; cursor:pointer; font-weight:bold; }
        .logout-button:hover { background:#c82333; }
        
        .cart-icon { width:22px; vertical-align:middle; margin-right:5px; }
        
        .category-bar { text-align:center; margin:25px 0; padding:10px; }
        .cat-btn { 
            padding:12px 22px; margin:6px; background:#007bff; color:white; 
            text-decoration:none; border-radius:25px; font-weight:bold; 
            display:inline-block; transition:0.3s;
        }
        .cat-btn:hover { background:#0056b3; transform:translateY(-2px); }
        .cat-btn.active { background:#28a745; }
        
        .product-list { display:flex; flex-wrap:wrap; gap:25px; justify-content:center; padding:20px; }
        
        .product { 
            background:white; border:1px solid #e0e0e0; padding:15px; 
            width:260px; text-align:center; border-radius:10px; 
            box-shadow:0 2px 8px rgba(0,0,0,0.1); transition:0.3s;
        }
        .product:hover { transform:translateY(-5px); box-shadow:0 6px 15px rgba(0,0,0,0.2); }
        
        .product img { width:200px; height:200px; object-fit:cover; border-radius:8px; margin-bottom:10px; }
        .product h3 { margin:10px 0; font-size:18px; color:#003366; }
        .product p { margin:8px 0; font-size:14px; }
        .product .price { font-size:20px; color:#28a745; font-weight:bold; }
        
        .add-to-cart-button { 
            background:#28a745; color:white; border:none; padding:12px 15px; 
            border-radius:5px; cursor:pointer; width:100%; font-weight:bold; 
            margin-top:10px; font-size:15px; transition:0.3s;
        }
        .add-to-cart-button:hover { background:#218838; }
        
        .no-products { text-align:center; font-size:18px; width:100%; padding:40px; color:#777; }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <h1>🛒 Our Online Store</h1>
            <nav>
                <a href="pages/login.php">LOGIN</a>
                <a href="pages/register.php">REGISTER</a>
                <a href="pages/cart.php">
                    <img src="images/cart-icon.png" alt="Cart" class="cart-icon">CART
                </a>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="logout-button">LOGOUT</button>
                </form>
            </nav>
        </div>
    </header>

    <div class="main-container">
        <!-- 5 Category Buttons -->
        <div class="category-bar">
            <a href="index.php" class="cat-btn <?= $category_id=='' ? 'active' : '' ?>">All Products</a>
            <a href="index.php?category=1" class="cat-btn <?= $category_id=='1' ? 'active' : '' ?>">Pens</a>
            <a href="index.php?category=2" class="cat-btn <?= $category_id=='2' ? 'active' : '' ?>">Notebooks</a>
            <a href="index.php?category=3" class="cat-btn <?= $category_id=='3' ? 'active' : '' ?>">Bags</a>
            <a href="index.php?category=4" class="cat-btn <?= $category_id=='4' ? 'active' : '' ?>">Phones</a>
            <a href="index.php?category=5" class="cat-btn <?= $category_id=='5' ? 'active' : '' ?>">Watches</a>
        </div>

        <main>
            <h2 style="text-align:center; margin-bottom:25px; font-size:28px;">
                <?php 
                $titles = [1=>'Pens', 2=>'Notebooks', 3=>'Bags', 4=>'Phones', 5=>'Watches'];
                echo isset($titles[$category_id]) ? $titles[$category_id] : "All Products";
                ?>
            </h2>
            
            <div class="product-list">
                <?php if (empty($products)) : ?>
                    <p class="no-products">No products found in this category.</p>
                <?php else : ?>
                    <?php foreach ($products as $product) : ?>
                        <div class="product">
                            <?php if (!empty($product['image'])) : ?>
                                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <img src="images/no-image.jpg" alt="No Image">
                            <?php endif; ?>
                            
                            <h3><?= htmlspecialchars($product['name']); ?></h3>
                            <p class="price">₹<?= number_format($product['price'], 2); ?></p>
                            <p><?= htmlspecialchars($product['description']); ?></p>
                            
                            <form method="POST" action="pages/cart.php">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>