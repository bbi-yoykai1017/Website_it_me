<?php
session_start();
include 'database.php';
include 'auth.php';

// Nếu đã đăng nhập, redirect về trang chủ
if (Auth::isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } else {
        $auth = new Auth();
        $result = $auth->login($email, $password);
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng Nhập - BookSaw</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .login-card h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-control {
            margin-bottom: 15px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .btn-login {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-login:hover {
            opacity: 0.9;
        }
        .alert {
            margin-bottom: 20px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-card">
        <h2>Đăng Nhập</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
            <button type="submit" name="login" class="btn-login">Đăng Nhập</button>
        </form>
        
        <div class="register-link">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
