<?php
class ProductModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy tất cả sản phẩm nổi bật
    public function getAllFeaturedProducts($limit = 5) {
        try {
            $stmt = $this->db->prepare("SELECT p.product_id, p.product_name, p.price, p.unit, i.image_url, p.stock_quantity, p.discount_id
                                        FROM products p
                                        LEFT JOIN images i ON p.product_id = i.product_id
                                        WHERE p.featured = 1
                                        LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $this->applyDiscounts($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log("Error in getAllFeaturedProducts: " . $e->getMessage());
            return [];
        }
    }

    // Lấy tổng số sản phẩm theo danh mục
    public function getTotalProductsByCategory($category_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE category_id = :category_id");
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['total'] : 0;
        } catch (PDOException $e) {
            error_log("Error in getTotalProductsByCategory: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy sản phẩm theo danh mục với phân trang
    public function getProductsByCategory($category_id, $page = 1, $perPage = 8) {
        try {
            $offset = ($page - 1) * $perPage;
            $stmt = $this->db->prepare("SELECT p.product_id, p.product_name, p.price, p.unit, i.image_url, p.stock_quantity, p.discount_id
                                        FROM products p
                                        LEFT JOIN images i ON p.product_id = i.product_id
                                        WHERE p.category_id = :category_id
                                        LIMIT :perPage OFFSET :offset");
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $this->applyDiscounts($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log("Error in getProductsByCategory: " . $e->getMessage());
            return [];
        }
    }

    // Lấy sản phẩm theo ID
    public function getProductById($product_id) {
        try {
            $stmt = $this->db->prepare("SELECT p.*, i.image_url FROM products p LEFT JOIN images i ON p.product_id = i.product_id WHERE p.product_id = :id");
            $stmt->execute(['id' => $product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ? $this->applyDiscount($product) : null;
        } catch (PDOException $e) {
            error_log("Error in getProductById: " . $e->getMessage());
            return null;
        }
    }

    // Lấy tên danh mục theo ID
    public function getCategoryNameById($category_id) {
        try {
            $stmt = $this->db->prepare("SELECT category_name FROM productcategories WHERE category_id = :id");
            $stmt->execute(['id' => $category_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['category_name' => 'Không tìm thấy'];
        } catch (PDOException $e) {
            error_log("Error in getCategoryNameById: " . $e->getMessage());
            return ['category_name' => 'Không tìm thấy'];
        }
    }

    // Lấy tất cả danh mục
    public function getAllCategories() {
        try {
            $stmt = $this->db->prepare("SELECT category_id, category_name FROM productcategories");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllCategories: " . $e->getMessage());
            return [];
        }
    }

    // Lấy sản phẩm liên quan
    public function getRelatedProducts($category_id, $product_id, $limit = 4) {
        try {
            $stmt = $this->db->prepare("SELECT p.product_id, p.product_name, p.price, p.unit, i.image_url, p.stock_quantity, p.discount_id
                                        FROM products p
                                        LEFT JOIN images i ON p.product_id = i.product_id
                                        WHERE p.category_id = :category_id AND p.product_id != :product_id
                                        LIMIT :limit");
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $this->applyDiscounts($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            error_log("Error in getRelatedProducts: " . $e->getMessage());
            return [];
        }
    }

    // Lấy số lượng tồn kho
    public function getStockQuantity($product_id) {
        try {
            $stmt = $this->db->prepare("SELECT stock_quantity FROM products WHERE product_id = :id");
            $stmt->execute(['id' => $product_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['stock_quantity'] : 0;
        } catch (PDOException $e) {
            error_log("Error in getStockQuantity: " . $e->getMessage());
            return 0;
        }
    }

    // Cập nhật số lượng tồn kho (hỗ trợ tăng hoặc giảm) với giao dịch
    public function updateStockQuantity($product_id, $quantity) {
        try {
            $this->db->beginTransaction();
            $currentStock = $this->getStockQuantity($product_id);

            if ($quantity > 0) { // Giảm tồn kho
                if ($currentStock < $quantity) {
                    throw new Exception("Số lượng tồn kho không đủ! Hiện tại: $currentStock, yêu cầu: $quantity");
                }
                $newStock = $currentStock - $quantity;
                $stmt = $this->db->prepare("UPDATE products SET stock_quantity = :newStock WHERE product_id = :id");
                $stmt->execute(['newStock' => $newStock, 'id' => $product_id]);
            } else { // Tăng tồn kho
                $quantity = abs($quantity);
                $newStock = $currentStock + $quantity;
                $stmt = $this->db->prepare("UPDATE products SET stock_quantity = :newStock WHERE product_id = :id");
                $stmt->execute(['newStock' => $newStock, 'id' => $product_id]);
            }

            if ($stmt->rowCount() > 0) {
                $this->db->commit();
                return true;
            } else {
                throw new Exception("Không có thay đổi nào được thực hiện!");
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in updateStockQuantity: " . $e->getMessage());
            return false;
        }
    }

    // Áp dụng mã giảm giá cho một sản phẩm
    private function applyDiscount($product, $cartDiscountId = null) {
        $discountPercentage = 0;

        // Ưu tiên mã giảm giá từ giỏ hàng nếu có
        if ($cartDiscountId) {
            $cartDiscount = $this->getDiscountDetails($cartDiscountId);
            if ($cartDiscount) {
                $discountPercentage = $cartDiscount['discount_percentage'];
            }
        }

        // Nếu không có mã giảm giá từ giỏ hàng hoặc mã giảm giá của sản phẩm lớn hơn
        if ($product['discount_id']) {
            $productDiscount = $this->getDiscountDetails($product['discount_id']);
            if ($productDiscount && $productDiscount['discount_percentage'] > $discountPercentage) {
                $discountPercentage = $productDiscount['discount_percentage'];
            }
        }

        if ($discountPercentage > 0) {
            $product['discounted_price'] = $product['price'] * (1 - $discountPercentage / 100);
            $product['discount_percentage'] = $discountPercentage;
        }

        return $product;
    }

    // Áp dụng mã giảm giá cho danh sách sản phẩm
    private function applyDiscounts($products, $cartDiscountId = null) {
        return array_map(function($product) use ($cartDiscountId) {
            return $this->applyDiscount($product, $cartDiscountId);
        }, $products);
    }

    // Lấy giỏ hàng theo user_id
    public function getCartByUserId($userId) {
        try {
            $query = "SELECT * FROM cart WHERE user_id = :user_id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userId]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$cart) {
                // Tạo giỏ hàng mới nếu không tìm thấy
                $cartId = $this->createCart($userId);
                if ($cartId) {
                    $query = "SELECT * FROM cart WHERE cart_id = :cart_id LIMIT 1";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute(['cart_id' => $cartId]);
                    $cart = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            }
            return $cart;
        } catch (PDOException $e) {
            error_log("Error in getCartByUserId: " . $e->getMessage());
            return null;
        }
    }

    // Tạo giỏ hàng mới
    public function createCart($userId) {
        try {
            $query = "INSERT INTO cart (user_id) VALUES (:user_id)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userId]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error in createCart: " . $e->getMessage());
            return 0;
        }
    }

    // Cập nhật discount_id cho giỏ hàng
    public function updateCartDiscount($cartId, $discountId) {
        try {
            $query = "UPDATE cart SET discount_id = :discount_id WHERE cart_id = :cart_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['discount_id' => $discountId, 'cart_id' => $cartId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error in updateCartDiscount: " . $e->getMessage());
            return false;
        }
    }

    // Lấy số lượng sản phẩm trong giỏ hàng
    public function getCartItemsCount($cartId) {
        try {
            $query = "SELECT COUNT(*) FROM cartitem WHERE cart_id = :cart_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['cart_id' => $cartId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error in getCartItemsCount: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy sản phẩm trong giỏ hàng
    public function getCartItem($cartId, $productId) {
        try {
            $query = "SELECT * FROM cartitem WHERE cart_id = :cart_id AND product_id = :product_id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCartItem: " . $e->getMessage());
            return null;
        }
    }

    // Thêm sản phẩm vào giỏ hàng với giá
    public function addCartItemWithPrice($cartId, $productId, $quantity, $unitPrice) {
        try {
            $query = "INSERT INTO cartitem (cart_id, product_id, quantity, unit_price) VALUES (:cart_id, :product_id, :quantity, :unit_price)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':cart_id' => $cartId,
                ':product_id' => $productId,
                ':quantity' => $quantity,
                ':unit_price' => $unitPrice
            ]);
        } catch (PDOException $e) {
            error_log("Error in addCartItemWithPrice: " . $e->getMessage());
        }
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateCartItemQuantity($cartId, $productId, $quantity) {
        try {
            $query = "UPDATE cartitem SET quantity = :quantity WHERE cart_id = :cart_id AND product_id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['quantity' => $quantity, 'cart_id' => $cartId, 'product_id' => $productId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error in updateCartItemQuantity: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật giá sản phẩm trong giỏ hàng
    public function updateCartItemUnitPrice($cartId, $productId, $unitPrice) {
        try {
            $query = "UPDATE cartitem SET unit_price = :unit_price WHERE cart_id = :cart_id AND product_id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['unit_price' => $unitPrice, 'cart_id' => $cartId, 'product_id' => $productId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error in updateCartItemUnitPrice: " . $e->getMessage());
            return false;
        }
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeCartItem($cartId, $productId) {
        try {
            $query = "DELETE FROM cartitem WHERE cart_id = :cart_id AND product_id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['cart_id' => $cartId, 'product_id' => $productId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error in removeCartItem: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách sản phẩm trong giỏ hàng
    public function getCartItems($cartId) {
        try {
            $query = "SELECT ci.*, p.product_name, p.price, p.unit, p.stock_quantity, p.discount_id, i.image_url
                      FROM cartitem ci
                      JOIN products p ON ci.product_id = p.product_id
                      LEFT JOIN images i ON p.product_id = i.product_id
                      WHERE ci.cart_id = :cart_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['cart_id' => $cartId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Lấy discount_id từ giỏ hàng
            $cart = $this->getCartByCartId($cartId);
            $cartDiscountId = $cart['discount_id'] ?? null;

            return $this->applyDiscounts($items, $cartDiscountId);
        } catch (PDOException $e) {
            error_log("Error in getCartItems: " . $e->getMessage());
            return [];
        }
    }

    // Lấy giỏ hàng theo cart_id
    public function getCartByCartId($cartId) {
        try {
            $query = "SELECT * FROM cart WHERE cart_id = :cart_id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['cart_id' => $cartId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCartByCartId: " . $e->getMessage());
            return null;
        }
    }

    // Lấy chi tiết mã giảm giá
    public function getDiscountDetails($discountId) {
        try {
            if (!$discountId) return null;
            $query = "SELECT * FROM discountcode WHERE discount_id = :discount_id AND status = 'active' AND end_date >= CURDATE()";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['discount_id' => $discountId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getDiscountDetails: " . $e->getMessage());
            return null;
        }
    }

    // Lấy mã giảm giá theo code
    public function getDiscountByCode($discountCode) {
        try {
            $query = "SELECT * FROM discountcode WHERE code = :code AND status = 'active' AND end_date >= CURDATE() AND (max_users IS NULL OR max_users > used_count)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['code' => $discountCode]);
            $discount = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($discount) {
                // Tăng used_count nếu mã giảm giá có giới hạn
                if ($discount['max_users'] !== null) {
                    $updateQuery = "UPDATE discountcode SET used_count = used_count + 1 WHERE discount_id = :discount_id";
                    $stmtUpdate = $this->db->prepare($updateQuery);
                    $stmtUpdate->execute(['discount_id' => $discount['discount_id']]);
                }
            }
            return $discount;
        } catch (PDOException $e) {
            error_log("Error in getDiscountByCode: " . $e->getMessage());
            return null;
        }
    }
}
?>