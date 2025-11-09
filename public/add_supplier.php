<?php
require_once __DIR__ . '/../src/bootstrap.php';

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;
$message = "";

// ✅ Khi nhấn nút Thêm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['supplier_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $ward = trim($_POST['ward'] ?? '');
    $address_detail = trim($_POST['address_detail'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // ✅ Gộp địa chỉ đầy đủ
    $address_parts = array_filter([$address_detail, $ward, $district, $province]);
    $address = implode(', ', $address_parts);

    if ($name === '') {
        $message = '<div class="alert alert-danger text-center">Vui lòng nhập tên nhà cung cấp.</div>';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO Suppliers (supplier_name, phone, address, email)
            VALUES (:name, :phone, :address, :email)
        ");
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':address' => $address,
            ':email' => $email
        ]);

        $_SESSION['message'] = '<div class="alert alert-success text-center">Thêm nhà cung cấp thành công!</div>';
        header('Location: suppliers_admin.php');
        exit;
    }
}

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="text-pink mb-4 text-center">Thêm nhà cung cấp mới</h3>

    <?= $message ?>

    <form method="POST" class="mx-auto shadow-sm p-4 rounded bg-white" style="max-width: 650px;">
        <!-- Tên -->
        <div class="mb-3">
            <label for="supplier_name" class="form-label fw-semibold">
                Tên nhà cung cấp <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
        </div>

        <!-- Điện thoại -->
        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="VD: 0987xxxxxx">
        </div>

        <!-- Địa chỉ -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Địa chỉ</label>
            <div class="row g-2">
                <div class="col-12 mb-2">
                    <input type="text" class="form-control" id="address_detail" name="address_detail"
                        placeholder="Số nhà, tên đường (VD: 12 Nguyễn Trãi)">
                </div>
                <div class="col-md-4">
                    <select id="province" name="province" class="form-select" required>
                        <option value="">-- Chọn Tỉnh/Thành phố --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="district" name="district" class="form-select" required>
                        <option value="">-- Chọn Quận/Huyện --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="ward" name="ward" class="form-select" required>
                        <option value="">-- Chọn Phường/Xã --</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="VD: supplier@gmail.com">
        </div>

        <!-- Nút -->
        <div class="d-flex justify-content-center mt-4 gap-3">
            <button type="submit" class="btn btn-pink px-4">
                <i class="bi bi-save"></i> Lưu
            </button>
            <a href="suppliers_admin.php" class="btn btn-secondary px-4">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </form>
</div>

<!-- ✅ Script load tỉnh/huyện/xã -->
<script>
const provinceSelect = document.getElementById("province");
const districtSelect = document.getElementById("district");
const wardSelect = document.getElementById("ward");
let provincesData = [];

// 🔹 1. Tải dữ liệu từ file JSON nội bộ
fetch("data/vietnam.json")
    .then(res => res.json())
    .then(data => {
        provincesData = data;
        data.forEach(p => {
            const option = new Option(p.Name, p.Name);
            option.dataset.code = p.Id;
            provinceSelect.add(option);
        });
    });

// 🔹 2. Khi chọn tỉnh → load huyện
provinceSelect.addEventListener("change", () => {
    const provinceCode = provinceSelect.options[provinceSelect.selectedIndex].dataset.code;
    const province = provincesData.find(p => p.Id == provinceCode);

    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';

    if (province) {
        province.Districts.forEach(d => {
            const option = new Option(d.Name, d.Name);
            option.dataset.code = d.Id;
            districtSelect.add(option);
        });
    }
});

// 🔹 3. Khi chọn huyện → load xã
districtSelect.addEventListener("change", () => {
    const provinceCode = provinceSelect.options[provinceSelect.selectedIndex].dataset.code;
    const districtCode = districtSelect.options[districtSelect.selectedIndex].dataset.code;

    const province = provincesData.find(p => p.Id == provinceCode);
    const district = province?.Districts.find(d => d.Id == districtCode);

    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    if (district) {
        district.Wards.forEach(w => {
            const option = new Option(w.Name, w.Name);
            option.dataset.code = w.Id;
            wardSelect.add(option);
        });
    }
});
</script>

<!-- CSS -->
<style>
body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
}

.btn-pink {
    background-color: #e91e63;
    color: white;
    border: none;
}

.btn-pink:hover {
    background-color: #d81b60;
}


/* Bảng nabar */

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

#productTable {
    width: 100%;
    max-width: 900px;
    margin: 0 auto;
    table-layout: fixed;
    word-wrap: break-word;
}

.table-title {
    text-align: center;
    max-width: 900px;
    margin: 20px auto 10px auto;
}

/* Nút màu hồng */
.btn-pink {
    background-color: #e91e63;
    color: white;
    font-weight: 600;
}

.btn-pink:hover {
    background-color: #c2185b;
    color: white;
}
</style>