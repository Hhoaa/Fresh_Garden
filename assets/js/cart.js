$(document).ready(function () {
    // Hàm hiển thị danh sách sản phẩm trong giỏ hàng
    function displayCartItems(items, totalPrice = null, discountPercentage = null) {
        var cartList = $('<div class="cart-list"></div>');
        if (items.length === 0) {
            cartList.append('<p>Giỏ hàng trống.</p>');
        } else {
            var ul = $('<ul></ul>');
            items.forEach(function (item) {
                var imageUrl = item.image_url ? 'assets/img/' + item.image_url : 'assets/img/default.jpg';
                var itemTotal = item.unit_price * item.quantity;
                ul.append(`
                    <li data-product-id="${item.product_id}">
                        <img src="${imageUrl}" alt="${item.product_name}" style="width: 50px;">
                        <span>${item.product_name}</span>
                        <span>Số lượng: 
                            <input type="number" class="quantity-input" data-id="${item.product_id}" value="${item.quantity}" min="1" style="width: 50px;">
                            <input type="hidden" class="max-stock" data-id="${item.product_id}" value="${item.stock_quantity || 100}">
                        </span>
                        <span>Giá: ${new Intl.NumberFormat('vi-VN').format(item.unit_price)} VNĐ</span>
                        <span>Tổng: ${new Intl.NumberFormat('vi-VN').format(itemTotal)} VNĐ</span>
                        <button class="remove-from-cart" data-id="${item.product_id}">Xóa</button>
                    </li>
                `);
                // Lưu số lượng gốc vào data attribute
                $(`li[data-product-id="${item.product_id}"]`).data('original-quantity', item.quantity);
            });
            cartList.append(ul);
            var displayTotal = totalPrice || items.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
            cartList.append(`<p>Tổng cộng: <span id="total-price">${new Intl.NumberFormat('vi-VN').format(displayTotal)} VNĐ</span></p>`);
            if (discountPercentage) {
                cartList.append(`<p>Giảm giá: ${discountPercentage}% - Tổng sau giảm: ${new Intl.NumberFormat('vi-VN').format(totalPrice)} VNĐ</p>`);
            }
        }

        $('#cart-items').html(cartList);
    }

    // Hàm cập nhật số lượng giỏ hàng trên giao diện
    function updateCartCount(items) {
        if ($('#cart-count').length) {
            var totalQuantity = items.reduce((sum, item) => sum + item.quantity, 0);
            $('#cart-count').text(totalQuantity);
        }
    }

    // Thêm sản phẩm vào giỏ hàng
    $('.add-to-cart-btn').on('click', function () {
        var productId = $(this).data('id');
        var quantity = parseInt($('#quantity').val()) || 1;

        if (quantity <= 0) {
            alert('Số lượng phải lớn hơn 0!');
            $('#quantity').val(1);
            return;
        }

        $.ajax({
            url: 'index.php?controller=cart&action=addToCart',
            method: 'POST',
            data: { product_id: productId, quantity: quantity },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    updateCartCount(response.items);
                    displayCartItems(response.items, response.total_price);
                } else {
                    alert(response.message);
                    if (response.message.includes('Vui lòng đăng nhập')) {
                        window.location.href = 'index.php?controller=user&action=login';
                    }
                }
            },
            error: function (xhr) {
                console.log('AJAX Error (addToCart):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                alert('Lỗi kết nối đến máy chủ! Mã trạng thái: ' + xhr.status + ', Chi tiết: ' + (xhr.statusText || 'Không xác định') + ', Phản hồi: ' + (xhr.responseText || 'Không có'));
            }
        });
    });

    // Xử lý nút Đặt hàng
    $('.order-btn').on('click', function () {
        var productId = $(this).data('id');
        var quantity = parseInt($('#quantity').val()) || 1;

        if (quantity <= 0) {
            alert('Số lượng phải lớn hơn 0!');
            $('#quantity').val(1);
            return;
        }

        $.ajax({
            url: 'index.php?controller=cart&action=checkLogin',
            method: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.loggedIn) {
                    $.ajax({
                        url: 'index.php?controller=cart&action=addToCart',
                        method: 'POST',
                        data: { product_id: productId, quantity: quantity },
                        dataType: 'json',
                        success: function (addResponse) {
                            if (addResponse.success) {
                                window.location.href = 'index.php?controller=cart&action=viewCart';
                            } else {
                                alert(addResponse.message);
                            }
                        },
                        error: function (xhr) {
                            console.log('AJAX Error (addToCart):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                            alert('Lỗi kết nối đến máy chủ! Mã trạng thái: ' + xhr.status + ', Chi tiết: ' + (xhr.statusText || 'Không xác định') + ', Phản hồi: ' + (xhr.responseText || 'Không có'));
                        }
                    });
                } else {
                    window.location.href = 'index.php?controller=user&action=login&redirect=cart&product_id=' + productId + '&quantity=' + quantity;
                }
            },
            error: function (xhr) {
                console.log('AJAX Error (checkLogin):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                alert('Lỗi kiểm tra đăng nhập! Mã trạng thái: ' + xhr.status + ', Chi tiết: ' + (xhr.statusText || 'Không xác định') + ', Phản hồi: ' + (xhr.responseText || 'Không có'));
            }
        });
    });

    // Cập nhật số lượng sản phẩm
    $(document).on('change', '.quantity-input', function () {
        var productId = $(this).data('id');
        var quantity = parseInt($(this).val());
        var maxStock = parseInt($('.max-stock[data-id="' + productId + '"]').val());
        var originalQuantity = $(this).closest('li').data('original-quantity') || 1;

        if (isNaN(quantity) || quantity <= 0) {
            alert('Số lượng phải lớn hơn 0!');
            $(this).val(originalQuantity);
            return;
        }

        if (quantity > maxStock) {
            alert('Số lượng vượt quá tồn kho (' + maxStock + ')!');
            $(this).val(originalQuantity);
            return;
        }

        // Kiểm tra đăng nhập trước khi cập nhật
        $.ajax({
            url: 'index.php?controller=cart&action=checkLogin',
            method: 'POST',
            dataType: 'json',
            success: function (loginResponse) {
                if (!loginResponse.loggedIn) {
                    alert('Vui lòng đăng nhập để cập nhật số lượng!');
                    window.location.href = 'index.php?controller=user&action=login';
                    return;
                }

                // Tiến hành cập nhật số lượng
                $.ajax({
                    url: 'index.php?controller=cart&action=updateQuantity',
                    method: 'POST',
                    data: { product_id: productId, quantity: quantity },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            updateCartCount(response.items);
                            displayCartItems(response.items, response.total_price);
                        } else {
                            alert('Lỗi: ' + response.message);
                            $('.quantity-input[data-id="' + productId + '"]').val(originalQuantity);
                        }
                    },
                    error: function (xhr) {
                        console.log('AJAX Error (updateQuantity):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                        alert('Lỗi cập nhật số lượng! Vui lòng kiểm tra console để biết chi tiết.');
                        $('.quantity-input[data-id="' + productId + '"]').val(originalQuantity);
                    }
                });
            },
            error: function (xhr) {
                console.log('AJAX Error (checkLogin):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                alert('Lỗi kiểm tra đăng nhập!');
                $('.quantity-input[data-id="' + productId + '"]').val(originalQuantity);
            }
        });
    });

    // Xóa sản phẩm khỏi giỏ hàng
    $(document).on('click', '.remove-from-cart', function () {
        var productId = $(this).data('id');

        if (!confirm('Bạn có muốn xóa không?')) {
            return;
        }

        $.ajax({
            url: 'index.php?controller=cart&action=removeFromCart',
            method: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    updateCartCount(response.items);
                    displayCartItems(response.items, response.total_price);
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr) {
                console.log('AJAX Error (removeFromCart):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                alert('Lỗi xóa sản phẩm! Mã trạng thái: ' + xhr.status + ', Chi tiết: ' + (xhr.statusText || 'Không xác định') + ', Phản hồi: ' + (xhr.responseText || 'Không có'));
            }
        });
    });

    // Áp dụng mã giảm giá
    $('.apply-discount-btn').on('click', function (e) {
        e.preventDefault();
        const discountCode = $('#discount-code').val().trim();
        if (!discountCode) {
            alert('Vui lòng nhập mã giảm giá!');
            return;
        }

        $.ajax({
            url: 'index.php?controller=cart&action=applyDiscount',
            method: 'POST',
            data: { discount_code: discountCode },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    updateCartCount(response.items);
                    displayCartItems(response.items, response.total_price, response.discount_percentage);
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr) {
                console.log('AJAX Error (applyDiscount):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                alert('Lỗi áp dụng mã giảm giá! Mã trạng thái: ' + xhr.status + ', Chi tiết: ' + (xhr.statusText || 'Không xác định') + ', Phản hồi: ' + (xhr.responseText || 'Không có'));
            }
        });
    });

    // Lấy và hiển thị giỏ hàng ban đầu
    if ($('#cart-items').length) {
        $.ajax({
            url: 'index.php?controller=cart&action=getCartDetails',
            method: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    updateCartCount(response.items);
                    displayCartItems(response.items, response.total_price);
                } else {
                    console.log('Error getting cart details:', response.message);
                    if (response.message.includes('Vui lòng đăng nhập')) {
                        window.location.href = 'index.php?controller=user&action=login';
                    }
                }
            },
            error: function (xhr) {
                console.log('AJAX Error (getCartDetails):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                alert('Lỗi lấy thông tin giỏ hàng! Mã trạng thái: ' + xhr.status + ', Chi tiết: ' + (xhr.statusText || 'Không xác định') + ', Phản hồi: ' + (xhr.responseText || 'Không có'));
            }
        });
    }
});