<?php
require_once 'Views/layouts/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Fresh Garden</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
</head>
<body>
    <div class="section">
        <div class="container">
            <h2>Thanh toán</h2>
            <?php if (empty($cartItems)): ?>
                <p>Giỏ hàng trống. Vui lòng thêm sản phẩm trước khi thanh toán.</p>
            <?php else: ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($cartItems as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo number_format($item['price']); ?> 000 VNĐ</td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo number_format($subtotal); ?> 000 VNĐ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Tổng cộng:</strong></td>
                            <td><strong><?php echo number_format($total); ?> 000 VNĐ</strong></td>
                        </tr>
                        <?php if ($discountPercentage > 0): ?>
                            <tr>
                                <td colspan="3"><strong>Giảm giá (<?php echo $discountPercentage; ?>%):</strong></td>
                                <td><strong>-<?php echo number_format($discountAmount); ?> 000 VNĐ</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>Tổng thanh toán:</strong></td>
                                <td><strong><?php echo number_format($finalTotal); ?> 000 VNĐ</strong></td>
                            </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
                <form method="POST" action="" class="checkout-form">
                    <div class="form-group">
                        <label for="payment_method">Phương thức thanh toán:</label>
                        <select name="payment_method" id="payment_method" required>
                            <option value="VNPAY">VNPAY</option>
                            <option value="Tiền mặt">Tiền mặt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note">Ghi chú:</label>
                        <textarea name="note" id="note" rows="3"></textarea>
                    </div>
                    <button type="submit" class="checkout-btn">Xác nhận thanh toán</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once 'Views/layouts/footer.php'; ?>
</body>
</html>