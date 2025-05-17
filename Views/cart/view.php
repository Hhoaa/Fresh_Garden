<?php require_once 'Views/layouts/header.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Fresh Garden</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/cart.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/cart.js"></script>
</head>
<body>
    <div class="section">
        <div class="container">
            <div class="section-title">
                <h3 class="title">Giỏ hàng của bạn</h3>
            </div>
            <div id="cart-items">
                <?php if (!empty($products)): ?>
                    <div class="cart-list">
                        <ul>
                            <?php foreach ($products as $item): ?>
                                <li>
                                    <img src="<?php echo htmlspecialchars($item['image_url'] ? 'assets/img/' . $item['image_url'] : 'assets/img/default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px;">
                                    <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    <span>Số lượng: 
                                        <input type="number" class="quantity-input" data-id="<?php echo $item['product_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" style="width: 50px;">
                                        <input type="hidden" class="max-stock" data-id="<?php echo $item['product_id']; ?>" value="<?php echo $item['stock_quantity'] ?? 100; ?>">
                                    </span>
                                    <span>Giá: <?php echo number_format($item['unit_price'], 0, ',', '.'); ?> VNĐ</span>
                                    <span>Tổng: <?php echo number_format($item['unit_price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</span>
                                    <button class="remove-from-cart" data-id="<?php echo $item['product_id']; ?>">Xóa</button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p>Tổng cộng: <span id="total-price"><?php echo number_format($totalPrice, 0, ',', '.'); ?> VNĐ</span></p>
                    </div>
                <?php else: ?>
                    <p>Giỏ hàng trống.</p>
                <?php endif; ?>
            </div>
            <div class="cart-actions">
                <button class="vnpay-btn" onclick="window.location.href='index.php?controller=payment&action=vnpayCheckoutFromCart'"><i class="fas fa-credit-card"></i> Thanh toán qua VNPay</button>
            </div>
        </div>
    </div>
    <?php require_once 'Views/layouts/footer.php'; ?>
</body>
</html>