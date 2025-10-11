<?php
require_once __DIR__ . '/../src/bootstrap.php';
session_start();

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;

// Lấy danh sách danh mục
$query = "
    SELECT 
        category_id,
        category_name
    FROM Categories
    ORDER BY category_id DESC
";
$stmt = $pdo->query($query);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<!-- Main content -->
<div class="p-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="text-pink mb-0"> Danh mục sản phẩm</h3>

        <!-- Search -->
        <div class="search-bar w-30 pe-5">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control rounded-pill" placeholder="Tìm danh mục...">
                <button class="btn btn-pink rounded-pill ms-2">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <a href="add_categories.php" class="btn btn-pink">
            <i class="bi bi-plus-circle"></i> Thêm danh mục
        </a>
    </div>

    <!-- Hiển thị thông báo -->
    <?php if (isset($_SESSION['message'])): ?>
    <div class="mt-3">
        <?= $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <table class="table table-bordered table-hover align-middle shadow-sm rounded" id="categoryTable">
        <thead class="table-primary text-center">
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= $cat['category_id'] ?></td>
                <td class="text-start fw-semibold"><?= htmlspecialchars($cat['category_name']) ?></td>

                <td class="text-center">
                    <a href="edit_category.php?id=<?= $cat['category_id'] ?>"
                        class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <a href="delete_category.php?id=<?= $cat['category_id'] ?>"
                        onclick="return confirm('Bạn có chắc muốn xóa danh mục này không?');"
                        class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted">Không có danh mục nào.</td>
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

<!-- JS: tìm kiếm danh mục -->
<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll("#categoryTable tbody tr");
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});
</script>