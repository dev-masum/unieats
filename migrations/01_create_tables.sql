-- 1. USERS TABLE
CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    UserType ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    FullName VARCHAR(100),
    PhoneNumber VARCHAR(15),
    CreatedDate DATE NOT NULL,
    LastLogin DATETIME,
    IsActive BOOLEAN DEFAULT TRUE,
    INDEX idx_username (Username),
    INDEX idx_email (Email)
);

-- 2. MENU TABLE
CREATE TABLE IF NOT EXISTS Menu (
    MenuID INT AUTO_INCREMENT PRIMARY KEY,
    ItemName VARCHAR(100) NOT NULL,
    Price DECIMAL(6,2) NOT NULL,
    Category VARCHAR(50) NOT NULL,
    Availability BOOLEAN NOT NULL DEFAULT TRUE,
    CreatedDate DATE NOT NULL,
    Description VARCHAR(150),
    ImageURL VARCHAR(255),
    PrepTime INT,
    INDEX idx_category (Category),
    INDEX idx_availability (Availability)
);

-- 3. CART TABLE
CREATE TABLE IF NOT EXISTS Cart (
    CartID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    MenuID INT NOT NULL,
    Quantity INT NOT NULL DEFAULT 1,
    AddedDate DATETIME NOT NULL,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (MenuID) REFERENCES Menu(MenuID) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_user_menu (UserID, MenuID),
    INDEX idx_user_id (UserID),
    INDEX idx_menu_id (MenuID)
);

-- 4. ORDERS TABLE
CREATE TABLE IF NOT EXISTS Orders (
    OrderID INT AUTO_INCREMENT PRIMARY KEY,
    UserID INT NOT NULL,
    OrderDate DATETIME NOT NULL,
    CollectionTime DATETIME NOT NULL,
    TotalAmount DECIMAL(8,2) NOT NULL,
    PaymentMethod VARCHAR(50) NOT NULL,
    PaymentStatus ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    OrderStatus ENUM('placed', 'confirmed', 'ready', 'collected', 'cancelled') NOT NULL DEFAULT 'placed',
    SpecialInstructions VARCHAR(255),
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user_id (UserID),
    INDEX idx_order_date (OrderDate),
    INDEX idx_order_status (OrderStatus),
    INDEX idx_payment_status (PaymentStatus)
);

-- 5. ORDER ITEMS TABLE
CREATE TABLE IF NOT EXISTS OrderItems (
    OrderItemID INT AUTO_INCREMENT PRIMARY KEY,
    OrderID INT NOT NULL,
    MenuID INT NOT NULL,
    Quantity INT NOT NULL,
    PriceAtOrder DECIMAL(6,2) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES Orders(OrderID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (MenuID) REFERENCES Menu(MenuID) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_order_id (OrderID),
    INDEX idx_menu_id (MenuID)
);

-- 6. ADMIN LOGS TABLE
CREATE TABLE IF NOT EXISTS AdminLogs (
    LogID INT AUTO_INCREMENT PRIMARY KEY,
    AdminUserID INT NOT NULL,
    Action VARCHAR(100) NOT NULL,
    Description VARCHAR(255),
    Timestamp DATETIME NOT NULL,
    RelatedOrderID INT,
    RelatedUserID INT,
    IPAddress VARCHAR(45),
    FOREIGN KEY (AdminUserID) REFERENCES Users(UserID) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (RelatedOrderID) REFERENCES Orders(OrderID) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (RelatedUserID) REFERENCES Users(UserID) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_admin_user_id (AdminUserID),
    INDEX idx_timestamp (Timestamp),
    INDEX idx_action (Action),
    INDEX idx_related_order_id (RelatedOrderID)
);
