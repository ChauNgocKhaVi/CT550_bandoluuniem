<?php
namespace CT550\Labs;

use PDO;
use PDOException;

class User {
    private ?PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Đăng ký tài khoản mới
     */
    public function register(
        string $username,
        string $full_name,
        string $email,
        string $password,
        string $phone_number,
        string $role = 'customer'
    ): bool {
        try {
            // Validate cơ bản
            if (empty($username) || empty($full_name) || empty($email) || empty($password)) {
                throw new \Exception("Các trường bắt buộc không được để trống.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Email không hợp lệ.");
            }

            if (strlen($password) < 6) {
                throw new \Exception("Mật khẩu phải có ít nhất 6 ký tự.");
            }

            // Kiểm tra trùng username hoặc email
            $checkStmt = $this->db->prepare(
                "SELECT user_id FROM Users WHERE username = :username OR email = :email"
            );
            $checkStmt->execute([
                ':username' => $username,
                ':email'    => $email
            ]);
            if ($checkStmt->fetch()) {
                throw new \Exception("Tên đăng nhập hoặc email đã tồn tại.");
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Thêm vào DB
            $stmt = $this->db->prepare("
                INSERT INTO Users (username, full_name, email, password, phone_number, role)
                VALUES (:username, :full_name, :email, :password, :phone_number, :role)
            ");

            return $stmt->execute([
                ':username'     => $username,
                ':full_name'    => $full_name,
                ':email'        => $email,
                ':password'     => $hashedPassword,
                ':phone_number' => $phone_number,
                ':role'         => $role
            ]);

        } catch (PDOException $e) {
            throw new \Exception("Lỗi CSDL: " . $e->getMessage());
        }
    }

    // Đăng nhập
    public function login($usernameOrEmail, $password) {
        try {
            // Cho phép đăng nhập bằng username hoặc email
            $stmt = $this->db->prepare("SELECT * FROM Users WHERE username = :ue OR email = :ue LIMIT 1");
            $stmt->execute([':ue' => $usernameOrEmail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Nếu mật khẩu đúng thì trả về thông tin user (trừ password)
                unset($user['password']);
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }


    /**
     * Xử lý form thêm người dùng
     */
    public function handleAddUserForm(array $postData): string {
        $message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username   = trim($postData['username'] ?? '');
            $full_name  = trim($postData['full_name'] ?? '');
            $email      = trim($postData['email'] ?? '');
            $phone      = trim($postData['phone_number'] ?? '');
            $password   = $postData['password'] ?? '';

            // Kiểm tra dữ liệu hợp lệ
            if (empty($username) || empty($full_name) || empty($email) || empty($password)) {
                return "<div class='alert alert-danger'>Vui lòng điền đầy đủ thông tin.</div>";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "<div class='alert alert-danger'>Email không hợp lệ.</div>";
            }

            if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
                return "<div class='alert alert-danger'>Số điện thoại chỉ gồm 10-11 chữ số.</div>";
            }

            try {
                // 🔍 Kiểm tra trùng username, email hoặc số điện thoại
                $checkStmt = $this->db->prepare("
                    SELECT * FROM Users 
                    WHERE username = :username OR email = :email OR phone_number = :phone
                    LIMIT 1
                ");
                $checkStmt->execute([
                    ':username' => $username,
                    ':email'    => $email,
                    ':phone'    => $phone
                ]);
                $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {
                    if ($existing['username'] === $username) {
                        return "<div class='alert alert-danger'>Tên đăng nhập đã tồn tại.</div>";
                    }
                    if ($existing['email'] === $email) {
                        return "<div class='alert alert-danger'>Email đã tồn tại.</div>";
                    }
                    if ($existing['phone_number'] === $phone) {
                        return "<div class='alert alert-danger'>Số điện thoại đã tồn tại.</div>";
                    }
                }

                // Nếu không trùng thì thêm mới
                $hashed_pw = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $this->db->prepare("
                    INSERT INTO Users (username, full_name, email, password, phone_number) 
                    VALUES (:username, :full_name, :email, :password, :phone_number)
                ");
                $stmt->execute([
                    ':username'     => $username,
                    ':full_name'    => $full_name,
                    ':email'        => $email,
                    ':password'     => $hashed_pw,
                    ':phone_number' => $phone
                ]);

                $message = "<div class='alert alert-success'>Thêm người dùng thành công!</div>";
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger'>Lỗi: " . $e->getMessage() . "</div>";
            }
        }

        return $message;
    }


    // Chỉnh sửa của user

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function handleEditUserForm($user_id, $data)
    {
        $username = trim($data['username']);
        $full_name = trim($data['full_name']);
        $email = trim($data['email']);
        $phone = trim($data['phone_number']);
        $password = trim($data['password']);

        if (empty($username) || empty($full_name) || empty($email)) {
            return "<div class='alert alert-danger text-center'>❌ Vui lòng điền đầy đủ thông tin bắt buộc.</div>";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "<div class='alert alert-danger text-center'>📧 Email không hợp lệ.</div>";
        }

        if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            return "<div class='alert alert-danger text-center'>📱 Số điện thoại chỉ gồm 10–11 chữ số.</div>";
        }

        try {
            // --- Kiểm tra trùng tên đăng nhập ---
            $checkUsername = $this->db->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
            $checkUsername->execute([$username, $user_id]);
            if ($checkUsername->fetch()) {
                return "<div class='alert alert-warning text-center'>⚠️ Tên đăng nhập đã tồn tại.</div>";
            }

            // --- Kiểm tra trùng email ---
            $checkEmail = $this->db->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            $checkEmail->execute([$email, $user_id]);
            if ($checkEmail->fetch()) {
                return "<div class='alert alert-warning text-center'>⚠️ Email đã tồn tại.</div>";
            }

            // --- Kiểm tra trùng số điện thoại ---
            $checkPhone = $this->db->prepare("SELECT user_id FROM users WHERE phone_number = ? AND user_id != ?");
            $checkPhone->execute([$phone, $user_id]);
            if ($checkPhone->fetch()) {
                return "<div class='alert alert-warning text-center'>⚠️ Số điện thoại đã tồn tại.</div>";
            }

            // --- Cập nhật dữ liệu ---
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users 
                        SET username=?, full_name=?, email=?, phone_number=?, password=? 
                        WHERE user_id=?";
                $params = [$username, $full_name, $email, $phone, $hashedPassword, $user_id];
            } else {
                $sql = "UPDATE users 
                        SET username=?, full_name=?, email=?, phone_number=? 
                        WHERE user_id=?";
                $params = [$username, $full_name, $email, $phone, $user_id];
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return "<div class='alert alert-success text-center'>✅ Cập nhật thông tin người dùng thành công!</div>";

        } catch (PDOException $e) {
            return "<div class='alert alert-danger text-center'>⚠️ Lỗi hệ thống, vui lòng thử lại sau.</div>";
        }
    }

    // Xóa người dùng
    public function deleteUser($user_id): string
    {
        try {
            // Kiểm tra user có tồn tại không
            $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return "<div class='alert alert-warning text-center'>⚠️ Người dùng không tồn tại.</div>";
            }

            // Thực hiện xóa
            $deleteStmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            $deleteStmt->execute([$user_id]);

            return "<div class='alert alert-success text-center'>✅ Đã xóa người dùng thành công!</div>";

        } catch (PDOException $e) {
            return "<div class='alert alert-danger text-center'>❌ Lỗi khi xóa người dùng: {$e->getMessage()}</div>";
        }
    }


}