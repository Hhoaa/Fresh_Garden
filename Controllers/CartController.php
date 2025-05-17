<?php
require_once 'config/config.php';
require_once 'Models/ProductModel.php';

class CartController {
    private $productModel;

    public function __construct() {
        $db = getDbConnection();
        $this->productModel = new ProductModel($db);
    }

    public function addToCart() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id']) || !isset($_POST['quantity'])) {
                echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
                exit;
            }

            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $productId = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];

            // Kiểm tra sản phẩm và số lượng tồn kho
            $product = $this->productModel->getProductById($productId);
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
                exit;
            }
            $stockQuantity = $this->productModel->getStockQuantity($productId);
            $unitPrice = $product['price'];

            if ($quantity <= 0 || $quantity > $stockQuantity) {
                echo json_encode(['success' => false, 'message' => 'Số lượng không hợp lệ hoặc vượt quá tồn kho (' . $stockQuantity . ')!']);
                exit;
            }

            // Lấy hoặc tạo giỏ hàng
            $cart = $this->productModel->getCartByUserId($userId);
            if (!$cart) {
                $cartId = $this->productModel->createCart($userId);
            } else {
                $cartId = $cart['cart_id'];
            }

            // Kiểm tra sản phẩm trong giỏ hàng
            $cartItem = $this->productModel->getCartItem($cartId, $productId);
            if ($cartItem) {
                $newQuantity = $cartItem['quantity'] + $quantity;
                if ($newQuantity > $stockQuantity) {
                    echo json_encode(['success' => false, 'message' => 'Tổng số lượng vượt quá tồn kho (' . $stockQuantity . ')!']);
                    exit;
                }
                $this->productModel->updateCartItemQuantity($cartId, $productId, $newQuantity);
                $this->productModel->updateCartItemUnitPrice($cartId, $productId, $unitPrice);
            } else {
                $this->productModel->addCartItemWithPrice($cartId, $productId, $quantity, $unitPrice);
            }

            // Cập nhật tồn kho
            if ($this->productModel->updateStockQuantity($productId, -$quantity)) {
                $cartItems = $this->productModel->getCartItems($cartId);
                $totalPrice = array_reduce($cartItems, function($sum, $item) {
                    return $sum + (($item['discounted_price'] ?? $item['unit_price']) * $item['quantity']);
                }, 0);

                // Chuẩn bị dữ liệu chi tiết cho giao diện
                $itemsWithDetails = array_map(function($item) use ($product) {
                    return [
                        'product_id' => $item['product_id'],
                        'product_name' => $product['product_name'],
                        'image_url' => $product['image_url'] ?? 'assets/img/default.jpg',
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'discounted_price' => $item['discounted_price'] ?? null
                    ];
                }, $cartItems);

                echo json_encode([
                    'success' => true,
                    'message' => 'Thêm sản phẩm vào giỏ hàng thành công!',
                    'cart_count' => $this->productModel->getCartItemsCount($cartId),
                    'items' => $itemsWithDetails,
                    'total_price' => $totalPrice
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật tồn kho!']);
            }
        } catch (Exception $e) {
            error_log('Error in addToCart: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
        exit;
    }

    public function updateQuantity() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id']) || !isset($_POST['quantity'])) {
                echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
                exit;
            }

            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để cập nhật giỏ hàng!']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $productId = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];

            $cart = $this->productModel->getCartByUserId($userId);
            if (!$cart) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng không tồn tại!']);
                exit;
            }
            $cartId = $cart['cart_id'];

            $cartItem = $this->productModel->getCartItem($cartId, $productId);
            if (!$cartItem) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng!']);
                exit;
            }

            $stockQuantity = $this->productModel->getStockQuantity($productId);
            if ($quantity <= 0 || $quantity > $stockQuantity + $cartItem['quantity']) {
                echo json_encode(['success' => false, 'message' => 'Số lượng vượt quá tồn kho (' . $stockQuantity . ')!']);
                exit;
            }

            $oldQuantity = $cartItem['quantity'];
            $quantityDifference = $quantity - $oldQuantity;
            if ($this->productModel->updateCartItemQuantity($cartId, $productId, $quantity) && $this->productModel->updateStockQuantity($productId, -$quantityDifference)) {
                $cartItems = $this->productModel->getCartItems($cartId);
                $totalPrice = array_reduce($cartItems, function($sum, $item) {
                    return $sum + (($item['discounted_price'] ?? $item['unit_price']) * $item['quantity']);
                }, 0);

                $itemsWithDetails = array_map(function($item) {
                    $product = $this->productModel->getProductById($item['product_id']);
                    return [
                        'product_id' => $item['product_id'],
                        'product_name' => $product['product_name'],
                        'image_url' => $product['image_url'] ?? 'assets/img/default.jpg',
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'discounted_price' => $item['discounted_price'] ?? null
                    ];
                }, $cartItems);

                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật số lượng thành công!',
                    'cart_count' => $this->productModel->getCartItemsCount($cartId),
                    'items' => $itemsWithDetails,
                    'total_price' => $totalPrice
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật số lượng!']);
            }
        } catch (Exception $e) {
            error_log('Error in updateQuantity: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
        exit;
    }

    public function removeFromCart() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
                echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
                exit;
            }

            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để xóa sản phẩm khỏi giỏ hàng!']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $productId = (int)$_POST['product_id'];

            $cart = $this->productModel->getCartByUserId($userId);
            if (!$cart) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng không tồn tại!']);
                exit;
            }
            $cartId = $cart['cart_id'];

            $cartItem = $this->productModel->getCartItem($cartId, $productId);
            if (!$cartItem) {
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng!']);
                exit;
            }

            $quantityToAddBack = $cartItem['quantity'];
            if ($this->productModel->removeCartItem($cartId, $productId) && $this->productModel->updateStockQuantity($productId, $quantityToAddBack)) {
                $cartItems = $this->productModel->getCartItems($cartId);
                $totalPrice = array_reduce($cartItems, function($sum, $item) {
                    return $sum + (($item['discounted_price'] ?? $item['unit_price']) * $item['quantity']);
                }, 0);

                $itemsWithDetails = array_map(function($item) {
                    $product = $this->productModel->getProductById($item['product_id']);
                    return [
                        'product_id' => $item['product_id'],
                        'product_name' => $product['product_name'],
                        'image_url' => $product['image_url'] ?? 'assets/img/default.jpg',
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'discounted_price' => $item['discounted_price'] ?? null
                    ];
                }, $cartItems);

                echo json_encode([
                    'success' => true,
                    'message' => 'Xóa sản phẩm thành công!',
                    'cart_count' => $this->productModel->getCartItemsCount($cartId),
                    'items' => $itemsWithDetails,
                    'total_price' => $totalPrice
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa sản phẩm!']);
            }
        } catch (Exception $e) {
            error_log('Error in removeFromCart: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
        exit;
    }

    public function getCartDetails() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ hoặc chưa đăng nhập!']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $cart = $this->productModel->getCartByUserId($userId);
            if (!$cart) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng không tồn tại!']);
                exit;
            }
            $cartId = $cart['cart_id'];

            $items = $this->productModel->getCartItems($cartId);
            $totalPrice = array_reduce($items, function($sum, $item) {
                return $sum + (($item['discounted_price'] ?? $item['unit_price']) * $item['quantity']);
            }, 0);

            $itemsWithDetails = array_map(function($item) {
                $product = $this->productModel->getProductById($item['product_id']);
                return [
                    'product_id' => $item['product_id'],
                    'product_name' => $product['product_name'],
                    'image_url' => $product['image_url'] ?? 'assets/img/default.jpg',
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'discounted_price' => $item['discounted_price'] ?? null
                ];
            }, $items);

            echo json_encode([
                'success' => true,
                'items' => $itemsWithDetails,
                'total_price' => $totalPrice
            ]);
        } catch (Exception $e) {
            error_log('Error in getCartDetails: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
        exit;
    }

    public function checkLogin() {
        header('Content-Type: application/json');
        echo json_encode(['loggedIn' => isset($_SESSION['user_id'])]);
        exit;
    }

    public function applyDiscount() {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['discount_code'])) {
                echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
                exit;
            }

            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để áp dụng mã giảm giá!']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $discountCode = trim($_POST['discount_code']);

            $cart = $this->productModel->getCartByUserId($userId);
            if (!$cart) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng không tồn tại!']);
                exit;
            }
            $cartId = $cart['cart_id'];

            $cartItems = $this->productModel->getCartItems($cartId);
            if (empty($cartItems)) {
                echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống!']);
                exit;
            }

            // Lấy thông tin mã giảm giá
            $discount = $this->productModel->getDiscountByCode($discountCode);
            if (!$discount) {
                echo json_encode(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!']);
                exit;
            }

            // Lưu discount_id vào giỏ hàng
            $discountId = $discount['discount_id'];
            if (!$this->productModel->updateCartDiscount($cartId, $discountId)) {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi áp dụng mã giảm giá!']);
                exit;
            }

            // Lấy lại danh sách sản phẩm với mã giảm giá đã áp dụng
            $cartItems = $this->productModel->getCartItems($cartId);
            $totalPrice = array_reduce($cartItems, function($sum, $item) {
                return $sum + (($item['discounted_price'] ?? $item['unit_price']) * $item['quantity']);
            }, 0);

            $itemsWithDetails = array_map(function($item) {
                $product = $this->productModel->getProductById($item['product_id']);
                return [
                    'product_id' => $item['product_id'],
                    'product_name' => $product['product_name'],
                    'image_url' => $product['image_url'] ?? 'assets/img/default.jpg',
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'discounted_price' => $item['discounted_price'] ?? null
                ];
            }, $cartItems);

            echo json_encode([
                'success' => true,
                'message' => 'Áp dụng mã giảm giá thành công!',
                'total_price' => $totalPrice,
                'discount_percentage' => $discount['discount_percentage'],
                'items' => $itemsWithDetails
            ]);
        } catch (Exception $e) {
            error_log('Error in applyDiscount: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
        exit;
    }

    public function viewCart() {
        try {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['redirect_url'] = 'index.php?controller=cart&action=viewCart';
                header('Location: index.php?controller=user&action=login');
                exit;
            }

            $userId = $_SESSION['user_id'];
            $cart = $this->productModel->getCartByUserId($userId);
            if (!$cart) {
                $cartId = $this->productModel->createCart($userId);
            } else {
                $cartId = $cart['cart_id'];
            }

            $products = $this->productModel->getCartItems($cartId);
            $totalPrice = array_reduce($products, function($sum, $item) {
                return $sum + (($item['discounted_price'] ?? $item['unit_price']) * $item['quantity']);
            }, 0);

            $data = [
                'products' => array_map(function($item) {
                    return [
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'image_url' => $item['image_url'] ?? 'assets/img/default.jpg',
                        'unit_price' => $item['unit_price'],
                        'quantity' => $item['quantity'],
                        'discounted_price' => $item['discounted_price'] ?? null
                    ];
                }, $products),
                'totalPrice' => $totalPrice
            ];
            return ['view' => 'cart/view.php', 'data' => $data];
        } catch (Exception $e) {
            error_log('Error in viewCart: ' . $e->getMessage());
            die('Lỗi máy chủ: ' . $e->getMessage());
        }
    }
}
?>