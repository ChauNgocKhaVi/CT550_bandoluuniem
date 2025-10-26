<?php

namespace CT550\Labs;

use PDO;

class Product
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Xử lý form thêm sản phẩm
    public function handleAddProductForm(array $data, array $files): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return '';
        }

        $name = trim($data['product_name'] ?? '');
        $category_id = $data['category_id'] ?? null;
        $brand_id = $data['brand_id'] ?? null;
        $description = trim($data['description'] ?? '');
        $original_price = $data['original_price'] ?? null;
        $price = $data['price'] ?? null;
        $stock_quantity = $data['stock_quantity'] ?? 0;

        if (empty($name) || empty($category_id) || empty($brand_id) || empty($price)) {
            return '<div class="alert alert-danger mt-3">Vui lòng nhập đầy đủ thông tin bắt buộc!</div>';
        }

        // ⚠️ Kiểm tra giá gốc và giá bán
        if ($original_price !== null && $price !== null && $original_price > $price) {
            return '<div class="alert alert-danger mt-3">❌ Giá gốc phải nhỏ hơn giá bán. Bạn đã nhập giá gốc ({$original_price}) và giá bán ({$price}) không hợp lệ!</div>';
        }

        // Xử lý upload ảnh
        $imagePath = null;
        if (!empty($files['image']['name'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // Lấy phần mở rộng file (vd: jpg, png, ...)
            $extension = pathinfo($files['image']['name'], PATHINFO_EXTENSION);
            $baseName = 'anh'; // tên cơ bản là "anh"
            $counter = 1;

            // Kiểm tra xem file đã tồn tại chưa, nếu có thì tăng số lên
            do {
                $fileName = $baseName . $counter . '.' . $extension;
                $targetFile = $uploadDir . $fileName;
                $counter++;
            } while (file_exists($targetFile));

            // Upload ảnh
            if (move_uploaded_file($files['image']['tmp_name'], $targetFile)) {
                $imagePath = 'uploads/' . $fileName;
            } else {
                return '<div class="alert alert-danger mt-3">❌ Lỗi khi tải ảnh lên!</div>';
            }
        }


        // Thêm sản phẩm vào CSDL
        $stmt = $this->db->prepare("
            INSERT INTO Products (category_id, brand_id, product_name, description, original_price, price, stock_quantity, image)
            VALUES (:category_id, :brand_id, :name, :description, :original_price, :price, :stock_quantity, :image)
        ");

        $stmt->execute([
            ':category_id' => $category_id,
            ':brand_id' => $brand_id,
            ':name' => $name,
            ':description' => $description,
            ':original_price' => $original_price,
            ':price' => $price,
            ':stock_quantity' => $stock_quantity,
            ':image' => $imagePath
        ]);

        return '<div class="alert alert-success mt-3">✅ Thêm sản phẩm thành công!</div>';
    }

    // Lấy danh sách thương hiệu
    public function getAllBrands(): array
    {
        $stmt = $this->db->query("SELECT * FROM Brands ORDER BY brand_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy danh sách thể loại
    public function getAllCategories(): array
    {
        $stmt = $this->db->query("SELECT category_id, category_name FROM Categories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Chỉnh sửa sản phẩm
    public function handleEditProductForm(int $id, array $data, array $files): string
    {
        $name = trim($data['product_name']);
        $category_id = $data['category_id'] ?? null;
        $brand_id = $data['brand_id'] ?? null;
        $description = trim($data['description'] ?? '');
        $original_price = $data['original_price'] ?? 0;
        $price = $data['price'] ?? 0;
        $stock = $data['stock_quantity'] ?? 0;

        // ✅ Kiểm tra giá hợp lệ
        if ($original_price <= 0 || $price <= 0) {
            return "<div class='alert alert-warning'>Giá gốc và giá bán phải lớn hơn 0.</div>";
        }

        if ($original_price > $price) {
            return "<div class='alert alert-warning'>Giá gốc phải nhỏ hơn giá bán.</div>";
        }

        // ✅ Lấy ảnh cũ từ DB
        $stmt = $this->db->prepare("SELECT image FROM Products WHERE product_id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        $oldImage = $current['image'] ?? null;

        // Nếu ảnh lưu có 'uploads/', chỉ lấy tên file
        if ($oldImage && str_starts_with($oldImage, 'uploads/')) {
            $oldImage = basename($oldImage);
        }

        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $imageName = $oldImage ? 'uploads/' . $oldImage : null;

        // ✅ Nếu có upload ảnh mới
        if (!empty($files['image']['name'])) {
            $tmpPath = $files['image']['tmp_name'];
            $extension = pathinfo($files['image']['name'], PATHINFO_EXTENSION);

            if ($oldImage) {
                // Nếu có ảnh cũ → giữ nguyên tên ảnh cũ
                $targetFile = $uploadDir . $oldImage;

                // Xóa ảnh cũ nếu tồn tại
                if (file_exists($targetFile)) {
                    unlink($targetFile);
                }
            } else {
                // Nếu chưa có ảnh cũ → tạo tên mới tự động như khi thêm sản phẩm
                $baseName = 'anh';
                $counter = 1;
                do {
                    $fileName = $baseName . $counter . '.' . strtolower($extension);
                    $targetFile = $uploadDir . $fileName;
                    $counter++;
                } while (file_exists($targetFile));

                $imageName = 'uploads/' . $fileName;
            }

            // Upload ảnh mới
            if (move_uploaded_file($tmpPath, $targetFile)) {
                // Giữ nguyên $imageName nếu có ảnh cũ, hoặc dùng tên mới nếu không
            } else {
                return "<div class='alert alert-danger'>❌ Lỗi khi tải ảnh lên!</div>";
            }
        }

        // ✅ Cập nhật DB
        $sql = "UPDATE Products 
        SET category_id = ?, brand_id = ?, product_name = ?, description = ?, 
            original_price = ?, price = ?, stock_quantity = ?, 
            image = ?, updated_at = NOW()
        WHERE product_id = ?";
        $params = [$category_id, $brand_id, $name, $description, $original_price, $price, $stock, $imageName, $id];
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return "<div class='alert alert-success text-center'>✅ Cập nhật sản phẩm thành công!</div>";
    }




    public function getProductById(int $id): ?array
    {
        $sql = "SELECT 
                p.*, 
                c.category_name 
            FROM Products p
            LEFT JOIN Categories c ON p.category_id = c.category_id
            WHERE p.product_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }
}