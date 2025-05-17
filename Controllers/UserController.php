<?php
require_once 'config/config.php';
require_once 'Models/UserModel.php';
require_once 'Models/ProductModel.php';

class UserController {
    private $userModel;
    private $productModel;
    private $db;

    public function __construct() {
        $this->db = getDbConnection();
        $this->userModel = new UserModel($this->db);
        $this->productModel = new ProductModel($this->db);
    }

    public function login() {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        if (isset($_SESSION['login_success'])) {
            $message = $_SESSION['login_success'];
            unset($_SESSION['login_success']);
            echo "<script>alert('$message'); window.location.href = 'index.php';</script>";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            try {
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $password = isset($_POST['password']) ? trim($_POST['password']) : '';

                if (empty($email) || empty($password)) {
                    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập email và mật khẩu!']);
                    exit;
                }

                $user = $this->userModel->getUserByEmail($email);
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_name'] = $user['full_name'];

                    $cart = $this->productModel->getCartByUserId($user['user_id']);
                    if (!$cart) {
                        $this->productModel->createCart($user['user_id']);
                    }

                    $redirectUrl = 'http://localhost/BTL_TTCN/index.php';
                    echo json_encode([
                        'success' => true,
                        'message' => 'Đăng nhập thành công! Chào mừng bạn, ' . htmlspecialchars($user['full_name']) . '!',
                        'redirect' => $redirectUrl
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Email hoặc mật khẩu không đúng!']);
                }
            } catch (Exception $e) {
                error_log('Error in login: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
            }
            exit;
        }

        return ['view' => 'user/login.php', 'data' => []];
    }

    public function register() {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php');
            exit;
        }

        if (isset($_SESSION['register_success'])) {
            $message = $_SESSION['register_success'];
            unset($_SESSION['register_success']);
            echo "<script>alert('$message'); window.location.href = 'index.php?controller=user&action=login';</script>";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            try {
                $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
                $password = isset($_POST['password']) ? trim($_POST['password']) : '';
                $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
                $address = isset($_POST['address']) ? trim($_POST['address']) : '';

                if (empty($full_name) || empty($email) || empty($phone_number) || empty($password) || empty($confirm_password)) {
                    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
                    exit;
                }

                if ($password !== $confirm_password) {
                    echo json_encode(['success' => false, 'message' => 'Mật khẩu không khớp!']);
                    exit;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(['success' => false, 'message' => 'Email không hợp lệ!']);
                    exit;
                }

                $existingUser = $this->userModel->getUserByEmail($email);
                if ($existingUser) {
                    echo json_encode(['success' => false, 'message' => 'Email đã được sử dụng!']);
                    exit;
                }

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $userId = $this->userModel->createUser($full_name, $email, $phone_number, $hashedPassword, $address);
                if ($userId) {
                    $this->productModel->createCart($userId);
                    $_SESSION['register_success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                    echo json_encode([
                        'success' => true,
                        'message' => 'Đăng ký thành công! Vui lòng đăng nhập.',
                        'redirect' => 'index.php?controller=user&action=login'
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Đăng ký thất bại! Vui lòng thử lại.']);
                }
            } catch (Exception $e) {
                error_log('Error in register: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
            }
            exit;
        }

        return ['view' => 'user/register.php', 'data' => []];
    }

    public function logout() {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['logout_success'] = 'Bạn đã đăng xuất thành công!';
        header('Location: http://localhost/BTL_TTCN/');
        exit;
    }
}
?>