<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/functions.php';
requireLogin();
requireStudent();

$db = createDb()->getConnection();
$orderModel = new Order($db);
$userId = (int) $_SESSION['user_id'];
$orders = $orderModel->getOrdersByUser($userId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>UniEats Orders</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Your Orders</h1>
            <nav>
                <a href="menu.php">Menu</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <main>
            <?php if (empty($orders)): ?>
                <p>No orders yet. <a href="menu.php">Go to menu</a>.</p>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <h3>Order #<?= escape($order['OrderID']) ?></h3>
                            <p><strong>Date:</strong> <?= escape($order['OrderDate']) ?></p>
                            <p><strong>Collection:</strong> <?= escape($order['CollectionTime']) ?></p>
                            <p><strong>Total:</strong> ₦<?= number_format($order['TotalAmount'], 2) ?></p>
                            <p><strong>Status:</strong> <?= escape($order['OrderStatus']) ?> /
                                <?= escape($order['PaymentStatus']) ?></p>
                            <details>
                                <summary>Order Items</summary>
                                <ul>
                                    <?php foreach ($orderModel->getOrderItems((int) $order['OrderID']) as $item): ?>
                                        <li><?= escape($item['ItemName']) ?> x <?= escape($item['Quantity']) ?>
                                            (₦<?= number_format($item['PriceAtOrder'], 2) ?>)</li>
                                    <?php endforeach; ?>
                                </ul>
                            </details>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>