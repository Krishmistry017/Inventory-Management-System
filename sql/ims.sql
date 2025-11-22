CREATE DATABASE inventory_db;

USE inventory_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(255)
);

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    type VARCHAR(50),
    stock INT,
    expiry_date DATE
);

CREATE TABLE incoming_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    purchase_date DATE,
    expiry_date DATE,
    quantity INT,
    unit_price DECIMAL(10, 2),
    supplier VARCHAR(100),
    FOREIGN KEY (item_id) REFERENCES items(id)
);
-- Similarly add outgoing_items, sales, wastage, etc.
