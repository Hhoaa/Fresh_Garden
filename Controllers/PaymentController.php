<?php
require_once 'config/config.php';
require_once 'Models/ProductModel.php';
require_once 'config/vnpay_config.php';

class PaymentController {
    private $productModel;

    public function __construct() {
        $db = getDbConnection();
        $this->productModel = new ProductModel($db);
    }

    public function vnpayCheckoutFromCart() {
        try {
            if (!isset($_SESSION['user_id'])) {
                error_log('User not logged in, redirecting to login');
                $_SESSION['redirect_url'] = 'index.php?controller=payment&action=vnpayCheckoutFromCart';
                header('Location: index.php?controller=user&action=login');
                exit;
            }

            $userId = $_SESSION['user_id'];
            error_log('User ID in vnpayCheckoutFromCart: ' . $userId);

            $cart = $this->productModel->getCartByUserId($userId);
            error_log('Cart data in vnpayCheckoutFromCart: ' . json_encode($cart));
            if (!$cart) {
                error_log('Cart not found for user ID: ' . $userId);
                die('Giỏ hàng không tồn tại!');
            }

            $cartId = $cart['cart_id'];
            $cartItems = $this->productModel->getCartItems($cartId);
            error_log('Cart items in vnpayCheckoutFromCart: ' . json_encode($cartItems));
            if (empty($cartItems)) {
                error_log('Cart is empty for cart ID: ' . $cartId);
                die('Giỏ hàng trống!');
            }

            $totalPrice = array_reduce($cartItems, function($sum, $item) {
                return $sum + ($item['unit_price'] * $item['quantity']);
            }, 0);

            $data = [
                'cart_id' => $cartId,
                'cart_items' => $cartItems,
                'total_price' => $totalPrice
            ];

            return ['view' => 'order/vnpay.php', 'data' => $data];
        } catch (Exception $e) {
            error_log('Error in vnpayCheckoutFromCart: ' . $e->getMessage());
            die('Lỗi máy chủ: ' . $e->getMessage());
        }
    }

    public function vnpayCheckoutFromProduct() {
        try {
            $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
            $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

            if ($product_id <= 0 || $quantity <= 0) {
                error_log('Invalid product ID or quantity: product_id=' . $product_id . ', quantity=' . $quantity);
                die('Sản phẩm hoặc số lượng không hợp lệ!');
            }

            // Lấy thông tin sản phẩm từ database
            $product = $this->productModel->getProductById($product_id);
            if (!$product) {
                error_log('Product not found for ID: ' . $product_id);
                die('Sản phẩm không tồn tại!');
            }

            if ($quantity > $product['stock_quantity']) {
                error_log('Quantity exceeds stock for product ID: ' . $product_id);
                die('Số lượng vượt quá tồn kho!');
            }

            $totalPrice = $product['price'] * $quantity;

            $data = [
                'product' => $product,
                'quantity' => $quantity,
                'total_price' => $totalPrice
            ];

            return ['view' => 'order/vnpay.php', 'data' => $data];
        } catch (Exception $e) {
            error_log('Error in vnpayCheckoutFromProduct: ' . $e->getMessage());
            die('Lỗi máy chủ: ' . $e->getMessage());
        }
    }

