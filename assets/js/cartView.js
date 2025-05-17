$(document).ready(function() {
    // Thanh toán thường
    $('#place-order-btn').on('click', function() {
        $.ajax({
            url: 'index.php?controller=cart&action=placeOrder',
            method: 'POST',
            data: { source: 'cart' },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = 'index.php?controller=user&action=orderHistory';
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Lỗi khi gửi yêu cầu!');
            }
        });
    });

    // Thanh toán qua VNPay
    $('#vnpay-order-btn').on('click', function() {
        $.ajax({
            url: 'index.php?controller=order&action=createVnpayUrl',
            method: 'POST',
            data: { source: 'cart' },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.url; // Chuyển hướng đến VNPay
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Lỗi khi gửi yêu cầu!');
            }
        });
    });
});