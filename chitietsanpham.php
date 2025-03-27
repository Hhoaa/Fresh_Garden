<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm - Pizza Phô Mai</title>
    <link rel="stylesheet" href="css/chitiet.css">
</head>
<body>
<?php include 'header.php'; ?>

    <!-- Container sản phẩm -->
    <div class="product-detail-container">
        <div class="product-images">
            <!-- Danh sách ảnh thu nhỏ -->
            <div class="thumbnail-list">
                <img src="image/pizza-cheesse-1.jpg" class="thumbnail active">
                <img src="image/pizza-seafood.jpg" class="thumbnail">
                <img src="image/pizza-hawaiian.jpg" class="thumbnail">
            </div>
            <!-- Ảnh chính -->
            <div class="main-image">
                <img src="image/pizza-cheese.jpg" id="main-img">
            </div>
        </div>

        <div class="product-info">
            <h1>Pizza Phô Mai</h1>
            <p class="product-category">Loại: Pizza | Mã SP: PZ001</p>
            <p class="product-price">100,000₫</p>

            <label>Size</label>
            <button class="option-button">Nhỏ</button>
            <button class="option-button">Vừa</button>
            <button class="option-button">Lớn</button>

            <div class="quantity">
                <button class="btn-quantity" onclick="changeQuantity(-1)">-</button>
                <input type="text" value="1" id="quantity">
                <button class="btn-quantity" onclick="changeQuantity(1)">+</button>
                <button class="add-to-cart">Thêm vào giỏ</button>
            </div>

            <button class="buy-now">Mua ngay</button>

            <div class="free-shipping">
                <img src="image/free-shipping.png">
                <p>Miễn phí giao hàng cho đơn từ 400k khi đặt qua fanpage.</p>
            </div>
        </div>
    </div>

    <!-- Thông tin sản phẩm -->
    <div class="product-description">
        <h2>Thông tin sản phẩm</h2>
        <p>Pizza phô mai với lớp phô mai tan chảy béo ngậy, kết hợp với đế bánh giòn xốp, mang đến hương vị tuyệt hảo. Phù hợp cho những ai yêu thích hương vị phô mai đậm đà!</p>
    </div>

    <!-- Sản phẩm liên quan -->
    <div class="related-products">
        <h2>Sản phẩm liên quan</h2>
        <div class="product-list">
            <div class="related-item">
                <img src="image/pizza-seafood.jpg">
                <p>Pizza Hải Sản</p>
            </div>
            <div class="related-item">
                <img src="image/pizza-hawaiian.jpg">
                <p>Pizza Hawaiian</p>
            </div>
            <div class="related-item">
                <img src="image/pizza-cheesse-1.jpg">
                <p>Pizza Phô Mai Đặc Biệt</p>
            </div>
            <div class="related-item">
                <img src="image/pizza-seafood.jpg">
                <p>Pizza Hải Sản</p>
            </div>
            <div class="related-item">
                <img src="image/pizza-hawaiian.jpg">
                <p>Pizza Hawaiian</p>
            </div>
            <div class="related-item">
                <img src="image/pizza-cheesse-1.jpg">
                <p>Pizza Phô Mai Đặc Biệt</p>
            </div>
            <div class="related-item">
                <img src="image/pizza-hawaiian.jpg">
                <p>Pizza Hawaiian</p>
            </div>
            <div class="related-item">
                <img src="image/pizza-cheesse-1.jpg">
                <p>Pizza Phô Mai Đặc Biệt</p>
        </div>
    </div>

    <script src="chitiet.js"></script>
</body>
</html>
