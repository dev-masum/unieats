# UniEats Smart Campus Food Ordering System
## System Requirements Document

**Project:** UniEats - Smart Campus Food Ordering System  
**Team:** Sprint Squad  
**Date:** May 2026  
**Allocated Component:** Menu Management System  
**Developer:** Mst Sabrina Nasrin Jarin

---

## 1. System Overview

UniEats is a web-based food ordering system designed for university students to efficiently browse canteen menus, pre-order meals, and schedule collection times. The system reduces waiting queues, saves time, and helps students manage their daily activities more effectively.

### 1.1 Core Functionality
- Browse canteen menus
- Pre-order meals
- Schedule collection times
- Reduce waiting queues and save time
- Admin monitoring and activity logging

### 1.2 Team Allocation
- **Suriya** – AdminLogs
- **Maher** – User Management
- **Sabrina** – Menu Management
- **Sahadat** – Order Management
- **Shubhra** – Cart & Checkout

---

## 2. Architecture Overview

UniEats follows a **three-layer architecture**:

### 2.1 Presentation Layer (Frontend)
- User-facing web pages and interfaces
- Pages include: Login, Menu, Cart, Order Confirmation, Admin Dashboard
- Collects user input and displays system responses

### 2.2 Business Logic Layer (Middle Tier)
- PHP classes and processing logic
- Handles requests from the frontend
- Applies system logic and validation
- Controls data flow between frontend and backend
- Manages specific functionalities:
  - User authentication and validation
  - Menu item management (CRUD operations)
  - Cart item management
  - Order processing and validation
  - Payment handling
  - Admin activity logging

### 2.3 Data Layer (Backend)
- Relational database
- Stores all system data
- Ensures data consistency and integrity

---

## 3. Database Requirements

### 3.1 Core Tables

#### 3.1.1 Users Table
- Stores user account information
- Parent table for Cart, Orders, and AdminLogs tables
- Fields:
  - UserID (INT, Primary Key)
  - Username (VARCHAR)
  - Email (VARCHAR)
  - Password (VARCHAR)
  - UserType (ENUM: 'student', 'admin')
  - CreatedDate (DATE)

#### 3.1.2 Menu Table
- Stores all available food items
- Parent table for Cart and Orders tables
- Fields:
  - MenuID (INT, Primary Key)
  - ItemName (VARCHAR 100)
  - Price (DECIMAL 6,2)
  - Category (VARCHAR 50)
  - Availability (BOOLEAN)
  - CreatedDate (DATE)
  - Description (VARCHAR 150)

#### 3.1.3 Cart Table
- Stores temporary items selected by users
- Child table with foreign keys to Users and Menu
- Fields:
  - CartID (INT, Primary Key)
  - UserID (INT, Foreign Key → Users)
  - MenuID (INT, Foreign Key → Menu)
  - Quantity (INT)
  - AddedDate (DATE/DATETIME)

#### 3.1.4 Orders Table
- Stores confirmed orders with payment information
- Child table with foreign key to Users
- Fields:
  - OrderID (INT, Primary Key)
  - UserID (INT, Foreign Key → Users)
  - OrderDate (DATE/DATETIME)
  - CollectionTime (DATETIME)
  - TotalAmount (DECIMAL 8,2)
  - PaymentMethod (VARCHAR)
  - PaymentStatus (ENUM: 'pending', 'completed', 'failed')
  - OrderStatus (ENUM: 'placed', 'confirmed', 'ready', 'collected', 'cancelled')

#### 3.1.5 AdminLogs Table
- Stores administrative activities and system events
- Child table with foreign key to Users
- Fields:
  - LogID (INT, Primary Key)
  - AdminUserID (INT, Foreign Key → Users)
  - Action (VARCHAR)
  - Description (VARCHAR)
  - Timestamp (DATETIME)
  - RelatedOrderID (INT, Foreign Key → Orders, nullable)

---

## 4. Menu Management System (Detailed)

### 4.1 Overview
The Menu Management System handles all food items available within the application. It provides functionality to create, read, update, and delete (CRUD) menu items, ensuring that the system maintains accurate and up-to-date information.

### 4.2 Menu Item Attributes
- **MenuID**: Unique identifier for each menu item (Primary Key)
- **ItemName**: Name of the food item (VARCHAR 100)
- **Price**: Price of the item (DECIMAL 6,2)
- **Category**: Food category (VARCHAR 50)
- **Availability**: Availability status (BOOLEAN)
- **CreatedDate**: Date the item was added to the menu (DATE)
- **Description**: Item details and description (VARCHAR 150)

### 4.3 Supported Operations
- Create new menu items with all required details
- Read/display menu items to users
- Update existing menu items (price, availability, description)
- Delete menu items
- Filter and list items by category
- Filter items by availability status

### 4.4 Key Features
- Maintains consistency between database and user interface
- Supports filtering by category and availability
- Ensures smooth experience for both administrators and end users

---

## 5. Use Case: Place Food Order

### 5.1 Actor
User (Student)

### 5.2 Goal
To allow a user to log in, view menu items, add items to the cart, and place an order in the UniEats system.

### 5.3 Pre-Conditions
- The user account already exists in the Users table
- Menu items are available in the Menu table
- The database connection is active

### 5.4 Trigger
The user wants to order food using the UniEats system

