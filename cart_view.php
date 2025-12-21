<?php
session_start();
include 'database.php';
include 'auth.php';

// Tính tổng giỏ hàng
$cart_count = 0;
$cart_total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
        $cart_total += $item['price'] * $item['quantity'];
    }
}

// Xử lý thanh toán
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!Auth::isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
    
    if (empty($_SESSION['cart'])) {
        $error = 'Giỏ hàng trống';
    } else {
        $db = new Database();
        $customer_id = $_SESSION['user_id'];
        $customer_info = $db->select("SELECT * FROM customers WHERE id = ?", [$customer_id]);
        $customer = $customer_info[0];
        
        // Tạo đơn hàng
        $db->execute(
            "INSERT INTO orders (customer_id, customer_name, customer_email, customer_phone, customer_address, total_amount, status) 
             VALUES (?, ?, ?, ?, ?, ?, 'pending')",
            [$customer_id, $customer['name'], $customer['email'], $customer['phone'], $customer['address'], $cart_total]
        );
        
        // Lấy order_id vừa tạo
        $order = $db->select("SELECT id FROM orders WHERE customer_id = ? ORDER BY id DESC LIMIT 1", [$customer_id]);
        $order_id = $order[0]['id'];
        
        // Thêm chi tiết đơn hàng
        foreach ($_SESSION['cart'] as $item) {
            $db->execute(
                "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)",
                [$order_id, $item['id'], $item['quantity'], $item['price']]
            );
        }
        
        // Xóa giỏ hàng
        $_SESSION['cart'] = [];
        $success_message = 'Thanh toán thành công! Đơn hàng #' . $order_id . ' của bạn đang được xử lý.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Giỏ Hàng - BookSaw</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .cart-container {
            min-height: 100vh;
            padding: 40px 20px;
            background: #f8f9fa;
        }
        .cart-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .cart-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 80px;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
        }
        .quantity-input {
            width: 60px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-remove:hover {
            background: #c82333;
        }
        .cart-summary {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 16px;
        }
        .summary-row.total {
            border-top: 2px solid #eee;
            padding-top: 15px;
            font-weight: bold;
            font-size: 18px;
            color: #667eea;
        }
        .btn-checkout {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 15px;
        }
        .btn-checkout:hover {
            opacity: 0.9;
        }
        .empty-cart {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .empty-cart h3 {
            color: #999;
            margin-bottom: 20px;
        }
        .empty-cart a {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="cart-container">
    <div class="container">
        <div class="cart-header">
            <h1>Giỏ Hàng</h1>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <h3>Giỏ hàng của bạn trống</h3>
                <p>Hãy thêm một số sách vào giỏ hàng của bạn</p>
                <a href="index.php">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <div class="cart-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['cart'] as $book_id => $item): ?>
                            <tr class="cart-item">
                                <td>
                                    <img src="./images/<?= htmlspecialchars($item['cover_image']) ?>.jpg" alt="<?= htmlspecialchars($item['title']) ?>" class="item-image" style="margin-right: 15px;">
                                    <strong><?= htmlspecialchars($item['title']) ?></strong>
                                </td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>
                                    <input type="number" min="1" value="<?= $item['quantity'] ?>" class="quantity-input" data-book-id="<?= $book_id ?>" onchange="updateQuantity(this)">
                                </td>
                                <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                <td>
                                    <button class="btn-remove" onclick="removeItem(<?= $book_id ?>)">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span>$<?= number_format($cart_total, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span>$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Tổng cộng:</span>
                    <span>$<?= number_format($cart_total, 2) ?></span>
                </div>
                
                <form method="POST">
                    <?php if (Auth::isLoggedIn()): ?>
                        <button type="submit" name="checkout" class="btn-checkout">Thanh Toán</button>
                    <?php else: ?>
                        <a href="login.php" class="btn-checkout" style="display: block; text-align: center; text-decoration: none;">Đăng Nhập để Thanh Toán</a>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateQuantity(input) {
    const bookId = input.dataset.bookId;
    const quantity = input.value;
    
    const formData = new FormData();
    formData.append('update_quantity', true);
    formData.append('book_id', bookId);
    formData.append('quantity', quantity);
    
    fetch('cart.php', {
        method: 'POST',
        body: formData
    }).then(() => {
        location.reload();
    });
}

function removeItem(bookId) {
    if (confirm('Bạn có chắc chắn muốn xóa sách này?')) {
        const formData = new FormData();
        formData.append('remove_from_cart', true);
        formData.append('book_id', bookId);
        
        fetch('cart.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            location.reload();
        });
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
