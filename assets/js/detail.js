$(document).ready(function() {
    // Thanh toán thường
    $('.order-btn').on('click', function() {
        const productId = $(this).data('id');
        const quantity = $('#quantity').val();
        if (!quantity || quantity <= 0) {
            alert('Vui lòng chọn số lượng hợp lệ!');
            return;
        }

        $.ajax({
            url: 'index.php?controller=cart&action=placeOrder',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                source: 'detail'
            },
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
    $('.vnpay-btn').on('click', function() {
        const productId = $(this).data('id');
        const quantity = $('#quantity').val();
        if (!quantity || quantity <= 0) {
            alert('Vui lòng chọn số lượng hợp lệ!');
            return;
        }

        $.ajax({
            url: 'index.php?controller=order&action=createVnpayUrl',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                source: 'detail'
            },
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