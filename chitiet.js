document.addEventListener("DOMContentLoaded", function () {
    // Thay đổi ảnh chính khi click vào ảnh thu nhỏ
    const thumbnails = document.querySelectorAll(".thumbnail");
    const mainImage = document.getElementById("main-img");

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener("click", function () {
            // Bỏ active class khỏi tất cả thumbnails
            thumbnails.forEach(thumb => thumb.classList.remove("active"));

            // Gán ảnh mới và thêm class active
            mainImage.src = this.src;
            this.classList.add("active");
        });
    });

    // Hàm tăng/giảm số lượng sản phẩm
    window.changeQuantity = function (value) {
        let quantityInput = document.getElementById("quantity");
        let currentValue = parseInt(quantityInput.value);

        if (!isNaN(currentValue)) {
            let newValue = currentValue + value;
            if (newValue >= 1) {
                quantityInput.value = newValue;
            }
        }
    };

    // Thêm hiệu ứng slider ngang cho sản phẩm liên quan
    let productList = document.querySelector(".product-list");
    let isDown = false;
    let startX;
    let scrollLeft;

    productList.addEventListener("mousedown", (e) => {
        isDown = true;
        startX = e.pageX - productList.offsetLeft;
        scrollLeft = productList.scrollLeft;
    });

    productList.addEventListener("mouseleave", () => {
        isDown = false;
    });

    productList.addEventListener("mouseup", () => {
        isDown = false;
    });

    productList.addEventListener("mousemove", (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - productList.offsetLeft;
        const walk = (x - startX) * 2; // Tăng tốc độ cuộn
        productList.scrollLeft = scrollLeft - walk;
    });
});
