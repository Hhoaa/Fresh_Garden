<?php
class AboutModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCategories() {
        $stmt = $this->conn->prepare("SELECT category_id, category_name, description FROM productcategories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLatestDiscount() {
        $stmt = $this->conn->prepare("SELECT discount_id, code, description, discount_percentage FROM discountcode WHERE status = 'active' ORDER BY created_at DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>