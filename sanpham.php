<?php include 'header.php'; ?>

<!-- Liên kết CSS từ thư mục /css -->
<link rel="stylesheet" href="css/style.css">

<!-- Banner -->
<div class="banner">
    <img src="image/col_all_banner.jpg" >
</div>

<main>
    <!-- Bộ lọc sản phẩm -->
    <section class="filter">
        <h2>Phân loại sản phẩm</h2>
        <ul>
            <li><a href="#">Bánh mì mặn</a></li>
            <li><a href="#">Bánh sinh nhật</a></li>
            <li><a href="#">Bánh mì ngọt</a></li>
            <li><a href="#">Hộp quà Tết</a></li>
            <li><a href="#">Bánh ngọt</a></li>
            <li><a href="#">Bánh mì kẹp</a></li>
            <li><a href="#">Bánh tráng miệng</a></li>
            <li><a href="#">Bánh khô ngọt</a></li>
            <li><a href="#">Phụ kiện bánh kem</a></li>
            <li><a href="#">Bánh pizza</a></li>
            <li><a href="#">Bánh mì gối</a></li>
            <li><a href="#">Bánh cuộn</a></li>
            <li><a href="#">Bánh kem Noel</a></li>
            <li><a href="#">Bánh bao</a></li>
            <li><a href="#">Cà phê</a></li>
            <li><a href="#">Bánh khô mặn</a></li>
            <li><a href="#">Bánh trường học</a></li>
            <li><a href="#">Nước uống đóng chai</a></li>
            <li><a href="#">Bánh cán lớp</a></li>
        </ul>
    </section>

    <!-- Danh sách sản phẩm -->
    <section class="products">
        <h2 class="title">Tất cả sản phẩm</h2>
        <div class="sort">
            <label for="sort">Sắp xếp theo:</label>
            <select id="sort" onchange="sortProducts(this.value)">
                <option value="newest">Mới nhất</option>
                <option value="price_asc">Giá tăng dần</option>
                <option value="price_desc">Giá giảm dần</option>
            </select>
        </div>

        <div class="product-list">
        <a href="chitietsanpham.php" class="product">
    <div class="product-card">
        <div class="product-img">
            <img src="image/pizza-cheese.jpg" class="front">
            <img src="image/pizza-hawaiian.jpg" class="back">
        </div>
        <p class="product-name">Pizza phô mai</p>
        <p class="product-price">100.000 đ</p>
    </div>
</a>

            <div class="product"><div class="product-card"><div class="product-img"><img src="image/pizza-hawaiian.jpg" class="front"><img src="image/pizza-seafood.jpg" class="back"></div><p class="product-name">Pizza dứa</p><p class="product-price">120.000 đ</p></div></div>
            <div class="product"><div class="product-card"><div class="product-img"><img src="image/pizza-seafood.jpg" class="front"><img src="image/pizza-cheese.jpg" class="back"></div><p class="product-name">Pizza hải sản</p><p class="product-price">150.000 đ</p></div></div>
            <div class="product"><div class="product-card"><div class="product-img"><img src="image/pizza-cheese.jpg" class="front"><img src="image/pizza-hawaiian.jpg" class="back"></div><p class="product-name">Pizza phô mai</p><p class="product-price">100.000 đ</p></div></div>
            <div class="product"><div class="product-card"><div class="product-img"><img src="image/pizza-hawaiian.jpg" class="front"><img src="image/pizza-seafood.jpg" class="back"></div><p class="product-name">Pizza dứa</p><p class="product-price">120.000 đ</p></div></div>
            <div class="product"><div class="product-card"><div class="product-img"><img src="image/pizza-seafood.jpg" class="front"><img src="image/pizza-cheese.jpg" class="back"></div><p class="product-name">Pizza hải sản</p><p class="product-price">150.000 đ</p></div></div>
            <div class="product"><div class="product-card"><div class="product-img"><img src="image/pizza-cheese.jpg" class="front"><img src="image/pizza-hawaiian.jpg" class="back"></div><p class="product-name">Pizza phô mai</p><p class="product-price">100.000 đ</p></div></div>
            <div class="product"><div class="product-card"><div class="product-img"><img src="image/pizza-hawaiian.jpg" class="front"><img src="image/pizza-seafood.jpg" class="back"></div><p class="product-name">Pizza dứa</p><p class="product-price">120.000 đ</p></div></div>
            <div class="product"><div class="product-card"><div class="product-img"><img src="image/pizza-seafood.jpg" class="front"><img src="image/pizza-cheese.jpg" class="back"></div><p class="product-name">Pizza hải sản</p><p class="product-price">150.000 đ</p></div></div>
        </div>

        <!-- Phân trang -->
        <div class="pagination">
            <a href="#" class="active">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
        </div>
    </section>
</main>

