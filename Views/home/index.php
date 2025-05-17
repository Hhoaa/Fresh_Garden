<?php
require_once 'Views/layouts/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Fresh Garden</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
</head>
<body>
    <?php
    if (isset($_SESSION['logout_success'])) {
        echo "<script>alert('" . addslashes($_SESSION['logout_success']) . "');</script>";
        unset($_SESSION['logout_success']);
    }
    ?>

    <!-- SLIDE SHOW -->
    <div class="section slideshow-section">
        <div class="container">
            <div class="slideshow">
                <div class="slide"><img src="assets/img/slide1.jpg" alt="Slide 1"></div>
                <div class="slide"><img src="assets/img/slide2.jpg" alt="Slide 2"></div>
                <div class="slide"><img src="assets/img/slide3.jpg" alt="Slide 3"></div>
            </div>
        </div>
    </div>

    <!-- DANH MỤC -->
    <div class="section">
        <div class="container">
            <div class="category-list">
                <?php if (empty($categories)): ?>
                    <p>Không có danh mục nào. Vui lòng kiểm tra cơ sở dữ liệu.</p>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <a href="index.php?controller=product&action=listByCategory&id=<?php echo htmlspecialchars($category['category_id']); ?>">
                            <div class="shop">
                                <div class="shop-img">
                                    <?php
                                    $iconMap = [
                                        1 => 'banhmi.png',
                                        2 => 'banhkem.png',
                                        3 => 'banhngot.png',
                                        4 => 'banhkho.png',
                                        5 => 'banhdonglanh.png',
                                        6 => 'douong.png',
                                        7 => 'banhmuavu.png',
                                        8 => 'phukien.png'
                                    ];
                                    $icon = $iconMap[$category['category_id']] ?? 'default.png';
                                    ?>
                                    <img src="assets/icons/<?php echo htmlspecialchars($icon); ?>" alt="<?php echo htmlspecialchars($category['category_name']); ?>" class="shop-icon">
                                </div>
                                <div class="shop-body">
                                    <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- SẢN PHẨM NỔI BẬT -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-title">
                        <h3 class="title">Sản phẩm nổi bật</h3>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="products-slick">
                        <?php if (empty($featuredProducts)): ?>
                            <p>Không có sản phẩm nổi bật nào. Vui lòng kiểm tra cơ sở dữ liệu hoặc cập nhật featured = 1.</p>
                        <?php else: ?>
                            <?php foreach ($featuredProducts as $value): ?>
                                <div class="product">
                                    <div class="product-img">
                                        <img src="<?php echo htmlspecialchars($value['image_url'] ? 'assets/img/' . $value['image_url'] : '../../assets/img/default.jpg'); ?>" alt="<?php echo htmlspecialchars($value['product_name']); ?>">
                                    </div>
                                    <div class="product-body">
                                        <h3 class="product-name">
                                            <a href="index.php?controller=product&action=detail&id=<?php echo htmlspecialchars($value['product_id']); ?>">
                                                <?php echo htmlspecialchars($value['product_name']); ?>
                                            </a>
                                        </h3>
                                        <h4 class="product-price"><?php echo number_format($value['price']); ?> 000 VNĐ</h4>
                                        Đơn vị: <?php echo htmlspecialchars($value['unit']); ?>
                                        <p><?php echo htmlspecialchars($value['description'] ?? ''); ?></p>
                                        <div class="product-btns">
                                            <button class="add-to-wishlist" data-id="<?php echo htmlspecialchars($value['product_id']); ?>"><i class="far fa-heart"></i></button>
                                            <button class="quick-view" data-id="<?php echo htmlspecialchars($value['product_id']); ?>"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="add-to-cart">
                                        <button class="add-to-cart-btn" data-id="<?php echo htmlspecialchars($value['product_id']); ?>"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VIDEO SECTION BEFORE FOOTER -->
    <div class="section video-section">
        <div class="container">
            <video controls>
                <source src="assets/video/video.mp4" type="video/mp4">
                Trình duyệt của bạn không hỗ trợ video.
            </video>
        </div>
    </div>

    <?php require_once 'Views/layouts/footer.php'; ?>

    <!-- Tải cả hai file JavaScript -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/cart.js"></script>
</body>
</html>