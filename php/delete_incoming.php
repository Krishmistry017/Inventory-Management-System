<?php
session_start();
require 'db_connect.php';

if (isset($_GET['id'])) {
    // First get the purchase details to update inventory
    $stmt = $conn->prepare("
        SELECT item_name, quantity 
        FROM purchases 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $purchase = $result->fetch_assoc();
    
    // Update inventory by reducing the stock
    $updateStmt = $conn->prepare("
        UPDATE items 
        SET stock = stock - ? 
        WHERE name = ?
    ");
    $updateStmt->bind_param("ds", $purchase['quantity'], $purchase['item_name']);
    $updateStmt->execute();
    
    // Now delete the purchase record
    $deleteStmt = $conn->prepare("DELETE FROM purchases WHERE id = ?");
    $deleteStmt->bind_param("i", $_GET['id']);
    $deleteStmt->execute();
    
    header("Location: incoming.php?success=1");
    exit;
} else {
    header("Location: incoming.php");
    exit;
}