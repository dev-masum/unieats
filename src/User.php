<?php

class User
{
    private mysqli $db;

    public function __construct(mysqli $connection)
    {
        $this->db = $connection;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM Users WHERE Username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM Users WHERE Email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function findById(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM Users WHERE UserID = ? LIMIT 1');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function register(string $username, string $email, string $password, string $fullName): bool
    {
        if ($this->findByUsername($username) || $this->findByEmail($email)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $createdDate = date('Y-m-d');
        $userType = 'student';
        $stmt = $this->db->prepare('INSERT INTO Users (Username, Email, Password, UserType, FullName, CreatedDate) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $username, $email, $hash, $userType, $fullName, $createdDate);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function login(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['Password'])) {
            return null;
        }

        return $user;
    }
}
