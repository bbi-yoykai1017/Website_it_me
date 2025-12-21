<?php
session_start();

// Lấy giỏ hàng từ session, nếu không có thì tạo mới
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý thêm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $book_id = $_POST['book_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($book_id) {
        include 'database.php';
        $db = new Database();
        
        // Lấy thông tin sách
        $book = $db->select("SELECT * FROM books WHERE id = ?", [$book_id]);
        
        if (!empty($book)) {
            $book = $book[0];
            
            // Kiểm tra sách đã có trong giỏ
            if (isset($_SESSION['cart'][$book_id])) {
                $_SESSION['cart'][$book_id]['quantity'] += (int)$quantity;
            } else {
                $_SESSION['cart'][$book_id] = [
                    'id' => $book['id'],
                    'title' => $book['title'],
                    'price' => $book['price'],
                    'cover_image' => $book['cover_image'],
                    'quantity' => (int)$quantity
                ];
            }
            
            // Trả về JSON để xử lý AJAX
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng']);
            exit;
        }
    }
}

// Xử lý xóa khỏi giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $book_id = $_POST['book_id'] ?? '';
    
    if ($book_id && isset($_SESSION['cart'][$book_id])) {
        unset($_SESSION['cart'][$book_id]);
        
        // Trả về JSON
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng']);
        exit;
    }
}

// Xử lý cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $book_id = $_POST['book_id'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($book_id && isset($_SESSION['cart'][$book_id])) {
        $quantity = (int)$quantity;
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$book_id]);
        } else {
            $_SESSION['cart'][$book_id]['quantity'] = $quantity;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Cập nhật số lượng thành công']);
        exit;
    }
}

// Xử lý xóa toàn bộ giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Giỏ hàng đã được xóa']);
    exit;
}

// Trả về số lượng sản phẩm trong giỏ
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_count'])) {
    header('Content-Type: application/json');
    $count = 0;
    $total = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
        $total += $item['price'] * $item['quantity'];
    }
    
    echo json_encode(['count' => $count, 'total' => number_format($total, 2)]);
    exit;
}
?>
