<?php
header("Content-Type: text/plain");

$user_message = $_POST['message'] ?? '';

$responses = [
    "chào" => "Xin chào! Freshgarden rất vui được phục vụ bạn 🥐🍰",
    "bánh mới" => "Chúng tôi vừa ra mắt Bánh Sô-cô-la Lava! Bạn có muốn thử không? 🍫",
    "khuyến mãi" => "Hiện tại Freshgarden đang có chương trình giảm giá 20% cho đơn hàng trên 200k! 🎉",
    "đặt hàng" => "Bạn có thể đặt hàng trực tiếp trên website hoặc gọi hotline: 1900.1234 📞",
];

$bot_reply = "Anh/ chị vui lòng chờ giây lát, cửa hàng sẽ phản hồi lại ngay.";
foreach ($responses as $key => $response) {
    if (stripos($user_message, $key) !== false) {
        $bot_reply = $response;
        break;
    }
}

echo $bot_reply;
?>