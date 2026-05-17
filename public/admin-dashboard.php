<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../src/functions.php';
requireLogin();
requireAdmin();

$db = createDb()->getConnection();
$orderModel = new Order($db);
$logModel = new AdminLog($db);
$orders = $orderModel->getAllOrders();
$logs = $logModel->getRecentLogs();
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int) ($_POST['order_id'] ?? 0);
    $orderStatus = trim($_POST['order_status'] ?? '');
    $paymentStatus = trim($_POST['payment_status'] ?? '');
    if ($orderId > 0 && $orderStatus !== '' && $paymentStatus !== '') {
        $updated = $orderModel->updateStatus($orderId, $orderStatus, $paymentStatus);
        if ($updated) {
            $logModel->addLog((int) $_SESSION['user_id'], 'Order update', "Order #{$orderId} set to {$orderStatus} / {$paymentStatus}", $orderId);
            $message = 'Order status updated.';
            $orders = $orderModel->getAllOrders();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>UniEats Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <a href="menu.php">Menu</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
                <a href="admin-dashboard.php">Admin</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        <main>
            <?php if ($message): ?>
                <div class="alert"><?= escape($message) ?></div>
            <?php endif; ?>
            <section class="admin-panel">
                <h2>All Orders</h2>
                <?php if (empty($orders)): ?>
                    <p>No orders found.</p>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>User</th>
                                <th>Total</th>
                                <th>Collection</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= escape($order['OrderID']) ?></td>
                                    <td><?= escape($order['Username']) ?></td>
                                    <td>₦<?= number_format($order['TotalAmount'], 2) ?></td>
                                    <td><?= escape($order['CollectionTime']) ?></td>
                                    <td><?= escape($order['OrderStatus']) ?></td>
                                    <td><?= escape($order['PaymentStatus']) ?></td>
                                    <td>
                                        <form method="post" action="admin-dashboard.php" class="inline-form">
                                            <input type="hidden" name="order_id" value="<?= escape($order['OrderID']) ?>">
                                            <select name="order_status">
                                                <?php foreach (['placed', 'confirmed', 'ready', 'collected', 'cancelled'] as $status): ?>
                                                    <option value="<?= $status ?>" <?= $status === $order['OrderStatus'] ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <select name="payment_status">
                                                <?php foreach (['pending', 'completed', 'failed'] as $status): ?>
                                                    <option value="<?= $status ?>" <?= $status === $order['PaymentStatus'] ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>
            <section class="admin-panel">
                <h2>Recent Admin Logs</h2>
                <?php if (empty($logs)): ?>
                    <p>No admin logs yet.</p>
                <?php else: ?>
                    <ul class="admin-log-list">
                        <?php foreach ($logs as $log): ?>
                            <li>
                                <strong><?= escape($log['AdminName']) ?></strong> — <?= escape($log['Action']) ?>
                                <span><?= escape($log['Description']) ?></span>
                                <em><?= escape($log['Timestamp']) ?></em>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>

</html>