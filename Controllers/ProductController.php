<?php
require_once 'config/config.php';
require_once 'Models/ProductModel.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $db = getDbConnection();
        $this->productModel = new ProductModel($db);
    }

    public function listByCategory() {
        $category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($category_id <= 0) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $category = $this->productModel->getCategoryNameById($category_id);
        if (!$category) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 8;
        $totalProducts = $this->productModel->getTotalProductsByCategory($category_id);
        $totalPages = ceil($totalProducts / $perPage);

        $products = $this->productModel->getProductsByCategory($category_id, $page, $perPage) ?: [];
        $category_name = $category['category_name'];
        $categories = $this->productModel->getAllCategories();

        $paginationHtml = '';
        if ($totalPages > 1) {
            $paginationHtml .= '<div class="pagination">';
            if ($page > 1) {
                $paginationHtml .= '<a href="index.php?controller=product&action=listByCategory&id=' . $category_id . '&page=' . ($page - 1) . '" class="page-link">« Trang trước</a>';
            }
            for ($i = 1; $i <= $totalPages; $i++) {
                $activeClass = ($i == $page) ? 'active' : '';
                $paginationHtml .= '<a href="index.php?controller=product&action=listByCategory&id=' . $category_id . '&page=' . $i . '" class="page-link ' . $activeClass . '">' . $i . '</a>';
            }
            if ($page < $totalPages) {
                $paginationHtml .= '<a href="index.php?controller=product&action=listByCategory&id=' . $category_id . '&page=' . ($page + 1) . '" class="page-link">Trang sau »</a>';
            }
            $paginationHtml .= '</div>';
        }

        require_once 'Views/product/list_by_category.php';
    }

    public function detail() {
        $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($product_id <= 0) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            header('Location: index.php?controller=home&action=index');
            exit;
        }

        $category_id = $product['category_id'];
        $relatedProducts = $this->productModel->getRelatedProducts($category_id, $product_id, 4) ?: [];
        $category = $this->productModel->getCategoryNameById($category_id);
        $category_name = $category['category_name'] ?? 'Không tìm thấy';

        require_once 'Views/product/detail.php';
    }

    public function addToCart() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng!']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $productId = (int)$_POST['product_id'];
            $quantity = isset($_POST['quantity']) && (int)$_POST['quantity'] > 0 ? (int)$_POST['quantity'] : 1;

            $product = $this->productModel->getProductById($productId);
            $stock_quantity = $this->productModel->getStockQuantity($productId);

            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
                exit;
            }

            if ($quantity > $stock_quantity) {
                echo json_encode(['success' => false, 'message' => 'Số lượng vượt quá tồn kho (' . $stock_quantity . ')!']);
                exit;
            }

            $cart = $this->productModel->getCartByUserId($userId);
            if (!$cart) {
                $cartId = $this->productModel->createCart($userId);
            } else {
                $cartId = $cart['cart_id'];
            }

            $cartItem = $this->productModel->getCartItem($cartId, $productId);
            if ($cartItem) {
                $newQuantity = $cartItem['quantity'] + $quantity;
                if ($newQuantity > $stock_quantity) {
                    echo json_encode(['success' => false, 'message' => 'Tổng số lượng vượt quá tồn kho (' . $stock_quantity . ')!']);
                    exit;
                }
                $this->productModel->updateCartItemQuantity($cartId, $productId, $newQuantity);
            } else {
                $this->productModel->addCartItem($cartId, $productId, $quantity);
            }

            if ($this->productModel->updateStockQuantity($productId, -$quantity)) {
                $cartCount = $this->productModel->getCartItemsCount($cartId);
                echo json_encode(['success' => true, 'message' => 'Thêm vào giỏ hàng thành công!', 'cart_count' => $cartCount]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật số lượng tồn kho!']);
            }
            exit;
        }
    }
}
?>