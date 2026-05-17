<?php

class Cart
{
    private mysqli $db;

    public function __construct(mysqli $connection)
    {
        $this->db = $connection;
    }

    public function addItem(int $userId, int $menuId, int $quantity): bool
    {
        $existing = $this->findItem($userId, $menuId);
        if ($existing) {
            $quantity += (int) $existing['Quantity'];
            $stmt = $this->db->prepare('UPDATE Cart SET Quantity = ?, AddedDate = NOW() WHERE CartID = ?');
            $stmt->bind_param('ii', $quantity, $existing['CartID']);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }

        $stmt = $this->db->prepare('INSERT INTO Cart (UserID, MenuID, Quantity, AddedDate) VALUES (?, ?, ?, NOW())');
        $stmt->bind_param('iii', $userId, $menuId, $quantity);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function findItem(int $userId, int $menuId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM Cart WHERE UserID = ? AND MenuID = ? LIMIT 1');
        $stmt->bind_param('ii', $userId, $menuId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();
        return $item ?: null;
    }

    public function getCartItems(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT c.CartID, c.MenuID, c.Quantity, c.AddedDate, m.ItemName, m.Price, m.Availability FROM Cart c JOIN Menu m ON c.MenuID = m.MenuID WHERE c.UserID = ? ORDER BY c.AddedDate DESC');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }

    public function updateQuantity(int $cartId, int $quantity): bool
    {
        $stmt = $this->db->prepare('UPDATE Cart SET Quantity = ? WHERE CartID = ?');
        $stmt->bind_param('ii', $quantity, $cartId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function removeItem(int $cartId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM Cart WHERE CartID = ?');
        $stmt->bind_param('i', $cartId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function clearCart(int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM Cart WHERE UserID = ?');
        $stmt->bind_param('i', $userId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getTotalAmount(int $userId): float
    {
        $stmt = $this->db->prepare('SELECT SUM(c.Quantity * m.Price) AS total FROM Cart c JOIN Menu m ON c.MenuID = m.MenuID WHERE c.UserID = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row['total'] !== null ? (float) $row['total'] : 0.0;
    }
}
