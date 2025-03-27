<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        /* Reset chung */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 50px;
            background: white;
            border-bottom: 2px solid #eee;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .logo a {
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
            color: #8BC34A;
        }

        .logo span {
            color: #C3D900;
        }

        nav ul {
            list-style: none;
            display: flex;
        }

        nav ul li {
            position: relative;
            margin: 0 15px;
        }

        nav ul li a {
            text-decoration: none;
            color: black;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            transition: 0.3s;
        }

        nav ul li a:hover {
            color: red;
        }

        /* Icon giỏ hàng & tìm kiếm */
        .icons {
            display: flex;
            align-items: center;
        }

        .icons a {
            text-decoration: none;
            color: black;
            font-size: 18px;
            margin: 0 10px;
        }

        /* Widget liên hệ */
        .contact-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        }

        .contact-widget a {
            width: 50px;
            height: 50px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            color: white;
            font-size: 24px;
            text-decoration: none;
            transition: 0.3s;
        }

        .call-button { background: red; }
        .zalo-button { background: #0088cc; }
        .messenger-button { background: #0078ff; }
        .scroll-top-button { background: #4a90e2; }

        /* Nút bật chat AI */
        .chat-icon {
            position: fixed;
            bottom: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
            background: #28a745;
            color: white;
            font-size: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .chat-icon:hover {
            background: #1e7a37;
        }

        /* Ẩn chatbot khi chưa mở */
        .chat-container {
            display: none;
            width: 350px;
            position: fixed;
            bottom: 80px;
            left: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .chat-header {
            background: #28a745;
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-header img {
            height: 30px;
            width: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .chat-body {
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
        }

        .chat-message {
            background: #e9f5e9;
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
            margin: 5px 0;
        }

        .chat-footer {
            display: flex;
            padding: 10px;
            background: #f1f1f1;
        }

        .chat-footer input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .chat-footer button {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            margin-left: 5px;
            cursor: pointer;
        }

        /* Nút đóng chat */
        .close-chat {
            background: none;
            border: none;
            font-size: 16px;
            color: white;
            cursor: pointer;
            margin-left: auto;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php">FRESH <span>GARDEN</span></a>
    </div>
    <nav>
        <ul>
            <li><a href="aboutus.php">Giới thiệu</a></li>
            <li><a href="dscuahang.php">Danh sách cửa hàng</a></li>
            <li><a href="sanpham.php">Sản phẩm</a></li>
            <li><a href="#">Tin tức</a></li>
        </ul>
    </nav>
    <div class="icons">
        <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>
        <a href="#"><i class="fa-solid fa-cart-shopping"></i></a>
    </div>
</header>

<!-- Widget Liên hệ -->
<div class="contact-widget">
    <a href="tel:0123456789" class="call-button"><i class="fa-solid fa-phone"></i></a>
    <a href="https://zalo.me/yourzalo" class="zalo-button"><i class="fa-solid fa-comment"></i></a>
    <a href="https://m.me/yourmessenger" class="messenger-button"><i class="fa-brands fa-facebook-messenger"></i></a>
    <a href="#" class="scroll-top-button" onclick="scrollToTop()"><i class="fa-solid fa-chevron-up"></i></a>
</div>

<!-- Nút bật chatbot -->
<div class="chat-icon" onclick="toggleChat()">
    <i class="fa-solid fa-comment-dots"></i>
</div>

<!-- Chatbot AI -->
<div class="chat-container">
    <div class="chat-header">
        <img src="image/croissant.webp" alt="Freshgarden Logo">
        <span>Trợ lý AI - Freshgarden</span>
        <button class="close-chat" onclick="toggleChat()">✖</button>
    </div>
    <div class="chat-body"></div>
    <div class="chat-footer">
        <input type="text" id="chat-input" placeholder="Nhập tin nhắn...">
        <button onclick="sendMessage()">Gửi</button>
    </div>
</div>

<script>
    function toggleChat() {
        var chatBox = document.querySelector(".chat-container");
        chatBox.style.display = (chatBox.style.display === "none" || chatBox.style.display === "") ? "block" : "none";
    }

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>

</body>
</html>
