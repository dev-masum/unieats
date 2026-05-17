<?php

class Menu
{
    private mysqli $db;

    public function __construct(mysqli $connection)
    {
        $this->db = $connection;
    }

    public function getAllItems(): array
    {
        $query = 'SELECT * FROM Menu ORDER BY Category, ItemName';
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAvailableItems(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM Menu WHERE Availability = 1 ORDER BY Category, ItemName');
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }

    public function getItemsByCategory(string $category): array
    {
        $stmt = $this->db->prepare('SELECT * FROM Menu WHERE Category = ? ORDER BY ItemName');
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $items;
    }

    public function getItemById(int $menuId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM Menu WHERE MenuID = ? LIMIT 1');
        $stmt->bind_param('i', $menuId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $stmt->close();
        return $item ?: null;
    }

    public function getCategories(): array
    {
        $query = 'SELECT DISTINCT Category FROM Menu ORDER BY Category';
        $result = $this->db->query($query);
        return $result ? array_column($result->fetch_all(MYSQLI_ASSOC), 'Category') : [];
    }
}
