<?php 

    final class db
    {
        private $conn_pdo;
        
        public function __construct() {
            $this->dbconnect();
        }

        public function dbconnect() {
            $host = "mysql:host=" . mysql_connect['host'] . ";port=" . mysql_connect['port'] . ";dbname=" . mysql_connect['db'];
            $user = mysql_connect['user'];
            $pass = mysql_connect['pass'];

            try {
                $this->conn_pdo = new PDO($host, $user, $pass);
                // установка режима вывода ошибок
                $this->conn_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $data['db_connect'] = true;
            }
            catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                $data['db_connect'] = false;
                $data['error'] = $e->getMessage();
            }

            if ($data['db_connect']) {
                return $data['db_connect'];
            } else {
                return $data['db_connect'];
            }
        }

        public function get_arr($sql) : array {
            $result = $this->conn_pdo->query($sql);
            $result = $result->fetchAll();
            return $result;
        }

        public function get_single($sql) : bool|array {
            $result = $this->conn_pdo->query($sql);
            $result = $result->fetch();
            return $result;
        }

        public function send($sql) {
            $result = $this->conn_pdo->query($sql);
            $result->fetch(PDO::FETCH_BOUND);
            $result = $result->rowCount();
            return $result;
        }

        function escape(string $str) : string {
            $str = $this->conn_pdo->quote($str);
            return $str;
        }

        function last_insert_id(string $table) : string|bool {
            $result = $this->conn_pdo->lastInsertId($table);
            return $result;
        }

        function __destruct() {
            $this->conn_pdo = null;
        }
    }