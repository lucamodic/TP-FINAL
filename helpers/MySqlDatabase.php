<?php

class MySqlDatabase {
    private $connection;

    public function __construct($serverName, $userName, $password, $databaseName) {
        $this->connection = mysqli_connect(
            $serverName,
            $userName,
            $password,
            $databaseName);

        if (!$this->connection) {
            die('Connection failed: ' . mysqli_connect_error());
        }
    }

    public function __destruct() {
        mysqli_close($this->connection);
    }

    public function query($sql) {
        Logger::info('Ejecutando query: ' . $sql);
        $result = mysqli_query($this->connection, $sql);
        return mysqli_fetch_all($result, MYSQLI_BOTH);
    }

    public function fetchColumn($sql){
        $result = mysqli_query($this->connection, $sql);
        $row = mysqli_fetch_row($result);
        return (int)$row[0];
    }

    public function querySinFetchAll($sql) {
        Logger::info('Ejecutando query: ' . $sql);
        $result = mysqli_query($this->connection, $sql);
        return mysqli_num_rows($result);
    }

    public function execute($sql) {
        Logger::info('Ejecutando query: ' . $sql);
        mysqli_query($this->connection, $sql);
    }

    public function getId(){
        return mysqli_insert_id($this->connection);
    }
}