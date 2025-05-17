<?php
class CategoryModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Lấy tất cả danh mục
    public function getCategoryNames() {
        $stmt = $this->db->prepare("SELECT category_id, category_name FROM productcategories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>