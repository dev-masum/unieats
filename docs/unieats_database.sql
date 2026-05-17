-- ============================================================================
-- UniEats Smart Campus Food Ordering System - Database Schema
-- ============================================================================
-- Project: UniEats
-- Team: Sprint Squad
-- Date: May 2026
-- Purpose: Create complete database structure for the food ordering system
-- ============================================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS unieats_db;
USE unieats_db;

-- ============================================================================
-- 1. USERS TABLE - Stores user account information
-- ============================================================================
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
) ;

-- ============================================================================
-- 2. MENU TABLE - Stores all available food items
-- ============================================================================
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

-- ============================================================================
-- 3. CART TABLE - Stores items temporarily added by users
-- Parent table: Users (UserID), Menu (MenuID)
-- ============================================================================
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

-- ============================================================================
-- 4. ORDERS TABLE - Stores confirmed orders with payment information
-- Parent table: Users (UserID)
-- ============================================================================
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

-- ============================================================================
-- 5. ORDER_ITEMS TABLE - Stores individual items within each order
-- This table maintains the relationship between Orders and Menu items
-- ============================================================================
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

-- ============================================================================
-- 6. ADMINLOGS TABLE - Stores administrative activities and system events
-- Parent tables: Users (AdminUserID), Orders (RelatedOrderID - optional)
-- ============================================================================
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

-- ============================================================================
-- SAMPLE DATA INSERTION
-- ============================================================================

-- Insert Sample Users
INSERT INTO Users (Username, Email, Password, UserType, FullName, PhoneNumber, CreatedDate, IsActive)
VALUES
    ('student1', 'student1@university.edu', 'hashed_password_123', 'student', 'John Doe', '0123456789', '2026-01-15', TRUE),
    ('student2', 'student2@university.edu', 'hashed_password_456', 'student', 'Jane Smith', '0123456790', '2026-02-10', TRUE),
    ('admin1', 'admin1@university.edu', 'hashed_password_admin', 'admin', 'Admin User', '0987654321', '2026-01-01', TRUE),
    ('student3', 'student3@university.edu', 'hashed_password_789', 'student', 'Bob Johnson', '0123456791', '2026-03-05', TRUE);

-- Insert Sample Menu Items
INSERT INTO Menu (ItemName, Price, Category, Availability, CreatedDate, Description, PrepTime)
VALUES
    ('Chicken Biryani', 8.50, 'Main Course', TRUE, '2026-01-20', 'Fragrant rice with spiced chicken', 20),
    ('Vegetable Curry', 5.99, 'Main Course', TRUE, '2026-01-20', 'Mixed vegetables in aromatic spices', 15),
    ('Butter Chicken', 9.99, 'Main Course', TRUE, '2026-02-01', 'Tender chicken in creamy butter sauce', 18),
    ('Margherita Pizza', 7.50, 'Pizza', TRUE, '2026-02-05', 'Fresh mozzarella, basil, tomato sauce', 12),
    ('Caesar Salad', 5.50, 'Salad', TRUE, '2026-02-10', 'Crispy romaine with parmesan and croutons', 5),
    ('Chocolate Cake', 3.99, 'Dessert', TRUE, '2026-02-15', 'Moist chocolate cake with frosting', 0),
    ('Iced Coffee', 2.99, 'Beverage', TRUE, '2026-02-20', 'Chilled coffee with ice', 3),
    ('Noodle Soup', 6.50, 'Main Course', TRUE, '2026-03-01', 'Egg noodles in aromatic broth', 10),
    ('Sandwich Deluxe', 4.99, 'Sandwich', TRUE, '2026-03-05', 'Multi-layered sandwich with fresh ingredients', 8),
    ('Fruit Smoothie', 3.50, 'Beverage', TRUE, '2026-03-10', 'Blended fresh fruits', 5);

-- Insert Sample Cart Items
INSERT INTO Cart (UserID, MenuID, Quantity, AddedDate)
VALUES
    (1, 1, 1, NOW()),
    (1, 5, 2, NOW()),
    (2, 4, 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
    (2, 7, 1, DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- Insert Sample Orders
INSERT INTO Orders (UserID, OrderDate, CollectionTime, TotalAmount, PaymentMethod, PaymentStatus, OrderStatus, SpecialInstructions)
VALUES
    (1, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY) + INTERVAL 2 HOUR, 15.49, 'Credit Card', 'completed', 'collected', 'No onions please'),
    (2, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY) + INTERVAL 1 HOUR 30 MINUTE, 10.49, 'Debit Card', 'completed', 'collected', 'Extra spicy'),
    (3, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 45 MINUTE, 8.50, 'Cash', 'pending', 'ready', NULL);

-- Insert Sample Order Items
INSERT INTO OrderItems (OrderID, MenuID, Quantity, PriceAtOrder)
VALUES
    (1, 1, 1, 8.50),
    (1, 5, 2, 5.50),
    (2, 4, 1, 7.50),
    (2, 7, 1, 2.99),
    (3, 2, 1, 5.99);

