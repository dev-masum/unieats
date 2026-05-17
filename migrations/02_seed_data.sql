-- 1. Users
INSERT INTO Users (Username, Email, Password, UserType, FullName, PhoneNumber, CreatedDate, IsActive)
VALUES
    ('student1', 'student1@university.edu', '$2y$10$sYpx7oHD2C7B3jR1cnMwp.TSf7I6iAHOROWdXzFuevnW5dwjM7C/W', 'student', 'John Doe', '0123456789', '2026-01-15', TRUE),
    ('student2', 'student2@university.edu', '$2y$10$sYpx7oHD2C7B3jR1cnMwp.TSf7I6iAHOROWdXzFuevnW5dwjM7C/W', 'student', 'Jane Smith', '0123456790', '2026-02-10', TRUE),
    ('admin1', 'admin1@university.edu', '$2y$10$UjtTQe3DGD1TQKTmeXjlve8G3LZexZy/K9qPd31HWVfibGWk0t3b2', 'admin', 'Admin User', '0987654321', '2026-01-01', TRUE),
    ('student3', 'student3@university.edu', '$2y$10$sYpx7oHD2C7B3jR1cnMwp.TSf7I6iAHOROWdXzFuevnW5dwjM7C/W', 'student', 'Bob Johnson', '0123456791', '2026-03-05', TRUE);

-- 2. Menu Items
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

-- 3. Cart Items
INSERT INTO Cart (UserID, MenuID, Quantity, AddedDate)
VALUES
    (1, 1, 1, NOW()),
    (1, 5, 2, NOW()),
    (2, 4, 1, DATE_SUB(NOW(), INTERVAL 2 HOUR)),
    (2, 7, 1, DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- 4. Orders
INSERT INTO Orders (UserID, OrderDate, CollectionTime, TotalAmount, PaymentMethod, PaymentStatus, OrderStatus, SpecialInstructions)
VALUES
    (1, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 5 DAY), INTERVAL 2 HOUR), 15.49, 'Credit Card', 'completed', 'collected', 'No onions please'),
    (2, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 3 DAY), INTERVAL 90 MINUTE), 10.49, 'Debit Card', 'completed', 'collected', 'Extra spicy'),
    (3, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 1 DAY), INTERVAL 45 MINUTE), 8.50, 'Cash', 'pending', 'ready', NULL);

-- 5. Order Items
INSERT INTO OrderItems (OrderID, MenuID, Quantity, PriceAtOrder)
VALUES
    (1, 1, 1, 8.50),
    (1, 5, 2, 5.50),
    (2, 4, 1, 7.50),
    (2, 7, 1, 2.99),
    (3, 2, 1, 5.99);

-- 6. Admin Logs
INSERT INTO AdminLogs (AdminUserID, Action, Description, Timestamp, RelatedOrderID, RelatedUserID, IPAddress)
VALUES
    (3, 'Order Status Update', 'Order #1 marked as collected', DATE_ADD(DATE_SUB(NOW(), INTERVAL 5 DAY), INTERVAL 135 MINUTE), 1, 1, '192.168.1.100'),
    (3, 'Order Status Update', 'Order #2 marked as collected', DATE_ADD(DATE_SUB(NOW(), INTERVAL 3 DAY), INTERVAL 105 MINUTE), 2, 2, '192.168.1.101'),
    (3, 'Order Status Update', 'Order #3 marked as ready', DATE_ADD(DATE_SUB(NOW(), INTERVAL 1 DAY), INTERVAL 45 MINUTE), 3, 3, '192.168.1.102'),
    (3, 'User Account Created', 'New user student3 registered', DATE_SUB(NOW(), INTERVAL 10 DAY), NULL, 4, '192.168.1.103');
