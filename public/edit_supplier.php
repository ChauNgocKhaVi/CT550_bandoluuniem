<?php
require_once __DIR__ . '/../src/bootstrap.php';

// ✅ Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../dang_nhap.php");
    exit;
}

$pdo = $PDO;
$message = "";

// ✅ Lấy ID nhà cung cấp từ URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: suppliers.php');
    exit;
}

$supplier_id = (int) $_GET['id'];

// ✅ Lấy thông tin nhà cung cấp hiện tại
$stmt = $pdo->prepare("SELECT * FROM Suppliers WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    $_SESSION['message'] = '<div class="alert alert-danger text-center">Không tìm thấy nhà cung cấp!</div>';
    header('Location: suppliers.php');
    exit;
}

// ✅ Khi nhấn nút cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['supplier_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $ward = trim($_POST['ward'] ?? '');
    $address_detail = trim($_POST['address_detail'] ?? '');

    $full_address = $address_detail . ', ' . $ward . ', ' . $district . ', ' . $province;

    if ($name === '') {
        $message = '<div class="alert alert-danger text-center">Vui lòng nhập tên nhà cung cấp.</div>';
    } else {
        $stmt = $pdo->prepare("
            UPDATE Suppliers
            SET supplier_name = :name, phone = :phone, address = :address, email = :email
            WHERE supplier_id = :id
        ");
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':address' => $full_address,
            ':email' => $email,
            ':id' => $supplier_id
        ]);

        $_SESSION['message'] = '<div class="alert alert-success text-center">Cập nhật thông tin nhà cung cấp thành công!</div>';
        header('Location: suppliers_admin.php');
        exit;
    }
}

// ✅ Tách địa chỉ cũ ra để hiển thị lại (nếu có)
$old_address = explode(', ', $supplier['address']);
$province_val = $old_address[count($old_address) - 1] ?? '';
$district_val = $old_address[count($old_address) - 2] ?? '';
$ward_val = $old_address[count($old_address) - 3] ?? '';
$detail_val = $old_address[0] ?? '';

include __DIR__ . '/../src/partials/head.php';
include __DIR__ . '/../src/partials/header_admin.php';
?>

<div class="container mt-4">
    <h3 class="text-pink mb-4 text-center">Chỉnh sửa nhà cung cấp</h3>

    <?= $message ?>

    <form method="POST" class="mx-auto shadow-sm p-4 rounded bg-white" style="max-width: 600px;">
        <div class="mb-3">
            <label for="supplier_name" class="form-label fw-semibold">Tên nhà cung cấp <span
                    class="text-danger">*</span></label>
            <input type="text" class="form-control" id="supplier_name" name="supplier_name"
                value="<?= htmlspecialchars($supplier['supplier_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
            <input type="text" class="form-control" id="phone" name="phone"
                value="<?= htmlspecialchars($supplier['phone']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Địa chỉ</label>
            <div class="row g-2">
                <div class="col-md-12">
                    <input type="text" id="address_detail" name="address_detail" class="form-control"
                        placeholder="Số nhà, tên đường..." value="<?= htmlspecialchars($detail_val) ?>">
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

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                value="<?= htmlspecialchars($supplier['email']) ?>">
        </div>

        <div class="d-flex justify-content-center mt-4 gap-3">
            <button type="submit" class="btn btn-pink px-4">
                <i class="bi bi-save"></i> Cập nhật
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

// 🔹 Lấy giá trị cũ từ PHP
const oldProvince = "<?= htmlspecialchars($province_val) ?>";
const oldDistrict = "<?= htmlspecialchars($district_val) ?>";
const oldWard = "<?= htmlspecialchars($ward_val) ?>";

// 🔹 1. Tải dữ liệu từ file nội bộ
fetch("data/vietnam.json")
    .then(res => res.json())
    .then(data => {
        provincesData = data;
        data.forEach(p => {
            const option = new Option(p.Name, p.Name);
            option.dataset.code = p.Id;
            provinceSelect.add(option);
        });

        // Hiển thị lại tỉnh đã chọn
        if (oldProvince) {
            provinceSelect.value = oldProvince;
            loadDistricts();
        }
    });

// 🔹 2. Khi chọn tỉnh → load huyện
provinceSelect.addEventListener("change", loadDistricts);

function loadDistricts() {
    const provinceCode = provinceSelect.options[provinceSelect.selectedIndex].dataset.code;
    const province = provincesData.find(p => p.Id == provinceCode);
    districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    province?.Districts.forEach(d => {
        const option = new Option(d.Name, d.Name);
        option.dataset.code = d.Id;
        districtSelect.add(option);
    });
    if (oldDistrict && districtSelect.querySelector(`option[value="${oldDistrict}"]`)) {
        districtSelect.value = oldDistrict;
        loadWards();
    }
}

// 🔹 3. Khi chọn huyện → load xã
districtSelect.addEventListener("change", loadWards);

function loadWards() {
    const provinceCode = provinceSelect.options[provinceSelect.selectedIndex].dataset.code;
    const districtCode = districtSelect.options[districtSelect.selectedIndex].dataset.code;
    const province = provincesData.find(p => p.Id == provinceCode);
    const district = province?.Districts.find(d => d.Id == districtCode);
    wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
    district?.Wards.forEach(w => {
        const option = new Option(w.Name, w.Name);
        option.dataset.code = w.Id;
        wardSelect.add(option);
    });
    if (oldWard && wardSelect.querySelector(`option[value="${oldWard}"]`)) {
        wardSelect.value = oldWard;
    }
}
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