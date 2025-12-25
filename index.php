<?php
session_start();
include 'database.php';
include 'auth.php';

$db = new Database();   

// ================ 1. FEATURED BOOKS ================
$featured_books = $db->select("
    SELECT b.*, a.name AS author_name 
    FROM books b 
    JOIN authors a ON b.author_id = a.id 
    WHERE b.featured = 1 
    ORDER BY b.id DESC 
    LIMIT 4
");

// ================ 2. POPULAR BOOKS + TÌM KIẾM + PHÂN TRANG ================
$limit    = 4; // Số sách hiển thị mỗi trang
$page     = max(1, (int)($_GET['page'] ?? 1));
$category = $_GET['cat'] ?? 'all';
$search   = trim($_GET['search'] ?? ''); // Lấy từ khóa tìm kiếm
$offset   = ($page - 1) * $limit;

// -- Câu truy vấn lấy sách --
$sql2 = "SELECT b.*, a.name AS author_name, c.name AS cat_name, c.slug AS cat_slug
         FROM books b
         JOIN authors a ON b.author_id = a.id
         JOIN categories c ON b.category_id = c.id
         WHERE 1=1"; // Kỹ thuật WHERE 1=1 để dễ nối chuỗi AND

$params = [];

// 1. Lọc theo danh mục
if ($category !== 'all') {
    $sql2 .= " AND c.slug = ?";
    $params[] = $category;
}

// 2. Lọc theo từ khóa tìm kiếm (MỚI)
if (!empty($search)) {
    $sql2 .= " AND (b.title LIKE ? OR a.name LIKE ?)";
    $params[] = "%$search%"; // Tìm kiếm tương đối theo tên sách
    $params[] = "%$search%"; // Tìm kiếm tương đối theo tên tác giả
}

// Thêm sắp xếp và giới hạn phân trang
$sql2 .= " ORDER BY b.id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

// Thực thi lấy sách
$books = $db->select($sql2, $params);

// ================ 3. ĐẾM TỔNG SỐ SÁCH (Cập nhật cho tìm kiếm) ================
$countSql = "SELECT COUNT(*) FROM books b 
             JOIN authors a ON b.author_id = a.id
             JOIN categories c ON b.category_id = c.id
             WHERE 1=1";
$countParams = [];

if ($category !== 'all') {
    $countSql .= " AND c.slug = ?";
    $countParams[] = $category;
}

if (!empty($search)) {
    $countSql .= " AND (b.title LIKE ? OR a.name LIKE ?)";
    $countParams[] = "%$search%";
    $countParams[] = "%$search%";
}

$total_result = $db->select($countSql, $countParams);
$total_books = $total_result[0]['COUNT(*)'] ?? 0;
$total_pages = ceil($total_books / $limit);

// ================ 3. ĐẾM TỔNG SỐ SÁCH (cho phân trang) ================
$countSql = "SELECT COUNT(*) FROM books b JOIN categories c ON b.category_id = c.id";
$countParams = [];

if ($category !== 'all') {
    $countSql .= " WHERE c.slug = ?";
    $countParams[] = $category;
}

$total_result = $db->select($countSql, $countParams);
$total_books = $total_result[0]['COUNT(*)'] ?? 0;
$total_pages = ceil($total_books / $limit);

// ================ 4. LẤY DANH SÁCH THỂ LOẠI ================
$categories = $db->select("SELECT name, slug FROM categories ORDER BY name");

// ================ 5. LẤY BÀI VIẾT BLOG ================
$posts = $db->select("SELECT * FROM posts ORDER BY published_at DESC LIMIT 3");

// ================ 6. TÍNH GIỎ HÀNG ================
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
	<title>BookSaw</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="format-detection" content="telephone=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="author" content="">
	<meta name="keywords" content="">
	<meta name="description" content="">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">

	<link rel="stylesheet" type="text/css" href="css/normalize.css">
	<link rel="stylesheet" type="text/css" href="icomoon/icomoon.css">
	<link rel="stylesheet" type="text/css" href="css/vendor.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">

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
						</div><!--social-links-->
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
										<input class="search-field text search-input" placeholder="Tìm kiếm sách..."
											type="search" name="q">
									</form>
								</div>
							</div>

						</div><!--top-right-->
					</div>

				</div>
			</div>
		</div><!--top-content-->

		<header id="header">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-2">
						<div class="main-logo">
							<a href="index.html"><img src="images/main-logo.png" alt="logo"></a>
						</div>
					</div>
					<div class="col-md-10">
						<nav id="navbar">
							<div class="main-menu stellarnav">
								<ul class="menu-list">
									<li class="menu-item active"><a href="#home">Home</a></li>
									<li class="menu-item has-sub">
										<a href="#pages" class="nav-link">Pages</a>

										<ul>
											<li class="active"><a href="index.php">Home</a></li>
											<li><a href="search.php">Tìm Kiếm Sách</a></li>
											<li><a href="index.php">About</a></li>
											<li><a href="index.php">Styles</a></li>
											<li><a href="index.php">Blog</a></li>
											<li><a href="index.php">Post Single</a></li>
											<li><a href="index.php">Our Store</a></li>
											<li><a href="index.php">Product Single</a></li>
											<li><a href="index.php">Contact</a></li>
											<li><a href="index.php">Thank You</a></li>
										</ul>
									</li>
									<li class="menu-item"><a href="#featured-books" class="nav-link">Featured</a></li>
									<li class="menu-item"><a href="search.php" class="nav-link">Tìm Kiếm</a></li>
									<li class="menu-item"><a href="#special-offer" class="nav-link">Offer</a></li>
									<li class="menu-item"><a href="#latest-blog" class="nav-link">Articles</a></li>
									<li class="menu-item"><a href="#download-app" class="nav-link">Download App</a></li>
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

	</div><!--header-wrap-->

	<section id="billboard">

		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<button class="prev slick-arrow">
						<i class="icon icon-arrow-left"></i>
					</button>

					<div class="main-slider pattern-overlay">
						<div class="slider-item">
							<div class="banner-content">
								<h2 class="banner-title">Life of the Wild</h2>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu feugiat amet, libero
									ipsum enim pharetra hac. Urna commodo, lacus ut magna velit eleifend. Amet, quis
									urna, a eu.</p>
								<div class="btn-wrap">
									<a href="#" class="btn btn-outline-accent btn-accent-arrow">Read More<i
											class="icon icon-ns-arrow-right"></i></a>
								</div>
							</div><!--banner-content-->
							<img src="images/main-banner1.jpg" alt="banner" class="banner-image">
						</div><!--slider-item-->

						<div class="slider-item">
							<div class="banner-content">
								<h2 class="banner-title">Birds gonna be Happy</h2>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu feugiat amet, libero
									ipsum enim pharetra hac. Urna commodo, lacus ut magna velit eleifend. Amet, quis
									urna, a eu.</p>
								<div class="btn-wrap">
									<a href="#" class="btn btn-outline-accent btn-accent-arrow">Read More<i
											class="icon icon-ns-arrow-right"></i></a>
								</div>
							</div><!--banner-content-->
							<img src="images/main-banner2.jpg" alt="banner" class="banner-image">
						</div><!--slider-item-->

					</div><!--slider-->

					<button class="next slick-arrow">
						<i class="icon icon-arrow-right"></i>
					</button>

				</div>
			</div>
		</div>

	</section>

	<!-- SEARCH SECTION -->
	<section id="search-section" class="py-5" style="background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-10">
					<div style="text-align: center; margin-bottom: 30px;">
						<h2 style="font-size: 32px; font-weight: bold; margin-bottom: 10px;">Tìm Kiếm Sách Yêu Thích</h2>
						<p style="font-size: 16px; color: #666;">Khám phá hàng nghìn cuốn sách từ các tác giả nổi tiếng</p>
					</div>

					<form method="get" action="search.php" style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
						<div class="row g-3">
							<div class="col-md-6">
								<input type="text" name="q" class="form-control" placeholder="🔍 Nhập tên sách hoặc tác giả..." style="padding: 12px; font-size: 14px; border: 2px solid #eee; border-radius: 5px;">
							</div>
							<div class="col-md-3">
								<select name="category" class="form-control" style="padding: 12px; font-size: 14px; border: 2px solid #eee; border-radius: 5px;">
									<option value="all">Tất Cả Danh Mục</option>
									<?php foreach ($categories as $cat): ?>
										<option value="<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-3">
								<button type="submit" class="btn btn-dark w-100" style="padding: 12px; font-size: 14px; border-radius: 5px;">
									<i class="icon icon-search"></i> Tìm Kiếm
								</button>
							</div>
						</div>
					</form>

					<div style="text-align: center; margin-top: 20px;">
						<p style="color: #999; font-size: 13px;">💡 Mẹo: Sử dụng <a href="search.php" style="color: #000; font-weight: bold;">trang tìm kiếm nâng cao</a> để lọc theo tác giả, giá tiền, v.v.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="client-holder" data-aos="fade-up">
		<div class="container">
			<div class="row">
				<div class="inner-content">
					<div class="logo-wrap">
						<div class="grid">
							<a href="#"><img src="images/client-image1.png" alt="client"></a>
							<a href="#"><img src="images/client-image2.png" alt="client"></a>
							<a href="#"><img src="images/client-image3.png" alt="client"></a>
							<a href="#"><img src="images/client-image4.png" alt="client"></a>
							<a href="#"><img src="images/client-image5.png" alt="client"></a>
						</div>
					</div><!--image-holder-->
				</div>
			</div>
		</div>
	</section>

	<section id="featured-books" class="py-5 my-5">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="section-header align-center">
						<div class="title"><span>Some quality items</span></div>
						<h2 class="section-title">Featured Books</h2>
					</div>

					<div class="product-list" data-aos="fade-up">
						<div class="row">
							<?php foreach ($featured_books as $book) { ?>
				<div class="col-md-3 mb-4">
									<div class="product-item">
										<figure class="product-style">
											<img src="./images/<?= htmlspecialchars($book["cover_image"]) ?>.jpg"
												alt="Books" class="product-item">
											<form action="cart.php" method="post" style="display: inline;">
												<input type="hidden" name="book_id"
													value="<?= htmlspecialchars($book['id']) ?>">
												<button type="submit" name="add_to_cart" class="add-to-cart">
													Add to Cart
												</button>
											</form>
										</figure>
										<figcaption>
											<h3><?= htmlspecialchars($book['title']) ?></h3>
											<span><?= htmlspecialchars($book['author_name']) ?></span>
											<div class="item-price">
												<?php if ($book['old_price'] > 0): ?>
													<span class="prev-price">$
														<?= number_format($book['old_price'], 2) ?></span>
												<?php endif; ?>
												$ <?= number_format($book['price'], 2) ?>
											</div>
										</figcaption>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>

					<div class="btn-wrap align-right mt-4">
						<a href="shop.php" class="btn-accent-arrow">View all products <i
								class="icon icon-ns-arrow-right"></i></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section id="best-selling" class="leaf-pattern-overlay">
		<div class="corner-pattern-overlay"></div>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<div class="row">
						<?php 
						// Lấy sách bán chạy nhất
						$bestseller = $db->select("SELECT b.*, a.name AS author_name FROM books b JOIN authors a ON b.author_id = a.id WHERE b.bestseller = 1 LIMIT 1");
						if (!empty($bestseller)) {
							$best = $bestseller[0];
						?>
						<div class="col-md-6">
							<figure class="products-thumb">
								<img src="./images/<?= htmlspecialchars($best['cover_image']) ?>.jpg" alt="book" class="single-image">
							</figure>
						</div>

						<div class="col-md-6">
							<div class="product-entry">
								<h2 class="section-title divider">Best Selling Book</h2>

								<div class="products-content">
									<div class="author-name">By <?= htmlspecialchars($best['author_name']) ?></div>
									<h3 class="item-title"><?= htmlspecialchars($best['title']) ?></h3>
									<p><?= htmlspecialchars(substr($best['description'], 0, 150)) ?>...</p>
									<div class="item-price">$ <?= number_format($best['price'], 2) ?></div>
									<div class="btn-wrap">
										<form action="cart.php" method="post" style="display: inline;">
											<input type="hidden" name="book_id" value="<?= htmlspecialchars($best['id']) ?>">
											<button type="submit" name="add_to_cart" class="btn-accent-arrow" style="background: none; border: none; cursor: pointer; color: inherit;">
												shop it now <i class="icon icon-ns-arrow-right"></i>
											</button>
										</form>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
					<!-- / row -->
				</div>
			</div>
		</div>
	</section>

	<section id="popular-books" class="bookshelf py-5 my-5">
		<!-- Tab lọc thể loại -->
		<ul class="tabs">
			<li class="tab <?= $category == 'all' ? 'active' : '' ?>"><a href="?cat=all">All Genre</a></li>
			<?php foreach ($categories as $cat): ?>
				<li class="tab <?= $category === $cat['slug'] ? 'active' : '' ?>">
					<a href="?cat=<?= $cat['slug'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
				</li>
			<?php endforeach; ?>
		</ul>

		<!-- Danh sách sách -->
		<div class="row">
			<?php foreach ($books as $book): ?>
				<div class="col-md-3 mb-4">
					<div class="product-item">
						<figure class="product-style">
							<img src="images/<?= htmlspecialchars($book['cover_image']) ?>"
								alt="<?= htmlspecialchars($book['title']) ?>" class="product-item">
							<form action="cart.php" method="post">
								<input type="hidden" name="book_id" value="<?= $book['id'] ?>">
								<button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
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
		<nav aria-label="Page navigation">
			<ul class="pagination justify-content-center">
				<?php for ($i = 1; $i <= $total_pages; $i++): ?>
					<li class="page-item <?= $i == $page ? 'active' : '' ?>">
						<a class="page-link" href="?cat=<?= $category ?>&page=<?= $i ?>"><?= $i ?></a>
					</li>
				<?php endfor; ?>
			</ul>
		</nav>
	</section>

	<section id="quotation" class="align-center pb-5 mb-5">
		<div class="inner-content">
			<h2 class="section-title divider">Quote of the day</h2>
			<blockquote data-aos="fade-up">
				<q>“The more that you read, the more things you will know. The more that you learn, the more places
					you’ll go.”</q>
				<div class="author-name">Dr. Seuss</div>
			</blockquote>
		</div>
	</section>

	<section id="special-offer" class="bookshelf pb-5 mb-5">

		<div class="section-header align-center">
			<div class="title">
				<span>Grab your opportunity</span>
			</div>
			<h2 class="section-title">Books with offer</h2>
		</div>

		<div class="container">
			<div class="row">
				<div class="inner-content">
					<div class="product-list" data-aos="fade-up">
						<div class="grid product-grid">
							<?php 
							// Lấy sách có discount
							$discount_books = $db->select("SELECT b.*, a.name AS author_name FROM books b JOIN authors a ON b.author_id = a.id WHERE b.on_sale = 1 LIMIT 5");
							foreach ($discount_books as $book):
							?>
							<div class="product-item">
								<figure class="product-style">
									<img src="./images/<?= htmlspecialchars($book['cover_image']) ?>.jpg" alt="Books" class="product-item">
									<form action="cart.php" method="post" style="display: inline;">
										<input type="hidden" name="book_id" value="<?= htmlspecialchars($book['id']) ?>">
										<button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
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
							<?php endforeach; ?>
						</div><!--grid-->
					</div>
				</div><!--inner-content-->
			</div>
		</div>
	</section>

	<section id="subscribe">
		<div class="container">
			<div class="row justify-content-center">

				<div class="col-md-8">
					<div class="row">

						<div class="col-md-6">

							<div class="title-element">
								<h2 class="section-title divider">Subscribe to our newsletter</h2>
							</div>

						</div>
						<div class="col-md-6">

							<div class="subscribe-content" data-aos="fade-up">
								<p>Sed eu feugiat amet, libero ipsum enim pharetra hac dolor sit amet, consectetur. Elit
									adipiscing enim pharetra hac.</p>
								<form id="form">
									<input type="text" name="email" placeholder="Enter your email addresss here">
									<button class="btn-subscribe">
										<span>send</span>
										<i class="icon icon-send"></i>
									</button>
								</form>
							</div>

						</div>

					</div>
				</div>

			</div>
		</div>
	</section>

	<section id="latest-blog" class="py-5 my-5">
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<div class="section-header align-center">
						<div class="title">
							<span>Read our articles</span>
						</div>
						<h2 class="section-title">Latest Articles</h2>
					</div>

					<div class="row">
						<?php foreach ($posts as $post): ?>
						<div class="col-md-4">

							<article class="column" data-aos="fade-up">

								<figure>
									<a href="#" class="image-hvr-effect">
										<img src="images/<?= htmlspecialchars($post['featured_image']) ?>" alt="post" class="post-image">
									</a>
								</figure>

								<div class="post-item">
									<div class="meta-date"><?= date('M d, Y', strtotime($post['published_at'])) ?></div>
									<h3><a href="#"><?= htmlspecialchars($post['title']) ?></a></h3>

									<div class="links-element">
										<div class="categories">inspiration</div>
										<div class="social-links">
											<ul>
												<li>
													<a href="#"><i class="icon icon-facebook"></i></a>
												</li>
												<li>
													<a href="#"><i class="icon icon-twitter"></i></a>
												</li>
												<li>
													<a href="#"><i class="icon icon-behance-square"></i></a>
												</li>
											</ul>
										</div>
									</div><!--links-element-->

								</div>
							</article>

						</div>
						<?php endforeach; ?>

					</div>

					<div class="row">

						<div class="btn-wrap align-center">
							<a href="#" class="btn btn-outline-accent btn-accent-arrow" tabindex="0">Read All Articles<i
									class="icon icon-ns-arrow-right"></i></a>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>

	<section id="download-app" class="leaf-pattern-overlay">
		<div class="corner-pattern-overlay"></div>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-8">
					<div class="row">

						<div class="col-md-5">
							<figure>
								<img src="images/device.png" alt="phone" class="single-image">
							</figure>
						</div>

						<div class="col-md-7">
							<div class="app-info">
								<h2 class="section-title divider">Download our app now !</h2>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sagittis sed ptibus
									liberolectus nonet psryroin. Amet sed lorem posuere sit iaculis amet, ac urna.
									Adipiscing fames semper erat ac in suspendisse iaculis.</p>
								<div class="google-app">
									<img src="images/google-play.jpg" alt="google play">
									<img src="images/app-store.jpg" alt="app store">
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</section>

	<footer id="footer">
		<div class="container">
			<div class="row">

				<div class="col-md-4">

					<div class="footer-item">
						<div class="company-brand">
							<img src="images/main-logo.png" alt="logo" class="footer-logo">
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sagittis sed ptibus liberolectus
								nonet psryroin. Amet sed lorem posuere sit iaculis amet, ac urna. Adipiscing fames
								semper erat ac in suspendisse iaculis.</p>
						</div>
					</div>

				</div>

				<div class="col-md-2">

					<div class="footer-menu">
						<h5>About Us</h5>
						<ul class="menu-list">
							<li class="menu-item">
								<a href="#">vision</a>
							</li>
							<li class="menu-item">
								<a href="#">articles </a>
							</li>
							<li class="menu-item">
								<a href="#">careers</a>
							</li>
							<li class="menu-item">
								<a href="#">service terms</a>
							</li>
							<li class="menu-item">
								<a href="#">donate</a>
							</li>
						</ul>
					</div>

				</div>
				<div class="col-md-2">

					<div class="footer-menu">
						<h5>Discover</h5>
						<ul class="menu-list">
							<li class="menu-item">
								<a href="#">Home</a>
							</li>
							<li class="menu-item">
								<a href="#">Books</a>
							</li>
							<li class="menu-item">
								<a href="#">Authors</a>
							</li>
							<li class="menu-item">
								<a href="#">Subjects</a>
							</li>
							<li class="menu-item">
								<a href="#">Advanced Search</a>
							</li>
						</ul>
					</div>

				</div>
				<div class="col-md-2">

					<div class="footer-menu">
						<h5>My account</h5>
						<ul class="menu-list">
							<li class="menu-item">
								<a href="#">Sign In</a>
							</li>
							<li class="menu-item">
								<a href="#">View Cart</a>
							</li>
							<li class="menu-item">
								<a href="#">My Wishtlist</a>
							</li>
							<li class="menu-item">
								<a href="#">Track My Order</a>
							</li>
						</ul>
					</div>

				</div>
				<div class="col-md-2">

					<div class="footer-menu">
						<h5>Help</h5>
						<ul class="menu-list">
							<li class="menu-item">
								<a href="#">Help center</a>
							</li>
							<li class="menu-item">
								<a href="#">Report a problem</a>
							</li>
							<li class="menu-item">
								<a href="#">Suggesting edits</a>
							</li>
							<li class="menu-item">
								<a href="#">Contact us</a>
							</li>
						</ul>
					</div>

				</div>

			</div>
			<!-- / row -->

		</div>
	</footer>

	<div id="footer-bottom">
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<div class="copyright">
						<div class="row">

							<div class="col-md-6">
								<p>© 2022 All rights reserved. Free HTML Template by <a
										href="https://www.templatesjungle.com/" target="_blank">TemplatesJungle</a></p>
							</div>

							<div class="col-md-6">
								<div class="social-links align-right">
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

						</div>
					</div><!--grid-->

				</div><!--footer-bottom-content-->
			</div>
		</div>
	</div>

	<script src="js/jquery-1.11.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm"
		crossorigin="anonymous"></script>
	<script src="js/plugins.js"></script>
	<script src="js/script.js"></script>
	<script src="js/search.js"></script>
	
	<script>
	// Xử lý đăng xuất
	function logout() {
		if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
			fetch('auth.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: 'action=logout'
			}).then(() => {
				window.location.href = 'index.php';
			});
		}
	}
	
	// Cập nhật giỏ hàng sau khi thêm sản phẩm
	document.querySelectorAll('form[action="cart.php"] button[name="add_to_cart"]').forEach(btn => {
		btn.addEventListener('click', (e) => {
			e.preventDefault();
			const form = btn.closest('form');
			const formData = new FormData(form);
			formData.append('add_to_cart', true);
			
			fetch('cart.php', {
				method: 'POST',
				body: formData
			}).then(response => response.json())
			.then(data => {
				alert(data.message);
				// Cập nhật số lượng giỏ hàng
				location.reload();
			});
		});
	});
	</script>

</body>

</html>