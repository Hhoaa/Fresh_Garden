
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Giới Thiệu</title>
    <link rel="stylesheet" href="../assets/css/abouts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body>
    <?php require_once '../Views/layouts/header.php'; ?>
    <div class="container">
        <div class="section">
            <img src="../assets/img/about.jpg" alt="Bánh ngọt Fresh Garden">
            <p>Fresh Garden là thương hiệu bánh ngọt hàng đầu với nguyên liệu tự nhiên và hương vị tinh tế.</p>
        </div>
        <div class="section">
            <img src="../assets/img/about1.jpg" alt="Bánh tươi ngon">
            <p>Chúng tôi cam kết mang đến cho khách hàng những chiếc bánh tươi ngon, an toàn và chất lượng cao.</p>
        </div>
        <div class="section">
            <img src="../assets/img/about2.jpg" alt="Cửa hàng Fresh Garden">
            <p>Hãy đến với Fresh Garden để thưởng thức những hương vị tuyệt vời!</p>
        </div>
    </div>

    <!-- Banner quảng cáo -->
    <div class="banner" id="banner">
        <div class="banner-content">
            <button class="close-btn" onclick="closeBanner()">×</button>
                <h2>Khuyến mãi đặc biệt!</h2>
            <img src="../assets/img/banner.jpg" alt="Khuyến mãi đặc biệt">
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
    <?php require_once '../Views/layouts/footer.php'; ?>
</body>
</html>