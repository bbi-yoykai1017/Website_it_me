<?php
session_start();
include 'database.php';
include 'auth.php';

$db = new Database();

// ================ LẤY THAM SỐ TÌM KIẾM ================
$search       = trim($_GET['q'] ?? '');
$category     = $_GET['category'] ?? 'all';
$author       = $_GET['author'] ?? 'all';
$sort_by      = $_GET['sort'] ?? 'newest'; // newest, price_low, price_high
$limit        = 12; // Số sách hiển thị mỗi trang
$page         = max(1, (int)($_GET['page'] ?? 1));
$offset       = ($page - 1) * $limit;

// ================ XÂY DỰNG CÂU TRUY VẤN ================
$sql = "SELECT b.*, a.name AS author_name, c.name AS cat_name, c.slug AS cat_slug
        FROM books b
        JOIN authors a ON b.author_id = a.id
        JOIN categories c ON b.category_id = c.id
        WHERE 1=1";

$params = [];

// Tìm kiếm theo từ khóa
if (!empty($search)) {
    $sql .= " AND (b.title LIKE ? OR b.description LIKE ? OR a.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Lọc theo danh mục
if ($category !== 'all') {
    $sql .= " AND c.slug = ?";
    $params[] = $category;
}

// Lọc theo tác giả
if ($author !== 'all') {
    $sql .= " AND a.id = ?";
    $params[] = (int)$author;
}

// Sắp xếp
switch ($sort_by) {
    case 'price_low':
        $sql .= " ORDER BY b.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY b.price DESC";
        break;
    case 'popular':
        $sql .= " ORDER BY b.bestseller DESC, b.id DESC";
        break;
    default: // newest
        $sql .= " ORDER BY b.id DESC";
}

// Đếm tổng số kết quả
$countSql = "SELECT COUNT(*) as total FROM books b
             JOIN authors a ON b.author_id = a.id
             JOIN categories c ON b.category_id = c.id
             WHERE 1=1";
$countParams = [];

if (!empty($search)) {
    $countSql .= " AND (b.title LIKE ? OR b.description LIKE ? OR a.name LIKE ?)";
    $countParams[] = "%$search%";
    $countParams[] = "%$search%";
    $countParams[] = "%$search%";
}

if ($category !== 'all') {
    $countSql .= " AND c.slug = ?";
    $countParams[] = $category;
}

if ($author !== 'all') {
    $countSql .= " AND a.id = ?";
    $countParams[] = (int)$author;
}

$total_result = $db->select($countSql, $countParams);
$total_books = $total_result[0]['total'] ?? 0;
$total_pages = ceil($total_books / $limit);

// Lấy danh sách sách
$sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$books = $db->select($sql, $params);

// ================ LẤY DANH SÁCH DANH MỤC ================
$categories = $db->select("SELECT name, slug FROM categories ORDER BY name");

// ================ LẤY DANH SÁCH TÁC GIẢ ================
$authors = $db->select("SELECT id, name FROM authors ORDER BY name");

// ================ TÍNH GIỎ HÀNG ================
$cart_count = 0;
$cart_total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
        $cart_total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Search Books - BookSaw</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="css/normalize.css">
    <link rel="stylesheet" type="text/css" href="icomoon/icomoon.css">
    <link rel="stylesheet" type="text/css" href="css/vendor.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/search.css">

</head>

<body data-bs-spy="scroll" data-bs-target="#header" tabindex="0">

    <div id="header-wrap">

        <div class="top-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="social-links">
                            <ul>
                                <li>
                                    <a href="#"><i class="icon icon-facebook"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="icon icon-twitter"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="icon icon-youtube-play"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="icon icon-behance-square"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="right-element">
                            <?php if (Auth::isLoggedIn()): ?>
                                <a href="cart_view.php" class="cart for-buy"><i class="icon icon-clipboard"></i><span>Cart:(<?= $cart_count ?> - $<?= number_format($cart_total, 2) ?>)</span></a>
                                <a href="javascript:void(0)" onclick="logout()" class="user-account for-buy"><i class="icon icon-user"></i><span>Đăng Xuất</span></a>
                            <?php else: ?>
                                <a href="login.php" class="user-account for-buy"><i class="icon icon-user"></i><span>Đăng Nhập</span></a>
                                <a href="cart_view.php" class="cart for-buy"><i class="icon icon-clipboard"></i><span>Cart:(<?= $cart_count ?> - $0.00)</span></a>
                            <?php endif; ?>

                            <div class="action-menu">
                                <div class="search-bar">
                                    <a href="#" class="search-button search-toggle" data-selector="#header-wrap">
                                        <i class="icon icon-search"></i>
                                    </a>
                                    <form role="search" method="get" class="search-box" action="search.php">
                                        <input class="search-field text search-input" placeholder="Search books..."
                                            type="search" name="q" value="<?= htmlspecialchars($search) ?>">
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <header id="header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        <div class="main-logo">
                            <a href="index.php"><img src="images/main-logo.png" alt="logo"></a>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <nav id="navbar">
                            <div class="main-menu stellarnav">
                                <ul class="menu-list">
                                    <li class="menu-item active"><a href="index.php">Home</a></li>
                                    <li class="menu-item"><a href="search.php" class="nav-link">Tìm Kiếm</a></li>
                                    <li class="menu-item"><a href="#featured-books" class="nav-link">Featured</a></li>
                                    <li class="menu-item"><a href="#special-offer" class="nav-link">Offer</a></li>
                                    <li class="menu-item"><a href="#latest-blog" class="nav-link">Articles</a></li>
                                </ul>

                                <div class="hamburger">
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                    <span class="bar"></span>
                                </div>

                            </div>
                        </nav>

                    </div>

                </div>
            </div>
        </header>

    </div>

    <section class="search-page">
        <div class="container-fluid">
            <div class="row">
                <!-- SIDEBAR FILTERS -->
                <div class="col-md-3">
                    <div class="sidebar">
                        <h3 style="margin-bottom: 20px;">Bộ Lọc Tìm Kiếm</h3>

                        <!-- Tìm kiếm chính -->
                        <form method="get" action="search.php">
                            <div class="filter-group">
                                <div class="filter-title">Tìm Kiếm</div>
                                <div class="filter-option">
                                    <input type="text" name="q" class="form-control" 
                                        placeholder="Tên sách, tác giả..." 
                                        value="<?= htmlspecialchars($search) ?>">
                                </div>
                            </div>

                            <!-- Danh mục -->
                            <div class="filter-group">
                                <div class="filter-title">Danh Mục</div>
                                <div class="filter-option">
                                    <input type="radio" name="category" id="cat_all" value="all" <?= $category === 'all' ? 'checked' : '' ?>>
                                    <label for="cat_all">Tất Cả Danh Mục</label>
                                </div>
                                <?php foreach ($categories as $cat): ?>
                                    <div class="filter-option">
                                        <input type="radio" name="category" id="cat_<?= $cat['slug'] ?>" value="<?= $cat['slug'] ?>" <?= $category === $cat['slug'] ? 'checked' : '' ?>>
                                        <label for="cat_<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Tác giả -->
                            <div class="filter-group">
                                <div class="filter-title">Tác Giả</div>
                                <div class="filter-option">
                                    <input type="radio" name="author" id="author_all" value="all" <?= $author === 'all' ? 'checked' : '' ?>>
                                    <label for="author_all">Tất Cả Tác Giả</label>
                                </div>
                                <?php foreach ($authors as $auth): ?>
                                    <div class="filter-option">
                                        <input type="radio" name="author" id="author_<?= $auth['id'] ?>" value="<?= $auth['id'] ?>" <?= $author == $auth['id'] ? 'checked' : '' ?>>
                                        <label for="author_<?= $auth['id'] ?>"><?= htmlspecialchars($auth['name']) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Sắp xếp -->
                            <div class="filter-group">
                                <div class="filter-title">Sắp Xếp</div>
                                <div class="filter-option">
                                    <select name="sort" class="form-control">
                                        <option value="newest" <?= $sort_by === 'newest' ? 'selected' : '' ?>>Mới Nhất</option>
                                        <option value="popular" <?= $sort_by === 'popular' ? 'selected' : '' ?>>Phổ Biến Nhất</option>
                                        <option value="price_low" <?= $sort_by === 'price_low' ? 'selected' : '' ?>>Giá Thấp → Cao</option>
                                        <option value="price_high" <?= $sort_by === 'price_high' ? 'selected' : '' ?>>Giá Cao → Thấp</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 mt-3">
                                <i class="icon icon-search"></i> Tìm Kiếm
                            </button>
                        </form>
                    </div>
                </div>

                <!-- RESULTS -->
                <div class="col-md-9">
                    <?php if (!empty($search) || $category !== 'all' || $author !== 'all'): ?>
                        <div class="search-info">
                            <h3>Kết Quả Tìm Kiếm</h3>
                            <p>
                                <?php if (!empty($search)): ?>
                                    Từ khóa: <strong><?= htmlspecialchars($search) ?></strong> 
                                <?php endif; ?>
                                <?php if ($category !== 'all'): ?>
                                    | Danh mục: <strong><?= htmlspecialchars($category) ?></strong>
                                <?php endif; ?>
                                <?php if ($author !== 'all'): ?>
                                    | Tác giả: <strong><?= htmlspecialchars($author) ?></strong>
                                <?php endif; ?>
                                <br>
                                Tìm thấy <strong><?= $total_books ?></strong> sách
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="search-results">
                        <?php if (empty($books)): ?>
                            <div class="no-results">
                                <h3>😟 Không tìm thấy sách nào</h3>
                                <p>Vui lòng thử lại với từ khóa khác hoặc điều chỉnh bộ lọc.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($books as $book): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="product-item">
                                            <figure class="product-style">
                                                <img src="images/<?= htmlspecialchars($book['cover_image']) ?>.jpg"
                                                    alt="<?= htmlspecialchars($book['title']) ?>" class="product-item">
                                                <form action="cart.php" method="post" style="display: inline;">
                                                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                                    <button type="submit" name="add_to_cart" class="add-to-cart">
                                                        Thêm Vào Giỏ
                                                    </button>
                                                </form>
                                            </figure>
                                            <figcaption>
                                                <h3><?= htmlspecialchars($book['title']) ?></h3>
                                                <span><?= htmlspecialchars($book['author_name']) ?></span>
                                                <div class="item-price">
                                                    <?php if ($book['old_price'] > 0): ?>
                                                        <span class="prev-price">$ <?= number_format($book['old_price'], 2) ?></span>
                                                    <?php endif; ?>
                                                    $ <?= number_format($book['price'], 2) ?>
                                                </div>
                                            </figcaption>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Phân trang -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Page navigation" class="mt-5">
                                    <ul class="pagination justify-content-center">
                                        <?php 
                                        $query_string = "?q=" . urlencode($search) . 
                                                      "&category=" . urlencode($category) . 
                                                      "&author=" . urlencode($author) . 
                                                      "&sort=" . urlencode($sort_by);
                                        ?>
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= $query_string ?>&page=1">First</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= $query_string ?>&page=<?= $page - 1 ?>">Previous</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php 
                                        $start = max(1, $page - 2);
                                        $end = min($total_pages, $page + 2);
                                        for ($i = $start; $i <= $end; $i++): 
                                        ?>
                                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                <a class="page-link" href="<?= $query_string ?>&page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= $query_string ?>&page=<?= $page + 1 ?>">Next</a>
                                            </li>
                                            <li class="page-item">
                                                <a class="page-link" href="<?= $query_string ?>&page=<?= $total_pages ?>">Last</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-1.11.0.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/script.js"></script>
    <script src="js/search.js"></script>

</body>

</html>
