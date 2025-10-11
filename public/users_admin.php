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

// Lấy danh sách người dùng là khách hàng
$stmt = $pdo->query("SELECT user_id, username, full_name, email, phone_number, role, created_at FROM Users WHERE role = 'customer' ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);



include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>



<!-- Main content -->
<div class="p-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="text-pink mb-0">Danh sách người dùng (Khách hàng)</h3>

        <!-- Search -->
        <div class="search-bar w-30 pe-5">
            <div class="input-group">
                <input type="text" class="form-control rounded-pill" placeholder="Bạn cần tìm ...">
                <button class="btn btn-pink rounded-pill ms-2">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <a href="add_user.php" class="btn btn-pink">
            <i class="bi bi-person-plus"></i> Thêm mới
        </a>
    </div>
    <!-- Hiển thị thông báo sau khi xóa -->
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
                <th>Tên đăng nhập</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['user_id'] ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['full_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone_number']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                <td class="text-center">
                    <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <a href="delete_user.php?id=<?= $user['user_id'] ?>"
                        onclick="return confirm('Bạn có chắc muốn xóa người dùng này không?');"
                        class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Xóa
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" class="text-center text-muted">Không tìm thấy người dùng phù hợp.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>




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

/* Bảng full width và rõ ràng hơn */
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