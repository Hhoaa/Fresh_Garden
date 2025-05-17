<?php
require_once 'Views/layouts/header.php';
?>
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/header.css">
<link rel="stylesheet" href="assets/css/footer.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="container mt-5">
    <h2 class="text-center">Thanh toán VNPay</h2>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h4>Thông tin sản phẩm</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($data['cart_items']) && !empty($data['cart_items'])): ?>
                        <?php foreach ($data['cart_items'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo number_format($item['unit_price'], 0, ',', '.') . ' VNĐ'; ?></td>
                                <td><?php echo number_format(($item['discounted_price'] ?? $item['unit_price']) * $item['quantity'], 0, ',', '.') . ' VNĐ'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php elseif (isset($data['product']) && $data['product']): ?>
                        <?php
                        $product = $data['product'];
                        $quantity = $data['quantity'];
                        $total = $data['total_price'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($quantity); ?></td>
                            <td><?php echo number_format($product['price'], 0, ',', '.') . ' VNĐ'; ?></td>
                            <td><?php echo number_format($total, 0, ',', '.') . ' VNĐ'; ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không có sản phẩm để thanh toán!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <h4>Tổng tiền: <?php echo number_format($data['total_price'] ?? 0, 0, ',', '.') . ' VNĐ'; ?></h4>

            <form action="index.php?controller=payment&action=processVnpay" method="post">
                <?php if (isset($data['cart_id'])): ?>
                    <input type="hidden" name="cart_id" value="<?php echo htmlspecialchars($data['cart_id']); ?>">
                <?php endif; ?>
                <?php if (isset($data['product']['product_id'])): ?>
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($data['product']['product_id']); ?>">
                    <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($data['quantity']); ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="discount_code">Mã giảm giá (nếu có):</label>
                    <input type="text" name="discount_code" id="discount_code" class="form-control" placeholder="Nhập mã giảm giá">
                </div>
                <button type="submit" class="btn btn-primary mt-3">Đặt hàng</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once 'Views/layouts/footer.php';
?>