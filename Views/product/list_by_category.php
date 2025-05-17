<?php
require_once 'config/config.php';
require_once 'Views/layouts/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh mục - <?php echo htmlspecialchars($category_name ?? ''); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/cart.js"></script>
</head>
<body>
    <div class="section">
        <div class="container">
            <div class="category-horizontal">
                <h3 class="category-title">Danh mục sản phẩm</h3>
                <div class="category-list-horizontal">
                    <?php foreach ($categories as $cat): ?>
                        <a href="index.php?controller=product&action=listByCategory&id=<?php echo htmlspecialchars($cat['category_id']); ?>" 
                           class="category-item <?php echo (isset($_GET['id']) && $_GET['id'] == $cat['category_id']) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat['category_name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="section-title">
                <h3 class="title">Danh mục: <?php echo htmlspecialchars($category_name ?? ''); ?></h3>
            </div>

            <div class="product-grid">
                <?php if (empty($products)): ?>
                    <p>Không có sản phẩm nào trong danh mục này.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product">
                            <div class="product-img">
                                <img src="<?php echo htmlspecialchars($product['image_url'] ? 'assets/img/' . $product['image_url'] : 'assets/img/default.jpg'); ?>" 
                                     onerror="this.src='assets/img/default.jpg'" 
                                     alt="<?php echo htmlspecialchars($product['product_name'] ?? 'Không có tên'); ?>">
                            </div>
                            <div class="product-body">
                                <h3 class="product-name">
                                    <a href="index.php?controller=product&action=detail&id=<?php echo htmlspecialchars($product['product_id'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($product['product_name'] ?? 'Không có tên'); ?>
                                    </a>
                                </h3>
                                <h4 class="product-price"><?php echo number_format($product['price'] ?? 0); ?> 000 VNĐ</h4>
                                 Đơn vị: <?php echo htmlspecialchars($product['unit']); ?>
                                <p><?php echo htmlspecialchars($product['description'] ?? ''); ?></p>
                                <div class="product-btns">
                                    <button class="add-to-wishlist" data-id="<?php echo htmlspecialchars($product['product_id'] ?? ''); ?>"><i class="far fa-heart"></i></button>
                                    <button class="quick-view" data-id="<?php echo htmlspecialchars($product['product_id'] ?? ''); ?>"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <div class="add-to-cart">
                                <button class="add-to-cart-btn" data-id="<?php echo htmlspecialchars($product['product_id'] ?? ''); ?>">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php echo $paginationHtml; ?>
        </div>
    </div>

    <?php require_once 'Views/layouts/footer.php'; ?>
</body>
</html>