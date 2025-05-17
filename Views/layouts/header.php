<?php
// Đảm bảo session đã được khởi tạo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra và require file config
if (!function_exists('getDbConnection')) {
    require_once dirname(__DIR__) . '/config/config.php';
}

require_once 'Models/ProductModel.php';

// Lấy số lượng giỏ hàng từ cơ sở dữ liệu nếu người dùng đã đăng nhập
$soluongGiohang = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $db = getDbConnection();
        $productModel = new ProductModel($db);
        $giohang = $productModel->getCartByUserId($_SESSION['user_id']);
        if ($giohang) {
            $soluongGiohang = $productModel->getCartItemsCount($giohang['cart_id']);
        }
    } catch (Exception $e) {
        error_log('Lỗi trong header.php - getCartCount: ' . $e->getMessage());
        $soluongGiohang = 0;
    }
}
?>

<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<header>
    <div class="logo">
        <a href="http://localhost/BTL_TTCN/">FRESH <span>GARDEN</span></a>
    </div>
    <nav>
        <ul>
            <li><a href="http://localhost/BTL_TTCN/views/abouts.php">Giới thiệu</a></li>
            <li><a href="index.php?controller=home&action=stores">Danh sách cửa hàng</a></li>
            <li><a href="index.php?controller=home&action=products">Sản phẩm</a></li>
            <li><a href="index.php?controller=home&action=news">Tin tức</a></li>
        </ul>
    </nav>
    <div class="icons">
        <a href="index.php?controller=home&action=search"><i class="fa-solid fa-magnifying-glass"></i></a>
        <a href="index.php?controller=cart&action=viewCart" class="cart-icon">
            <i class="fa-solid fa-cart-shopping"></i>
            <span id="cart-count"><?php echo htmlspecialchars($soluongGiohang); ?></span>
        </a>
        <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_name'])): ?>
            <div class="user-menu">
                <div class="user-dropdown">
                    <span class="user-name">
                        <i class="fa-solid fa-user"></i>
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        <i class="fa-solid fa-caret-down"></i>
                    </span>
                    <div class="dropdown-content">
                        <a href="index.php?controller=user&action=profile"><i class="fa-solid fa-address-card"></i> Thông tin tài khoản</a>
                        <a href="index.php?controller=order&action=history"><i class="fa-solid fa-box"></i> Quản lý đơn hàng</a>
                        <a href="index.php?controller=user&action=logout"><i class="fa-solid fa-sign-out-alt"></i> Đăng xuất</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="index.php?controller=user&action=login" class="login-button"><i class="fa-solid fa-sign-in-alt"></i> Đăng nhập</a>
            <a href="index.php?controller=user&action=register" class="register-button"><i class="fa-solid fa-user-plus"></i> Đăng ký</a>
        <?php endif; ?>
    </div>
</header>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_SESSION['user_id'])): ?>
            $.ajax({
                url: 'index.php?controller=cart&action=getCartDetails',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const soluongGiohang = response.items ? response.items.length : 0;
                        $('#cart-count').text(soluongGiohang);
                    } else {
                        console.log('Lấy chi tiết giỏ hàng thất bại:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Lỗi AJAX (getCartDetails):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                }
            });
        <?php endif; ?>
    });
</script>