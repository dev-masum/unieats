<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/functions.php';
requireLogin();
requireStudent();

$db = createDb()->getConnection();
$orderModel = new Order($db);
$cartModel = new Cart($db);
$userId = (int) $_SESSION['user_id'];
$message = null;
$successMessage = null;

$items = $cartModel->getCartItems($userId);
$total = $cartModel->getTotalAmount($userId);

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collectionTime = trim($_POST['collection_time'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? '');
    $specialInstructions = trim($_POST['special_instructions'] ?? '');

    if ($collectionTime === '' || $paymentMethod === '') {
        $message = 'Please choose a collection time and payment method.';
    } else {
        $orderId = $orderModel->createOrder($userId, $collectionTime, $paymentMethod, 'pending', 'placed', $specialInstructions);
        if ($orderId !== null) {
            $successMessage = 'Your order has been placed successfully. Order #' . $orderId;
            $items = [];
            $total = 0.0;
        } else {
            $message = 'Unable to place order. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>UniEats Checkout</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Checkout</h1>
            <nav>
                <a href="menu.php">Menu</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <main>
            <?php if ($message): ?>
                <div class="alert"><?= escape($message) ?></div>
            <?php endif; ?>
            <?php if ($successMessage): ?>
                <div class="success"><?= escape($successMessage) ?></div>
                <p><a href="orders.php">View order history</a></p>
            <?php else: ?>
                <section class="checkout-summary">
                    <h2>Order Summary</h2>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?= escape($item['ItemName']) ?></td>
                                    <td><?= escape($item['Quantity']) ?></td>
                                    <td>$<?= number_format($item['Price'], 2) ?></td>
                                    <td>$<?= number_format($item['Price'] * $item['Quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Total</td>
                                <td>$<?= number_format($total, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </section>
                <section class="checkout-form">
                    <h2>Delivery Details</h2>
                    <form method="post" action="checkout.php">
                        <label for="collection_time">Collection Time</label>
                        <input type="datetime-local" id="collection_time" name="collection_time" required>
                        <label for="payment_method">Payment Method</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                        </select>
                        <label for="special_instructions">Special Instructions</label>
                        <textarea id="special_instructions" name="special_instructions"></textarea>
                        <button type="submit">Place Order</button>
                    </form>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>