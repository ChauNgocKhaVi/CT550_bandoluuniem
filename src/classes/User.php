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
}