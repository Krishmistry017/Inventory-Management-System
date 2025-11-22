<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'];
    $saleDate = $_POST['sale_date'];
    $quantity = $_POST['quantity'];
    $unitPrice = $_POST['unit_price'];
    $customer = $_POST['customer'];

    // Check available stock
    $checkStmt = $conn->prepare("SELECT stock FROM items WHERE id = ?");
    $checkStmt->bind_param("i", $itemId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $item = $result->fetch_assoc();

    if ($item['stock'] < $quantity) {
        header("Location: outgoing.php?error=insufficient_stock");
        exit;
    }

    // Record the sale
    $stmt = $conn->prepare("
        INSERT INTO sales (item_id, sale_date, quantity, unit_price, customer)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isids", $itemId, $saleDate, $quantity, $unitPrice, $customer);
    $stmt->execute();

    // Update inventory
    $updateStmt = $conn->prepare("UPDATE items SET stock = stock - ? WHERE id = ?");
    $updateStmt->bind_param("di", $quantity, $itemId);
    $updateStmt->execute();

    header("Location: outgoing.php?success=sale_recorded");
    exit;
} else {
    header("Location: outgoing.php");
    exit;
}