### 5.5 Main Success Scenario
1. The user opens the UniEats system
2. The user logs into the system
3. The system validates the user's login details using the Users table
4. The user opens the menu page
5. The system displays available menu items from the Menu table
6. The user selects one or more food items
7. The system adds the selected items to the Cart table
8. The user reviews the cart contents
9. The user confirms the order
10. The system creates a new record in the Orders table
11. The system stores payment method and payment status in the same order record
12. The system confirms that the order has been placed successfully
13. If required, related admin activity can later be recorded in the AdminLogs table

### 5.6 Post-Conditions
- Selected items are stored in the Cart table
- A confirmed order is stored in the Orders table
- Payment information is stored in the Orders table
- Order-related admin activity may be recorded in the AdminLogs table

### 5.7 Alternative / Exception Scenarios
- **Invalid Login**: If login details are incorrect, the system denies access and shows an error message
- **No Menu Items**: If no menu items are available, the user cannot select food
- **Empty Cart**: If the cart is empty, the order cannot be placed
- **Incomplete Payment**: If payment is not completed, the order remains stored with the relevant payment status

---

## 6. System Flow

```
User Input (Frontend)
         ↓
   Business Logic (PHP)
         ↓
   Data Processing & Validation
         ↓
   Database Operations (Backend)
         ↓
   Return Response to Frontend
```

**Example - Adding Item to Cart:**
1. Frontend: User clicks "Add to Cart" button
2. PHP Logic: Receives request, validates item and quantity
3. Database: Inserts record into Cart table with UserID and MenuID
4. Frontend: Displays confirmation message

**Example - Placing an Order:**
1. Frontend: User submits checkout form with payment details
2. PHP Logic: Validates cart items, calculates total, validates payment info
3. Database: Inserts order into Orders table, clears corresponding Cart items
4. Frontend: Displays order confirmation

---

## 7. Data Relationships

### 7.1 Entity Relationships
- **Users → Cart**: One user can have multiple cart items (1:M)
- **Menu → Cart**: One menu item can be in multiple carts (1:M)
- **Users → Orders**: One user can place multiple orders (1:M)
- **Users → AdminLogs**: One admin can perform multiple actions (1:M)
- **Orders → AdminLogs**: One order can have multiple admin logs (1:M)

### 7.2 Referential Integrity
- Foreign keys ensure data consistency
- Cascade delete rules are recommended for maintaining referential integrity
- All foreign key relationships must be enforced at the database level

---

## 8. Technical Specifications

### 8.1 Technology Stack
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL/MariaDB (Relational Database)
- **Architecture**: Three-tier web application

### 8.2 Database Constraints
- Primary keys for unique identification
- Foreign keys for referential integrity
- Data type validation
- NOT NULL constraints for required fields
- Default values where applicable
- UNIQUE constraints for email addresses

### 8.3 Performance Considerations
- Indexing on frequently queried columns (UserID, MenuID)
- Efficient SQL queries for menu filtering and listing
- Proper database normalization to reduce redundancy

---

## 9. Security Requirements

### 9.1 User Authentication
- Secure password storage (hashed)
- Login validation against Users table
- Session management for authenticated users

### 9.2 Data Protection
- Enforce foreign key constraints
- Validate all user inputs
- Sanitize data before database insertion
- Prevent SQL injection attacks

### 9.3 Admin Access Control
- Restrict admin functions to authorized users
- Log all administrative activities in AdminLogs table
- Track who performed what action and when

---

## 10. Functional Requirements

### 10.1 User Management (Maher)
- Register new user accounts
- Authenticate users via login
- Manage user profiles
- Store user information securely

### 10.2 Menu Management (Sabrina)
- Display available menu items
- Filter items by category
- Show/hide items based on availability
- Manage menu item details (name, price, description)
- Add new items to menu
- Update existing menu items
- Delete menu items

### 10.3 Cart & Checkout (Shubhra)
- Add items to cart
- Remove items from cart
- Update item quantities
- Calculate cart total
- Display cart contents
- Process checkout

### 10.4 Order Management (Sahadat)
- Create order records
- Store order details (items, quantities, total)
- Capture payment information
- Track order status
- Set collection time
- Display order history

### 10.5 Admin Logs (Suriya)
- Record all administrative activities
- Log system events
- Track user actions
- Maintain audit trail
- Generate activity reports

---

## 11. Non-Functional Requirements

### 11.1 Usability
- Intuitive user interface
- Easy navigation between pages
- Clear error messages
- Responsive design

### 11.2 Reliability
- Database backup and recovery procedures
- Error handling and logging
- Data consistency validation

### 11.3 Performance
- Fast response times for menu queries
- Efficient cart operations
- Quick order processing

### 11.4 Maintainability
- Clean, documented code
- Proper separation of concerns
- Modular design

---

## 12. Assumptions

- University provides stable network connectivity
- Users have valid university email addresses
- Menu items are pre-configured by administrators
- Payment gateway will be integrated separately
- All users have access to a modern web browser

---

## 13. Constraints

- University assignment scope - not production-ready
- Limited to web-based interface
- Single university campus implementation
- Standard relational database technology
- Three-tier architecture must be maintained

---

**Document Version:** 1.0  
**Last Updated:** May 2026
