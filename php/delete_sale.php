<?php
session_start();
require 'db_connect.php';

if (isset($_GET['id'])) {
    // First get the sale details to restore inventory
    $stmt = $conn->prepare("
        SELECT item_id, quantity 
        FROM sales 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $sale = $result->fetch_assoc();
    
    // Restore inventory by adding back the quantity
    $updateStmt = $conn->prepare("
        UPDATE items 
        SET stock = stock + ? 
        WHERE id = ?
    ");
    $updateStmt->bind_param("di", $sale['quantity'], $sale['item_id']);
    $updateStmt->execute();
    
    // Now delete the sale record
    $deleteStmt = $conn->prepare("DELETE FROM sales WHERE id = ?");
    $deleteStmt->bind_param("i", $_GET['id']);
    $deleteStmt->execute();
    
    header("Location: outgoing.php?success=sale_deleted");
    exit;
} else {
    header("Location: outgoing.php");
    exit;
}