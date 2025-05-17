<?php
require_once '../layouts/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng xuất - Fresh Garden</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="section">
        <div class="container">
            <h2>Đăng xuất</h2>
            <?php if (isset($_SESSION['logout_success'])): ?>
                <p class="success"><?php echo htmlspecialchars($_SESSION['logout_success']); ?></p>
                <?php unset($_SESSION['logout_success']); // Xóa thông báo sau khi hiển thị ?>
            <?php endif; ?>
            <div class="logout-actions">
                <a href="http://localhost/BTL_TTCN/" class="login-btn">Quay lại trang chủ</a>
            </div>
        </div>
    </div>

    <?php require_once '../layouts/footer.php'; ?>
</body>
</html>