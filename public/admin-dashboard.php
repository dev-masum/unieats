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
                <a href="admin-dashboard.php">Dashboard</a>
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
                                <th>Order ID</th>
                                <th>Student</th>
                                <th>Total Amount</th>
                                <th>Collection Time</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Manage Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= escape($order['OrderID']) ?></td>
                                    <td><?= escape($order['Username']) ?></td>
                                    <td>$<?= number_format($order['TotalAmount'], 2) ?></td>
                                    <td><?= escape($order['CollectionTime']) ?></td>
                                    <td><?= escape($order['OrderStatus']) ?></td>
                                    <td><?= escape($order['PaymentStatus']) ?></td>
                                    <td>
                                        <form method="post" action="admin-dashboard.php" style="display: flex; flex-direction: column; gap: 8px; min-width: 140px;">
                                            <input type="hidden" name="order_id" value="<?= escape($order['OrderID']) ?>">
                                            
                                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                                <label for="order_status_<?= $order['OrderID'] ?>" style="margin: 0; font-size: 0.8rem; color: #666; text-transform: uppercase; font-weight: bold;">Status</label>
                                                <select id="order_status_<?= $order['OrderID'] ?>" name="order_status" aria-label="Order Status" title="Update Order Status" style="padding: 6px; font-size: 0.9rem;">
                                                    <?php foreach (['placed', 'confirmed', 'ready', 'collected', 'cancelled'] as $status): ?>
                                                        <option value="<?= $status ?>" <?= $status === $order['OrderStatus'] ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                                <label for="payment_status_<?= $order['OrderID'] ?>" style="margin: 0; font-size: 0.8rem; color: #666; text-transform: uppercase; font-weight: bold;">Payment</label>
                                                <select id="payment_status_<?= $order['OrderID'] ?>" name="payment_status" aria-label="Payment Status" title="Update Payment Status" style="padding: 6px; font-size: 0.9rem;">
                                                    <?php foreach (['pending', 'completed', 'failed'] as $status): ?>
                                                        <option value="<?= $status ?>" <?= $status === $order['PaymentStatus'] ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <button type="submit" style="padding: 6px 12px; font-size: 0.9rem; margin-top: 4px;">Update</button>
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
                                <strong>Admin:</strong> <?= escape($log['AdminName']) ?> |
                                <strong>Action:</strong> <?= escape($log['Action']) ?><br>
                                <strong>Details:</strong> <span><?= escape($log['Description']) ?></span><br>
                                <strong>Date/Time:</strong> <em><?= escape($log['Timestamp']) ?></em>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>

</html>