    public function processVnpay() {
        try {
            if (!isset($_SESSION['user_id'])) {
                error_log('User not logged in, redirecting to login in processVnpay');
                header('Location: index.php?controller=user&action=login');
                exit;
            }

            if (!isset($_POST['cart_id']) && !isset($_POST['product_id'])) {
                error_log('Invalid request in processVnpay: cart_id or product_id missing');
                die('Yêu cầu không hợp lệ!');
            }

            $userId = $_SESSION['user_id'];
            error_log('Processing VNPay for user ID: ' . $userId);

            if (isset($_POST['cart_id'])) {
                $cartId = (int)$_POST['cart_id'];
                $discountCode = trim($_POST['discount_code'] ?? '');
                $cart = $this->productModel->getCartByUserId($userId);
                if (!$cart || $cart['cart_id'] != $cartId) {
                    error_log('Invalid cart in processVnpay for user ID: ' . $userId . ', cart ID: ' . $cartId);
                    die('Giỏ hàng không hợp lệ!');
                }

                $cartItems = $this->productModel->getCartItems($cartId);
                if (empty($cartItems)) {
                    error_log('Cart is empty in processVnpay for cart ID: ' . $cartId);
                    die('Giỏ hàng trống!');
                }

                if (!empty($discountCode)) {
                    $discount = $this->productModel->getDiscountByCode($discountCode);
                    if ($discount) {
                        $this->productModel->updateCartDiscount($cartId, $discount['discount_id']);
                        error_log('Discount applied: ' . json_encode($discount));
                    } else {
                        error_log('Invalid discount code: ' . $discountCode);
                    }
                }

                $cartItems = $this->productModel->getCartItems($cartId);
                $totalPrice = array_reduce($cartItems, function($sum, $item) {
                    return $sum + (($item['discounted_price'] ?? $item['unit_price']) * $item['quantity']);
                }, 0);
                error_log('Total price after discount (cart): ' . $totalPrice);
            } else {
                $product_id = (int)$_POST['product_id'];
                $quantity = (int)$_POST['quantity'];
                $discountCode = trim($_POST['discount_code'] ?? '');

                $product = $this->productModel->getProductById($product_id);
                if (!$product) {
                    error_log('Product not found for ID: ' . $product_id);
                    die('Sản phẩm không tồn tại!');
                }

                if ($quantity > $product['stock_quantity']) {
                    error_log('Quantity exceeds stock for product ID: ' . $product_id);
                    die('Số lượng vượt quá tồn kho!');
                }

                $totalPrice = $product['price'] * $quantity;
                if (!empty($discountCode)) {
                    $discount = $this->productModel->getDiscountByCode($discountCode);
                    if ($discount) {
                        $totalPrice -= ($totalPrice * $discount['discount_percentage'] / 100);
                        error_log('Discount applied to product: ' . json_encode($discount));
                    } else {
                        error_log('Invalid discount code: ' . $discountCode);
                    }
                }
                error_log('Total price after discount (product): ' . $totalPrice);
            }

            // Cấu hình thanh toán VNPay
            $vnp_TxnRef = time() . '_' . ($cartId ?? $product_id);
            $vnp_OrderInfo = 'Thanh toán ' . ($cartId ? 'giỏ hàng ID ' . $cartId : 'sản phẩm ID ' . $product_id);
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $totalPrice * 100; // VNPay yêu cầu số tiền nhân 100
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => VNPAY_TMN_CODE,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => VNPAY_RETURN_URL,
                "vnp_TxnRef" => $vnp_TxnRef
            );

            ksort($inputData);
            $query = http_build_query($inputData);
            $vnpSecureHash = hash_hmac('sha512', $query, VNPAY_HASH_SECRET);
            $vnp_Url = VNPAY_URL . "?" . $query . '&vnp_SecureHash=' . $vnpSecureHash;
            error_log('VNPay URL: ' . $vnp_Url);

            header('Location: ' . $vnp_Url);
            exit;
        } catch (Exception $e) {
            error_log('Error in processVnpay: ' . $e->getMessage());
            die('Lỗi máy chủ: ' . $e->getMessage());
        }
    }

    public function vnpayReturn() {
        try {
            $vnp_HashSecret = VNPAY_HASH_SECRET;
            $inputData = $_GET;
            $vnp_SecureHash = $inputData['vnp_SecureHash'];
            unset($inputData['vnp_SecureHash']);

            ksort($inputData);
            $query = http_build_query($inputData);
            $hashData = hash_hmac('sha512', $query, $vnp_HashSecret);

            $data = [
                'success' => false,
                'message' => 'Giao dịch không thành công!'
            ];

            if ($vnp_SecureHash === $hashData && $inputData['vnp_ResponseCode'] == '00') {
                $refId = explode('_', $inputData['vnp_TxnRef'])[1];
                $cartItems = isset($_POST['cart_id']) ? $this->productModel->getCartItems($refId) : null;
                if ($cartItems) {
                    foreach ($cartItems as $item) {
                        $this->productModel->updateStockQuantity($item['product_id'], -$item['quantity']);
                    }
                } else {
                    $product_id = (int)$refId;
                    $quantity = (int)$_POST['quantity'];
                    $this->productModel->updateStockQuantity($product_id, -$quantity);
                }
                $data = [
                    'success' => true,
                    'message' => 'Thanh toán thành công!',
                    'transaction_id' => $inputData['vnp_TransactionNo'],
                    'amount' => $inputData['vnp_Amount'] / 100,
                    'order_info' => $inputData['vnp_OrderInfo']
                ];
                error_log('VNPay transaction successful: ' . json_encode($data));
            } else {
                error_log('VNPay transaction failed: ' . json_encode($inputData));
            }

            return ['view' => 'order/veduct.php', 'data' => $data];
        } catch (Exception $e) {
            error_log('Error in vnpayReturn: ' . $e->getMessage());
            die('Lỗi máy chủ: ' . $e->getMessage());
        }
    }
}
?>