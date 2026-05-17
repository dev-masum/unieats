<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/functions.php';
requireLogin();
requireStudent();

$db = createDb()->getConnection();
$cartModel = new Cart($db);
$userId = (int) $_SESSION['user_id'];
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        $cartId = (int) $_POST['cart_id'];
        $cartModel->removeItem($cartId);
        $message = 'Item removed from cart.';
    }
    if (isset($_POST['update'])) {
        $cartId = (int) $_POST['cart_id'];
        $quantity = max(1, (int) $_POST['quantity']);
        $cartModel->updateQuantity($cartId, $quantity);
        $message = 'Cart updated.';
    }
}
$items = $cartModel->getCartItems($userId);
$total = $cartModel->getTotalAmount($userId);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>UniEats Cart</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Your Cart</h1>
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
            <?php if (empty($items)): ?>
                <p>Your cart is empty. Go back to the <a href="menu.php">menu</a>.</p>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= escape($item['ItemName']) ?></td>
                                <td>₦<?= number_format($item['Price'], 2) ?></td>
                                <td>
                                    <form method="post" action="cart.php" class="inline-form">
                                        <input type="hidden" name="cart_id" value="<?= escape($item['CartID']) ?>">
                                        <input type="number" name="quantity" value="<?= escape($item['Quantity']) ?>" min="1">
                                        <button type="submit" name="update">Update</button>
                                    </form>
                                </td>
                                <td>₦<?= number_format($item['Price'] * $item['Quantity'], 2) ?></td>
                                <td>
                                    <form method="post" action="cart.php">
                                        <input type="hidden" name="cart_id" value="<?= escape($item['CartID']) ?>">
                                        <button type="submit" name="remove">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total</td>
                            <td colspan="2">₦<?= number_format($total, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="checkout-actions">
                    <a class="button" href="checkout.php">Proceed to Checkout</a>
                    <a class="button secondary" href="menu.php">Continue Shopping</a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>