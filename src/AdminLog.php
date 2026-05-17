<?php

class AdminLog
{
    private mysqli $db;

    public function __construct(mysqli $connection)
    {
        $this->db = $connection;
    }

    public function addLog(int $adminUserId, string $action, string $description, ?int $relatedOrderId = null): bool
    {
        $stmt = $this->db->prepare('INSERT INTO AdminLogs (AdminUserID, Action, Description, Timestamp, RelatedOrderID) VALUES (?, ?, ?, NOW(), ?)');
        $stmt->bind_param('issi', $adminUserId, $action, $description, $relatedOrderId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getRecentLogs(int $limit = 10): array
    {
        $stmt = $this->db->prepare('SELECT al.*, u.Username AS AdminName FROM AdminLogs al JOIN Users u ON al.AdminUserID = u.UserID ORDER BY al.Timestamp DESC LIMIT ?');
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $logs = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $logs;
    }
}
