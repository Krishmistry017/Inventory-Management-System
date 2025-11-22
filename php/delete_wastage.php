<?php
session_start();
require 'db_connect.php';

if (isset($_GET['id'])) {
    // First get the wastage details to restore inventory
    $stmt = $conn->prepare("
        SELECT item_id, quantity 
        FROM wastage 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $wastage = $result->fetch_assoc();
    
    // Restore inventory by adding back the quantity
    $updateStmt = $conn->prepare("
        UPDATE items 
        SET stock = stock + ? 
        WHERE id = ?
    ");
    $updateStmt->bind_param("di", $wastage['quantity'], $wastage['item_id']);
    $updateStmt->execute();
    
    // Now delete the wastage record
    $deleteStmt = $conn->prepare("DELETE FROM wastage WHERE id = ?");
    $deleteStmt->bind_param("i", $_GET['id']);
    $deleteStmt->execute();
    
    header("Location: outgoing.php?success=wastage_deleted");
    exit;
} else {
    header("Location: outgoing.php");
    exit;
}