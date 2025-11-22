<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $itemName = $_POST['item_name'];
    $itemType = $_POST['item_type'];
    $purchaseDate = $_POST['purchase_date'];
    $expiryDate = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;
    $quantity = $_POST['quantity'];
    $unitPrice = $_POST['unit_price'];
    $supplier = $_POST['supplier'];

    // Insert into purchases table
    $stmt = $conn->prepare("
        INSERT INTO purchases (item_name, item_type, purchase_date, expiry_date, quantity, unit_price, supplier)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssdds", $itemName, $itemType, $purchaseDate, $expiryDate, $quantity, $unitPrice, $supplier);
    $stmt->execute();

    // Check if item exists in inventory
    $checkStmt = $conn->prepare("SELECT id FROM items WHERE name = ?");
    $checkStmt->bind_param("s", $itemName);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing item stock
        $updateStmt = $conn->prepare("UPDATE items SET stock = stock + ? WHERE name = ?");
        $updateStmt->bind_param("ds", $quantity, $itemName);
        $updateStmt->execute();
    } else {
        // Insert new item
        $insertStmt = $conn->prepare("
            INSERT INTO items (name, type, stock, unit_price, expiry_date)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertStmt->bind_param("ssdds", $itemName, $itemType, $quantity, $unitPrice, $expiryDate);
        $insertStmt->execute();
    }

    header("Location: incoming.php?success=1");
    exit;
} else {
    header("Location: incoming.php");
    exit;
}