-- Insert Sample Admin Logs
INSERT INTO AdminLogs (AdminUserID, Action, Description, Timestamp, RelatedOrderID, RelatedUserID, IPAddress)
VALUES
    (3, 'Order Status Update', 'Order #1 marked as collected', DATE_SUB(NOW(), INTERVAL 5 DAY) + INTERVAL 2 HOUR 15 MINUTE, 1, 1, '192.168.1.100'),
    (3, 'Order Status Update', 'Order #2 marked as collected', DATE_SUB(NOW(), INTERVAL 3 DAY) + INTERVAL 1 HOUR 45 MINUTE, 2, 2, '192.168.1.101'),
    (3, 'Order Status Update', 'Order #3 marked as ready', DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 45 MINUTE, 3, 3, '192.168.1.102'),
    (3, 'User Account Created', 'New user student3 registered', DATE_SUB(NOW(), INTERVAL 10 DAY), NULL, 4, '192.168.1.103');

-- ============================================================================
-- VIEWS FOR COMMON QUERIES
-- ============================================================================

-- View 1: Current Cart Details with Item Information
CREATE OR REPLACE VIEW CartDetails AS
SELECT 
    c.CartID,
    u.UserID,
    u.Username,
    c.MenuID,
    m.ItemName,
    m.Price,
    m.Category,
    c.Quantity,
    (c.Quantity * m.Price) AS SubTotal,
    c.AddedDate
FROM Cart c
JOIN Users u ON c.UserID = u.UserID
JOIN Menu m ON c.MenuID = m.MenuID;

-- View 2: Order Summary with Customer Information
CREATE OR REPLACE VIEW OrderSummary AS
SELECT 
    o.OrderID,
    u.UserID,
    u.Username,
    u.Email,
    o.OrderDate,
    o.CollectionTime,
    o.TotalAmount,
    o.PaymentMethod,
    o.PaymentStatus,
    o.OrderStatus,
    COUNT(oi.OrderItemID) AS ItemCount
FROM Orders o
JOIN Users u ON o.UserID = u.UserID
LEFT JOIN OrderItems oi ON o.OrderID = oi.OrderID
GROUP BY o.OrderID;

-- View 3: Menu Availability Report
CREATE OR REPLACE VIEW MenuAvailabilityReport AS
SELECT 
    MenuID,
    ItemName,
    Price,
    Category,
    Availability,
    CASE 
        WHEN Availability = TRUE THEN 'Available'
        ELSE 'Not Available'
    END AS AvailabilityStatus,
    CreatedDate
FROM Menu
ORDER BY Category, ItemName;

-- ============================================================================
-- INDEXES FOR PERFORMANCE OPTIMIZATION
-- ============================================================================

CREATE INDEX idx_cart_user_date ON Cart(UserID, AddedDate);
CREATE INDEX idx_orders_user_date ON Orders(UserID, OrderDate);
CREATE INDEX idx_order_items_order ON OrderItems(OrderID);
CREATE INDEX idx_adminlogs_user_timestamp ON AdminLogs(AdminUserID, Timestamp);

-- ============================================================================
-- STORED PROCEDURES (Optional - for common operations)
-- ============================================================================

-- Procedure 1: Get User Cart Total
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS GetCartTotal(IN userId INT, OUT cartTotal DECIMAL(8,2))
BEGIN
    SELECT SUM(c.Quantity * m.Price) INTO cartTotal
    FROM Cart c
    JOIN Menu m ON c.MenuID = m.MenuID
    WHERE c.UserID = userId;
END //
DELIMITER ;

-- Procedure 2: Place Order from Cart
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS PlaceOrderFromCart(
    IN userId INT,
    IN collectionTime DATETIME,
    IN paymentMethod VARCHAR(50),
    IN specialInstructions VARCHAR(255)
)
BEGIN
    DECLARE orderId INT;
    DECLARE cartTotal DECIMAL(8,2);
    
    START TRANSACTION;
    
    -- Get cart total
    SELECT SUM(c.Quantity * m.Price) INTO cartTotal
    FROM Cart c
    JOIN Menu m ON c.MenuID = m.MenuID
    WHERE c.UserID = userId;
    
    -- Insert new order
    INSERT INTO Orders (UserID, OrderDate, CollectionTime, TotalAmount, PaymentMethod, PaymentStatus, OrderStatus, SpecialInstructions)
    VALUES (userId, NOW(), collectionTime, cartTotal, paymentMethod, 'pending', 'placed', specialInstructions);
    
    SET orderId = LAST_INSERT_ID();
    
    -- Copy cart items to order items
    INSERT INTO OrderItems (OrderID, MenuID, Quantity, PriceAtOrder)
    SELECT orderId, MenuID, Quantity, (SELECT Price FROM Menu WHERE MenuID = c.MenuID)
    FROM Cart c
    WHERE c.UserID = userId;
    
    -- Clear cart
    DELETE FROM Cart WHERE UserID = userId;
    
    COMMIT;
    SELECT orderId AS NewOrderID;
END //
DELIMITER ;

-- ============================================================================
-- END OF DATABASE SCHEMA
-- ============================================================================
-- Description: This script creates a complete database structure for the
-- UniEats Smart Campus Food Ordering System with proper relationships,
-- constraints, and sample data for testing purposes.
-- ============================================================================
