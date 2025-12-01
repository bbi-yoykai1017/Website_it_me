<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "dbbook";
    private $conn;

    public function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname,3307);
        $this->conn->set_charset("utf8mb4");
    }

    // SELECT trả mảng
    public function select($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $types = '';
            foreach ($params as $p) {
                if (is_int($p)) $types .= 'i';
                elseif (is_float($p)) $types .= 'd';
                else $types .= 's';
            }
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $data;
    }

    // INSERT/UPDATE/DELETE
    public function execute($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $types = '';
            foreach ($params as $p) {
                if (is_int($p)) $types .= 'i';
                elseif (is_float($p)) $types .= 'd';
                else $types .= 's';
            }
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected >= 0;
    }

    public function insert_id() {
        return $this->conn->insert_id;
    }

    public function close() {
        $this->conn->close();
    }

    public function getConn() {
        return $this->conn;
    }
}
?>