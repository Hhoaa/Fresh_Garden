<?php require_once 'Views/layouts/header.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả thanh toán VNPay - Fresh Garden</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="section">
        <div class="container">
            <h2>Kết quả thanh toán</h2>
            <?php if ($data['success']): ?>
                <p style="color: green;"><?php echo htmlspecialchars($data['message']); ?></p>
                <p><strong>Mã giao dịch:</strong> <?php echo htmlspecialchars($data['transaction_id']); ?></p>
                <p><strong>Số tiền:</strong> <?php echo number_format($data['amount'], 0, ',', '.'); ?> VNĐ</p>
                <p><strong>Thông tin đơn hàng:</strong> <?php echo htmlspecialchars($data['order_info']); ?></p>
            <?php else: ?>
                <p style="color: red;"><?php echo htmlspecialchars($data['message']); ?></p>
            <?php endif; ?>
            <a href="index.php?controller=home&action=index" class="btn">Quay về trang chủ</a>
        </div>
    </div>
    <?php require_once 'Views/layouts/footer.php'; ?>
</body>
</html>