<?php

function redirect(string $location): void
{
    header('Location: ' . $location, true, 302);
    exit();
}

function html_escape(string|null $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8', false);
}



function getRandomBooks(PDO $conn, int $limit): array
{
    // Kiểm tra giới hạn hợp lệ
    if ($limit <= 0) {
        return []; // Trả về mảng rỗng nếu $limit không hợp lệ
    }

    // Câu SQL để lấy sách ngẫu nhiên
    $sql = "SELECT * FROM sach ORDER BY RAND() LIMIT :limit";
    
    // Chuẩn bị truy vấn
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT); // Gắn giá trị cho tham số :limit
    $stmt->execute();
    
    // Lấy kết quả
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về mảng kết hợp
    return $books;
}




