<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'];
    $wastageDate = $_POST['wastage_date'];
    $quantity = $_POST['quantity'];
    $reason = $_POST['reason'];
    $notes = $_POST['notes'];

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

    // Record the wastage
    $stmt = $conn->prepare("
        INSERT INTO wastage (item_id, wastage_date, quantity, reason, notes)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isiss", $itemId, $wastageDate, $quantity, $reason, $notes);
    $stmt->execute();

    // Update inventory
    $updateStmt = $conn->prepare("UPDATE items SET stock = stock - ? WHERE id = ?");
    $updateStmt->bind_param("di", $quantity, $itemId);
    $updateStmt->execute();

    header("Location: outgoing.php?success=wastage_recorded");
    exit;
} else {
    header("Location: outgoing.php");
    exit;
}