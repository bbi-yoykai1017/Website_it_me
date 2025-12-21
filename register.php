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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Vui lòng điền đầy đủ thông tin';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu không khớp';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự';
    } else {
        $auth = new Auth();
        $result = $auth->register($email, $password, $name);
        if ($result['success']) {
            $success = 'Đăng ký thành công! Vui lòng <a href="login.php">đăng nhập</a>';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng Ký - BookSaw</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .register-card {
            width: 100%;
            max-width: 450px;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .register-card h2 {
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
        .btn-register {
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
        .btn-register:hover {
            opacity: 0.9;
        }
        .alert {
            margin-bottom: 20px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="register-card">
        <h2>Đăng Ký</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="text" name="name" class="form-control" placeholder="Họ và tên" required>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
            <input type="password" name="confirm_password" class="form-control" placeholder="Xác nhận mật khẩu" required>
            <button type="submit" name="register" class="btn-register">Đăng Ký</button>
        </form>
        
        <div class="login-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
