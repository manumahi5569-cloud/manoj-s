<?php
session_start();

// If user not logged in, send to login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <style>
        body{
            font-family: 'Segoe UI', Arial, sans-serif; 
            background:#f4f6f9; 
            display:flex; 
            justify-content:center; 
            align-items:center; 
            height:100vh; 
            margin:0;
        }
        .success-box{
            background:white; 
            padding:50px 40px; 
            text-align:center; 
            border-radius:15px; 
            box-shadow:0 4px 20px rgba(0,0,0,0.1); 
            width:500px;
        }
        .success-icon{
            font-size:70px; 
            color:#28a745;
        }
        .success-box h1{
            color:#28a745; 
            font-size:32px;
            margin:10px 0;
        }
        .success-box p{
            font-size:16px; 
            color:#555;
            line-height:1.6;
        }
        .order-id{
            background:#e9f7ef; 
            padding:10px; 
            border-radius:5px; 
            margin:20px 0; 
            font-weight:bold;
            color:#155724;
        }
        .btn{
            background:#007bff; 
            color:white; 
            padding:14px 25px; 
            text-decoration:none; 
            border-radius:5px; 
            font-weight:bold; 
            display:inline-block; 
            margin:10px 5px;
            transition:0.3s;
        }
        .btn:hover{background:#0056b3;}
        .btn-gray{background:#6c757d;}
        .btn-gray:hover{background:#5a6268;}
    </style>
</head>
<body>
    <div class="success-box">
        <div class="success-icon">✅</div>
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for shopping with us.<br>We will contact you soon on your registered phone number.</p>
        
        <div class="order-id">
            Your Order ID: #<?= 'ORD' . rand(10000,99999); ?>
        </div>

        <a href="../index.php" class="btn">Continue Shopping</a>
        <a href="my_orders.php" class="btn btn-gray">View My Orders</a>
    </div>
</body>
</html>