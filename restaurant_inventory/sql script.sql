CREATE DATABASE IF NOT EXISTS restaurant_inventory;
USE restaurant_inventory;

-- Ingredients Table
CREATE TABLE Ingredients (
    ingredient_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    unit VARCHAR(20),
    stock_level DECIMAL(10,2) DEFAULT 0,
    reorder_threshold DECIMAL(10,2) DEFAULT 0
);

-- Suppliers Table
CREATE TABLE Suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20)
);

-- Inventory Transactions Table
CREATE TABLE Inventory_Transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ingredient_id) REFERENCES Ingredients(ingredient_id)
);

-- Purchase Orders Table
CREATE TABLE PurchaseOrders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT NOT NULL,
    supplier_id INT NOT NULL,
    order_quantity DECIMAL(10,2),
    order_date DATE,
    FOREIGN KEY (ingredient_id) REFERENCES Ingredients(ingredient_id),
    FOREIGN KEY (supplier_id) REFERENCES Suppliers(supplier_id)
);

-- Sample Data
INSERT INTO Ingredients (name, unit, stock_level, reorder_threshold)
VALUES
('Tomatoes', 'kg', 25.5, 10),
('Flour', 'kg', 40, 15),
('Cheese', 'kg', 10, 5);

INSERT INTO Suppliers (name, contact_email, contact_phone)
VALUES
('MooMooFarms', 'contact@moofarms.com', '1234567890'),
('BakingTypeShi Co.', 'hello@bakeshi.com', '9876543210');

INSERT INTO Inventory_Transactions (ingredient_id, type, quantity)
VALUES
(1, 'OUT', 2),
(2, 'IN', 5),
(3, 'OUT', 1.5);

INSERT INTO PurchaseOrders (ingredient_id, supplier_id, order_quantity, order_date)
VALUES
(1, 1, 20, '2025-05-10'),
(2, 2, 50, '2025-05-12');
