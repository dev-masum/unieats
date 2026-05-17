<?php

class Order
{
    private mysqli $db;

    public function __construct(mysqli $connection)
    {
        $this->db = $connection;
    }

    public function createOrder(int $userId, string $collectionTime, string $paymentMethod, string $paymentStatus, string $orderStatus, ?string $specialInstructions): ?int
    {
        $cartQuery = $this->db->prepare('SELECT c.MenuID, c.Quantity, m.Price FROM Cart c JOIN Menu m ON c.MenuID = m.MenuID WHERE c.UserID = ?');
        $cartQuery->bind_param('i', $userId);
        $cartQuery->execute();
        $cartResult = $cartQuery->get_result();
        $cartItems = $cartResult->fetch_all(MYSQLI_ASSOC);
        $cartQuery->close();

        if (empty($cartItems)) {
            return null;
        }

        $totalAmount = 0.0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['Price'] * $item['Quantity'];
        }

        $stmt = $this->db->prepare('INSERT INTO Orders (UserID, OrderDate, CollectionTime, TotalAmount, PaymentMethod, PaymentStatus, OrderStatus, SpecialInstructions) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isdssss', $userId, $collectionTime, $totalAmount, $paymentMethod, $paymentStatus, $orderStatus, $specialInstructions);
        $success = $stmt->execute();
        $orderId = $this->db->insert_id;
        $stmt->close();

        if (!$success) {
            return null;
        }

        $itemStmt = $this->db->prepare('INSERT INTO OrderItems (OrderID, MenuID, Quantity, PriceAtOrder) VALUES (?, ?, ?, ?)');
        foreach ($cartItems as $item) {
            $itemStmt->bind_param('iiid', $orderId, $item['MenuID'], $item['Quantity'], $item['Price']);
            $itemStmt->execute();
        }
        $itemStmt->close();

        $this->clearCart($userId);
        return $orderId;
    }

    public function getOrdersByUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM Orders WHERE UserID = ? ORDER BY OrderDate DESC');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $orders = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $orders;
    }

    public function getOrderItems(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT oi.*, m.ItemName FROM OrderItems oi JOIN Menu m ON oi.MenuID = m.MenuID WHERE oi.OrderID = ?');
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }

    public function getOrderById(int $orderId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM Orders WHERE OrderID = ? LIMIT 1');
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        $stmt->close();
        return $order ?: null;
    }

    public function getAllOrders(): array
    {
        $query = 'SELECT o.*, u.Username FROM Orders o JOIN Users u ON o.UserID = u.UserID ORDER BY o.OrderDate DESC';
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function updateStatus(int $orderId, string $orderStatus, string $paymentStatus): bool
    {
        $stmt = $this->db->prepare('UPDATE Orders SET OrderStatus = ?, PaymentStatus = ? WHERE OrderID = ?');
        $stmt->bind_param('ssi', $orderStatus, $paymentStatus, $orderId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    private function clearCart(int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM Cart WHERE UserID = ?');
        $stmt->bind_param('i', $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
