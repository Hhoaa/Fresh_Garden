<?php
require_once 'Views/layouts/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Fresh Garden</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="section">
        <div class="container">
            <h2>Đăng nhập</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form id="login-form" class="login-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn">Đăng nhập</button>
                <p>Chưa có tài khoản? <a href="index.php?controller=user&action=register">Đăng ký ngay</a></p>
            </form>
        </div>
    </div>

    <?php require_once 'Views/layouts/footer.php'; ?>

    <script>
        $(document).ready(function () {
            $('#login-form').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'index.php?controller=user&action=login',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = response.redirect || 'index.php';
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('AJAX Error (login):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                        alert('Lỗi kết nối đến máy chủ (login)! Mã trạng thái: ' + xhr.status + ', Chi tiết: ' + (xhr.statusText || 'Không xác định') + ', Phản hồi: ' + (xhr.responseText || 'Không có'));
                    }
                });
            });
        });
    </script>
</body>
</html>