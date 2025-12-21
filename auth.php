<?php
session_start();
include 'database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Đăng ký
    public function register($email, $password, $name) {
        // Kiểm tra email đã tồn tại
        $check = $this->db->select("SELECT id FROM customers WHERE email = ?", [$email]);
        if (!empty($check)) {
            return ['success' => false, 'message' => 'Email đã được sử dụng'];
        }
        
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $result = $this->db->execute(
            "INSERT INTO customers (email, password_hash, name) VALUES (?, ?, ?)",
            [$email, $password_hash, $name]
        );
        
        return ['success' => $result, 'message' => $result ? 'Đăng ký thành công' : 'Lỗi đăng ký'];
    }
    
    // Đăng nhập
    public function login($email, $password) {
        $user = $this->db->select("SELECT * FROM customers WHERE email = ?", [$email]);
        
        if (empty($user)) {
            return ['success' => false, 'message' => 'Email không tồn tại'];
        }
        
        $user = $user[0];
        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Mật khẩu sai'];
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        
        return ['success' => true, 'message' => 'Đăng nhập thành công'];
    }
    
    // Đăng xuất
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Đăng xuất thành công'];
    }
    
    // Kiểm tra đăng nhập
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Lấy thông tin user
    public static function getUser() {
        return $_SESSION['user_email'] ?? null;
    }
}

// Xử lý request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $auth = new Auth();
    
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $result = $auth->login($email, $password);
        echo json_encode($result);
        exit;
    } elseif ($action === 'register') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $name = $_POST['name'] ?? '';
        $result = $auth->register($email, $password, $name);
        echo json_encode($result);
        exit;
    } elseif ($action === 'logout') {
        $result = $auth->logout();
        header('Location: index.php');
        exit;
    }
}
?>
