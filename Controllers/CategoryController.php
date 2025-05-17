<?php
require_once 'Models/ProductModel.php';

class CategoryController {
    private $productModel;

    public function __construct($db) {
        $this->productModel = new ProductModel($db);
    }

    public function listByCategory($category_id) {
        $category_id = isset($category_id) ? (int)$category_id : 1; // Giá trị mặc định nếu không có
        $category_name = $this->productModel->getCategoryNameById($category_id);
        $products = $this->productModel->getProductsByCategory($category_id);
        $categories = $this->productModel->getAllCategories();

        require_once 'Views/listByCategory.php';
    }
}
?>