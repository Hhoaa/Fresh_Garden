$(document).ready(function() {
    // Khởi tạo slick slider cho slideshow
    $('.slideshow').slick({
        infinite: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 3000,
        dots: true,
        arrows: true,
        prevArrow: '<button type="button" class="slick-prev"></button>',
        nextArrow: '<button type="button" class="slick-next"></button>'
    });

    // Khởi tạo slick slider cho sản phẩm nổi bật (hiển thị tối đa 5 sản phẩm)
    $('.products-slick').slick({
        infinite: true,
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: false,
        prevArrow: '<button type="button" class="slick-prev"><</button>',
        nextArrow: '<button type="button" class="slick-next">></button>',
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    // Xử lý dropdown menu
    document.addEventListener('DOMContentLoaded', function () {
        const userName = document.querySelector('.user-name');
        const dropdownContent = document.querySelector('.dropdown-content');

        if (userName && dropdownContent) {
            userName.addEventListener('click', function (e) {
                e.preventDefault();
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            });

            // Ẩn dropdown khi click bên ngoài
            document.addEventListener('click', function (e) {
                if (!userName.contains(e.target) && !dropdownContent.contains(e.target)) {
                    dropdownContent.style.display = 'none';
                }
            });
        }
    });

    // Xử lý đăng nhập
    $('#login-form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'index.php?controller=user&action=login',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = response.redirect || 'http://localhost/BTL_TTCN/index.php';
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log('AJAX Error (login):', { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
                alert('Lỗi kết nối đến máy chủ (login)! Mã trạng thái: ' + xhr.status + ', Chi tiết: ' + (xhr.statusText || 'Không xác định') + ', Phản hồi: ' + (xhr.responseText || 'Không có'));
            }
        });
    });

    
});