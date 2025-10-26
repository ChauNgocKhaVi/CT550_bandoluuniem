<?php
require_once __DIR__ . '/../src/bootstrap.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

// Kết nối CSDL
$pdo = $PDO;

// Lấy danh sách sản phẩm và tên thể loại
$query = "
    SELECT 
        p.product_id, 
        p.product_name, 
        c.category_name,
        b.brand_name, 
        p.description, 
        p.original_price, 
        p.price, 
        p.stock_quantity, 
        p.image, 
        p.created_at 
    FROM Products p
    LEFT JOIN Categories c ON p.category_id = c.category_id
    LEFT JOIN Brands b ON p.brand_id = b.brand_id
    ORDER BY p.created_at DESC
";
$stmt = $pdo->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<!-- Main content -->
<div class="p-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="text-pink mb-0">Danh sách sản phẩm</h3>

        <!-- Search -->
        <div class="search-bar w-30 pe-5">
            <div class="input-group">
                <input type="text" class="form-control rounded-pill" placeholder="Tìm sản phẩm...">
                <button class="btn btn-pink rounded-pill ms-2">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <a href="add_product.php" class="btn btn-pink">
            <i class="bi bi-plus-circle"></i> Thêm sản phẩm
        </a>
    </div>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="mt-3">
            <?= $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <table class="table table-bordered table-hover align-middle shadow-sm rounded">
        <thead class="table-primary text-center">
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Thể loại</th>
                <th>Thương hiệu</th>
                <th>Giá gốc (₫)</th>
                <th>Giá bán (₫)</th>
                <th>Tồn kho</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['product_id'] ?></td>
                        <td>
                            <?php if (!empty($product['image'])): ?>
                                <!-- htmlspecialchars($product['image'])  -->
                                <img src="<?= htmlspecialchars($product['image']) ?>"
                                    alt="<?= htmlspecialchars($product['product_name']) ?>" width="60" height="60"
                                    class="rounded shadow-sm">
                            <?php else: ?>
                                <span class="text-muted">Không có ảnh</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-start"><?= htmlspecialchars($product['product_name']) ?></td>
                        <td><?= htmlspecialchars($product['category_name'] ?? 'Chưa phân loại') ?></td>
                        <td><?= htmlspecialchars($product['brand_name'] ?? 'Chưa thương hiệu') ?></td>
                        <td><?= number_format($product['original_price'], 0, ',', '.') ?></td>
                        <td class="text-danger fw-bold"><?= number_format($product['price'], 0, ',', '.') ?></td>
                        <td><?= $product['stock_quantity'] ?></td>

                        <td><?= date('d/m/Y H:i', strtotime($product['created_at'])) ?></td>
                        <td class="text-center">
                            <a href="edit_product.php?id=<?= $product['product_id'] ?>"
                                class="btn btn-sm btn-outline-primary me-1 ">
                                <i class="bi bi-pencil"></i> Sửa
                            </a>
                            <a href="delete_product.php?id=<?= $product['product_id'] ?>"
                                onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này không?');"
                                class="btn btn-sm btn-outline-danger mt-2">
                                <i class="bi bi-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center text-muted">Không có sản phẩm nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- CSS -->
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f8f9fa;
    }

    #sidebar .nav-link {
        font-weight: 500;
        color: #333;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    #sidebar .nav-link:hover {
        background-color: #e3f2fd;
        color: #e91e63;
        border-radius: 8px;
    }

    #sidebar .nav-link.active {
        background-color: #e91e63;
        color: white;
        border-radius: 8px;
    }

    /* Bảng hiển thị rõ ràng hơn */
    .table {
        width: 100%;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
    }

    .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
    }

    .table thead th {
        background-color: #e91e63;
        color: white;
    }
</style>