<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới Thiệu - Fresh Garden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            background-image: url('image/croissant.webp'); /* Thay bằng đường dẫn hình mờ */
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 24px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.9); /* Làm nền trong suốt nhẹ để dễ đọc chữ */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .section img {
            width: 40%;
            border-radius: 10px;
            margin: 10px;
        }
        .section p {
            width: 55%;
            padding: 10px;
        }
        .section:nth-child(even) {
            flex-direction: row-reverse;
        }
        .footer {
            text-align: center;
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            margin-top: 20px;
        }

        /* Banner quảng cáo */
        .banner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .banner-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            position: relative;
            max-width: 500px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .banner img {
            width: 100%;
            border-radius: 10px;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
<?php include 'header.php'; ?>  <!-- Nhúng file header -->
    <div class="header">Giới Thiệu Fresh Garden</div>
    <div class="container">
        <div class="section">
            <img src="image/croissant.webp" alt="Bánh ngọt Fresh Garden">
            <p>Fresh Garden là thương hiệu bánh ngọt hàng đầu với nguyên liệu tự nhiên và hương vị tinh tế.</p>
        </div>
        <div class="section">
            <img src="image/croissant.webp" alt="Bánh tươi ngon">
            <p>Chúng tôi cam kết mang đến cho khách hàng những chiếc bánh tươi ngon, an toàn và chất lượng cao.</p>
        </div>
        <div class="section">
            <img src="image/croissant.webp" alt="Cửa hàng Fresh Garden">
            <p>Hãy đến với Fresh Garden để thưởng thức những hương vị tuyệt vời!</p>
        </div>
    </div>
    <div class="footer">&copy; 2025 Fresh Garden. Mọi quyền được bảo lưu.</div>

     <!-- Banner quảng cáo -->
     <div class="banner" id="banner">
        <div class="banner-content">
            <button class="close-btn" onclick="closeBanner()">×</button>
            <img src="image/banner-ads.jpg" alt="Khuyến mãi đặc biệt">
            <h2>Khuyến mãi đặc biệt!</h2>
            <p>Giảm giá 50% cho tất cả các loại bánh hôm nay!</p>
        </div>
    </div>

    <script>
        // Hiển thị banner khi trang tải xong
        window.onload = function() {
            document.getElementById("banner").style.display = "flex";
        };

        // Hàm đóng banner
        function closeBanner() {
            document.getElementById("banner").style.display = "none";
        }
    </script>
</body>
</html>