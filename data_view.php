<?php
session_start();
include 'database.php';
include 'auth.php';

$db = new Database();

// Lấy tất cả dữ liệu từ database
$books = $db->select("SELECT b.*, a.name AS author_name, c.name AS cat_name FROM books b JOIN authors a ON b.author_id = a.id JOIN categories c ON b.category_id = c.id ORDER BY b.id DESC");
$authors = $db->select("SELECT * FROM authors ORDER BY name");
$categories = $db->select("SELECT * FROM categories ORDER BY name");
$posts = $db->select("SELECT * FROM posts ORDER BY published_at DESC");
$customers = $db->select("SELECT * FROM customers ORDER BY id DESC");
$orders = $db->select("SELECT o.*, c.name as customer_name FROM orders o LEFT JOIN customers c ON o.customer_id = c.id ORDER BY o.id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Dữ liệu BookSaw</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .section-title {
            margin: 30px 0 20px;
            color: #333;
            font-weight: bold;
        }
        table {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th {
            background: #667eea;
            color: white;
            font-weight: bold;
        }
        .container {
            max-width: 1400px;
        }
        .nav-tabs {
            border-bottom: 2px solid #667eea;
        }
        .nav-link {
            color: #667eea;
        }
        .nav-link.active {
            background: #667eea;
            color: white;
        }
        .tab-content {
            padding: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 style="margin-bottom: 30px; color: #667eea;">📚 Quản lý Dữ liệu BookSaw</h1>
    
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#books">📖 Sách (<?= count($books) ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#authors">✍️ Tác giả (<?= count($authors) ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#categories">📂 Thể loại (<?= count($categories) ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#posts">📝 Bài viết (<?= count($posts) ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#customers">👥 Khách hàng (<?= count($customers) ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#orders">🛒 Đơn hàng (<?= count($orders) ?>)</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- TAB SÁCH -->
        <div id="books" class="tab-pane fade show active">
            <h2 class="section-title">Danh sách Sách</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Tác giả</th>
                            <th>Thể loại</th>
                            <th>Giá</th>
                            <th>Giá cũ</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Bestseller</th>
                            <th>Sale</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?= $book['id'] ?></td>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['author_name']) ?></td>
                                <td><?= htmlspecialchars($book['cat_name']) ?></td>
                                <td>$<?= number_format($book['price'], 2) ?></td>
                                <td><?= $book['old_price'] ? '$' . number_format($book['old_price'], 2) : '-' ?></td>
                                <td><?= $book['stock'] ?></td>
                                <td><?= $book['featured'] ? '✅' : '❌' ?></td>
                                <td><?= $book['bestseller'] ? '✅' : '❌' ?></td>
                                <td><?= $book['on_sale'] ? '✅' : '❌' ?></td>
                                <td><?= date('d/m/Y', strtotime($book['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB TÁC GIẢ -->
        <div id="authors" class="tab-pane fade">
            <h2 class="section-title">Danh sách Tác giả</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Tiểu sử</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($authors as $author): ?>
                            <tr>
                                <td><?= $author['id'] ?></td>
                                <td><?= htmlspecialchars($author['name']) ?></td>
                                <td><?= htmlspecialchars(substr($author['bio'] ?? '', 0, 100)) ?></td>
                                <td><?= date('d/m/Y', strtotime($author['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB THỂ LOẠI -->
        <div id="categories" class="tab-pane fade">
            <h2 class="section-title">Danh sách Thể loại</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Slug</th>
                            <th>Mô tả</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= $cat['id'] ?></td>
                                <td><?= htmlspecialchars($cat['name']) ?></td>
                                <td><?= htmlspecialchars($cat['slug']) ?></td>
                                <td><?= htmlspecialchars(substr($cat['description'] ?? '', 0, 100)) ?></td>
                                <td><?= date('d/m/Y', strtotime($cat['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB BÀI VIẾT -->
        <div id="posts" class="tab-pane fade">
            <h2 class="section-title">Danh sách Bài viết</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Slug</th>
                            <th>Tác giả</th>
                            <th>Hình ảnh</th>
                            <th>Ngày xuất bản</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?= $post['id'] ?></td>
                                <td><?= htmlspecialchars($post['title']) ?></td>
                                <td><?= htmlspecialchars($post['slug']) ?></td>
                                <td><?= htmlspecialchars($post['author_name']) ?></td>
                                <td><?= htmlspecialchars($post['featured_image']) ?></td>
                                <td><?= date('d/m/Y', strtotime($post['published_at'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($post['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB KHÁCH HÀNG -->
        <div id="customers" class="tab-pane fade">
            <h2 class="section-title">Danh sách Khách hàng</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?= $customer['id'] ?></td>
                                <td><?= htmlspecialchars($customer['name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($customer['email']) ?></td>
                                <td><?= htmlspecialchars($customer['phone'] ?? '') ?></td>
                                <td><?= htmlspecialchars(substr($customer['address'] ?? '', 0, 50)) ?></td>
                                <td><?= date('d/m/Y', strtotime($customer['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB ĐƠN HÀNG -->
        <div id="orders" class="tab-pane fade">
            <h2 class="section-title">Danh sách Đơn hàng</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Email</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['customer_name'] ?? $order['customer_email']) ?></td>
                                <td><?= htmlspecialchars($order['customer_email']) ?></td>
                                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <?php 
                                    $status_colors = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $color = $status_colors[$order['status']] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>"><?= ucfirst($order['status']) ?></span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 30px; padding: 20px; background: white; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <h5>Thống kê:</h5>
        <ul>
            <li>📖 Tổng sách: <strong><?= count($books) ?></strong></li>
            <li>✍️ Tổng tác giả: <strong><?= count($authors) ?></strong></li>
            <li>📂 Tổng thể loại: <strong><?= count($categories) ?></strong></li>
            <li>📝 Tổng bài viết: <strong><?= count($posts) ?></strong></li>
            <li>👥 Tổng khách hàng: <strong><?= count($customers) ?></strong></li>
            <li>🛒 Tổng đơn hàng: <strong><?= count($orders) ?></strong></li>
        </ul>
        <a href="index.php" class="btn btn-primary mt-3">← Quay lại Trang chủ</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
