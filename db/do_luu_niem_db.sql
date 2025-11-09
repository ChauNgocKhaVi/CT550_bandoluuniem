-- Bảng người dùng
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    role ENUM('customer','admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng danh mục sản phẩm
CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
    
);

-- 🔹 Bảng thương hiệu (đơn giản)
CREATE TABLE Brands (
    brand_id INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(100) NOT NULL
);

-- 🔹 Bảng sản phẩm
CREATE TABLE Products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    brand_id INT,
    product_name VARCHAR(150) NOT NULL,
    description TEXT,
    original_price DECIMAL(10,2),
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    sold_quantity INT DEFAULT 0 AFTER stock_quantity,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id),
    FOREIGN KEY (brand_id) REFERENCES Brands(brand_id)
);

-- Bảng đơn hàng
CREATE TABLE Orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('pending','confirmed','shipping','delivered','canceled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('unpaid','paid') DEFAULT 'unpaid',
    payment_method ENUM('cash','bank_transfer','credit_card') DEFAULT 'cash',
    shipping_address VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

-- Bảng chi tiết đơn hàng
CREATE TABLE OrderDetails (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id)
);

-- Bảng đánh giá sản phẩm
CREATE TABLE Reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES Products(product_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);


