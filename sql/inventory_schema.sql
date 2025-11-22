-- Create a simple inventory system database
CREATE DATABASE inventory_app;

USE inventory_app;

-- Users table
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(100) NOT NULL
);

-- Items
CREATE TABLE items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  price DECIMAL(10,2),
  stock INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Item groups
CREATE TABLE item_groups (
  id INT PRIMARY KEY AUTO_INCREMENT,
  group_name VARCHAR(100) NOT NULL
);

-- Composite Items
CREATE TABLE composite_items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  composite_name VARCHAR(100) NOT NULL
);

-- Price lists
CREATE TABLE price_lists (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  discount_percent DECIMAL(5,2)
);
CREATE TABLE sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT,
  quantity_sold INT,
  sale_date DATE,
  FOREIGN KEY (item_id) REFERENCES items(id)
);

CREATE TABLE wastage (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT,
  quantity_wasted INT,
  reason VARCHAR(255),
  wastage_date DATE,
  FOREIGN KEY (item_id) REFERENCES items(id)
);
