<?php
require_once 'Views/layouts/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết sản phẩm - <?php echo htmlspecialchars($product['product_name'] ?? ''); ?> - Fresh Garden</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/cart.js"></script> <!-- Thêm file cart.js -->
</head>
<body>
    <!-- CHI TIẾT SẢN PHẨM -->
    <div class="section">
        <div class="container">
            <div class="product-detail">
                <div class="product-detail-img">
                    <img src="<?php echo htmlspecialchars($product['image_url'] ? 'assets/img/' . $product['image_url'] : 'assets/img/default.jpg'); ?>" 
                         onerror="this.src='assets/img/default.jpg'" 
                         alt="<?php echo htmlspecialchars($product['product_name'] ?? 'Không có tên'); ?>">
                </div>
                <div class="product-detail-body">
                    <h2><?php echo htmlspecialchars($product['product_name'] ?? 'Không có tên'); ?></h2>
                    <p><strong>Danh mục:</strong> <?php echo htmlspecialchars($category_name); ?></p>
                    <p><strong>Giá:</strong> <?php echo number_format($product['price'] ?? 0); ?> 000 VNĐ</p>
                    <p><strong>Đơn vị:</strong> <?php echo htmlspecialchars($product['unit'] ?? 'cái'); ?></p>
                    <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($product['description'] ?? 'Không có mô tả'); ?></p>
                  
                    <div class="add-to-cart">
                        <div class="quantity-control">
                            <label for="quantity"><strong>Số lượng:</strong></label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity'] ?? 0; ?>">
                        </div>
                        <button class="add-to-cart-btn" data-id="<?php echo htmlspecialchars($product['product_id'] ?? ''); ?>">
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                        </button>
                        <button class="order-btn" data-id="<?php echo htmlspecialchars($product['product_id'] ?? ''); ?>">
                            <i class="fas fa-check"></i> Đặt hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SẢN PHẨM LIÊN QUAN -->
    <div class="section">
        <div class="container">
            <div class="section-title">
                <h3 class="title">Sản phẩm liên quan</h3>
            </div>
            <div class="product-grid">
                <?php if (empty($relatedProducts)): ?>
                    <p>Không có sản phẩm liên quan.</p>
                <?php else: ?>
                    <?php foreach ($relatedProducts as $related): ?>
                        <div class="product">
                            <div class="product-img">
                                <img src="<?php echo htmlspecialchars($related['image_url'] ? 'assets/img/' . $related['image_url'] : 'assets/img/default.jpg'); ?>" 
                                     onerror="this.src='assets/img/default.jpg'" 
                                     alt="<?php echo htmlspecialchars($related['product_name'] ?? 'Không có tên'); ?>">
                            </div>
                            <div class="product-body">
                                <h3 class="product-name">
                                    <a href="index.php?controller=product&action=detail&id=<?php echo htmlspecialchars($related['product_id'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($related['product_name'] ?? 'Không có tên'); ?>
                                    </a>
                                </h3>
                                <h4 class="product-price"><?php echo number_format($related['price'] ?? 0); ?> 000 VNĐ</h4>
                                Đơn vị: <?php echo htmlspecialchars($related['unit']); ?>
                                <p><?php echo htmlspecialchars($related['description'] ?? ''); ?></p>
                                <div class="product-btns">
                                    <button class="add-to-wishlist" data-id="<?php echo htmlspecialchars($related['product_id'] ?? ''); ?>"><i class="far fa-heart"></i></button>
                                    <button class="quick-view" data-id="<?php echo htmlspecialchars($related['product_id'] ?? ''); ?>"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <div class="add-to-cart">
                                <button class="add-to-cart-btn" data-id="<?php echo htmlspecialchars($related['product_id'] ?? ''); ?>">
                                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require_once 'Views/layouts/footer.php'; ?>
</body>
</html>