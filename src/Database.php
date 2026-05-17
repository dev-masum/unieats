<?php

class Database
{
    private $host;
    private $user;
    private $pass;
    private $name;
    private $connection;

    public function __construct(array $config)
    {
        $this->host = $config['db_host'];
        $this->user = $config['db_user'];
        $this->pass = $config['db_pass'];
        $this->name = $config['db_name'];
    }

    public function connect()
    {
        if ($this->connection === null) {
            $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->name);
            if ($this->connection->connect_error) {
                die('Database connection failed: ' . $this->connection->connect_error);
            }
            $this->connection->set_charset('utf8mb4');
        }
        return $this->connection;
    }

    public function getConnection()
    {
        return $this->connect();
    }

    public function close()
    {
        if ($this->connection !== null) {
            $this->connection->close();
        }
    }
}
