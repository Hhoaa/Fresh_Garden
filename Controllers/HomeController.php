<?php
// Bao gồm các file cần thiết
require_once 'config/config.php';
require_once 'Models/CategoryModel.php';
require_once 'Models/ProductModel.php';

class HomeController {
    private $categoryModel;
    private $productModel;

    /**
     * Khởi tạo HomeController và truyền kết nối cơ sở dữ liệu vào các model
     */
    public function __construct() {
        // Lấy kết nối cơ sở dữ liệu từ hàm getDbConnection()
        $db = getDbConnection();
        // Khởi tạo các model với kết nối cơ sở dữ liệu
        $this->categoryModel = new CategoryModel($db);
        $this->productModel = new ProductModel($db);
    }

    /**
     * Hiển thị trang chủ
     * Lấy danh sách danh mục và sản phẩm nổi bật để hiển thị
     */
    public function index() {
        // Lấy danh sách tất cả danh mục
        $categories = $this->categoryModel->getCategoryNames();
        // Nếu không có danh mục, gán mảng rỗng để tránh lỗi
        $categories = $categories ?: [];

        // Lấy tối đa 5 sản phẩm nổi bật
        $featuredProducts = $this->productModel->getAllFeaturedProducts(5); // Sửa từ ProductModel thành productModel
        // Nếu không có sản phẩm nổi bật, gán mảng rỗng
        $featuredProducts = $featuredProducts ?: [];

        // Tải view trang chủ và truyền dữ liệu
        require_once 'Views/home/index.php';
    }
}
?>