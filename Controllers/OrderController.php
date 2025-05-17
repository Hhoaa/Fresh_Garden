<?php
require_once 'config/config.php';
require_once 'Models/ProductModel.php';

class OrderController {
    private $productModel;

    public function __construct() {
        $db = getDbConnection();
        $this->productModel = new ProductModel($db);
        session_start();
    }

    // Tạo URL thanh toán VNPay
    public function createVnpayUrl() {
        header('Content-Type: application/json');
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thanh toán!']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $source = $_POST['source'] ?? 'cart'; // 'detail' hoặc 'cart'
            $orderItems = [];
            $totalPrice = 0;

            if ($source === 'detail') {
                if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
                    echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
                    exit;
                }
                $productId = (int)$_POST['product_id'];
                $quantity = (int)$_POST['quantity'];

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

                $orderItems[] = ['product_id' => $productId, 'quantity' => $quantity, 'unit_price' => $unitPrice];
                $totalPrice = $unitPrice * $quantity;
            } else { // source === 'cart'
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

                foreach ($cartItems as $item) {
                    $orderItems[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price']
                    ];
                }
                $totalPrice = $this->productModel->getCartTotalPrice($cartId);
            }

            // Tạo đơn hàng tạm thời
            $orderId = $this->productModel->createOrder($userId, $totalPrice);
            if (!$orderId) {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo đơn hàng!']);
                exit;
            }

            // Lưu orderItems và orderId vào session để xử lý sau khi thanh toán
            $_SESSION['pending_order'] = [
                'order_id' => $orderId,
                'order_items' => $orderItems,
                'source' => $source,
                'cart_id' => $cartId ?? null
            ];

            // Tạo URL thanh toán VNPay
            $vnp_TmnCode = "YOUR_TMNCODE"; // Thay bằng mã của bạn
            $vnp_HashSecret = "YOUR_SECRET"; // Thay bằng secret của bạn
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = "http://yourdomain.com/index.php?controller=order&action=vnpayReturn"; // Thay bằng domain của bạn

            $vnp_TxnRef = $orderId . "_" . time(); // Mã giao dịch
            $vnp_OrderInfo = "Thanh toan don hang $orderId";
            $vnp_OrderType = "billpayment";
            $vnp_Amount = $totalPrice * 100; // Số tiền (VNĐ) * 100
            $vnp_Locale = "vn";
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
            $vnp_CreateDate = date('YmdHis');
            $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

            $inputData = [
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => $vnp_ExpireDate
            ];

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }
            $vnp_Url = $vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac("sha512", $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            echo json_encode(['success' => true, 'url' => $vnp_Url]);
        } catch (Exception $e) {
            error_log('Error in createVnpayUrl: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
        exit;
    }

    // Xử lý kết quả trả về từ VNPay
    public function vnpayReturn() {
        $vnp_HashSecret = "YOUR_SECRET"; // Thay bằng secret của bạn
        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac("sha512", $hashData, $vnp_HashSecret);
        $status = "Thất bại";
        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                // Thanh toán thành công
                if (isset($_SESSION['pending_order'])) {
                    $pendingOrder = $_SESSION['pending_order'];
                    $orderId = $pendingOrder['order_id'];
                    $orderItems = $pendingOrder['order_items'];
                    $source = $pendingOrder['source'];
                    $cartId = $pendingOrder['cart_id'];

                    $this->productModel->getDb()->beginTransaction();
                    foreach ($orderItems as $item) {
                        $productId = $item['product_id'];
                        $quantity = $item['quantity'];
                        $unitPrice = $item['unit_price'];

                        if (!$this->productModel->addOrderItem($orderId, $productId, $quantity, $unitPrice)) {
                            throw new Exception('Lỗi khi thêm sản phẩm vào đơn hàng!');
                        }
                        if (!$this->productModel->updateStockQuantity($productId, -$quantity)) {
                            throw new Exception('Số lượng tồn kho không đủ!');
                        }
                    }

                    if ($source === 'cart' && $cartId) {
                        if (!$this->productModel->clearCart($cartId)) {
                            throw new Exception('Lỗi khi xóa giỏ hàng!');
                        }
                    }

                    $this->productModel->getDb()->commit();
                    $status = "Thành công";
                }
            } else {
                $status = "Giao dịch không thành công: " . $_GET['vnp_ResponseCode'];
            }
        } else {
            $status = "Chữ ký không hợp lệ!";
        }

        unset($_SESSION['pending_order']);
        require_once 'Views/order/vnpay_return.php';
    }

    // Hiển thị lịch sử đơn hàng
    public function history() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $stmt = $this->productModel->getDb()->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC");
        $stmt->execute(['user_id' => $user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'Views/order/history.php';
    }
}
?>