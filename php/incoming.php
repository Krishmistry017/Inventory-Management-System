<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// Display success message if present
$success = isset($_GET['success']) ? $_GET['success'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Incoming Items - Inventory App</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- INCOMING ITEMS PAGE -->
<section id="incoming-page" class="page">
  <!-- Sidebar remains the same as your original -->
  
  <main class="main-content">
    <h1>Incoming Items</h1>
    
    <?php if ($success): ?>
    <div class="alert success">
        Item successfully added to inventory!
    </div>
    <?php endif; ?>
    
    <div class="action-bar">
      <button onclick="showAddIncomingForm()">Add New Purchase</button>
    </div>

    <div id="add-incoming-form" class="form-container hidden">
      <h2>Add Incoming Item</h2>
      <form action="process_incoming.php" method="POST">
        <div class="form-group">
          <label for="item-name">Item Name:</label>
          <input type="text" id="item-name" name="item_name" required>
        </div>
        
        <div class="form-group">
          <label for="item-type">Item Type:</label>
          <select id="item-type" name="item_type" required>
            <option value="">Select Type</option>
            <option value="raw-material">Raw Material</option>
            <option value="finished-goods">Finished Goods</option>
            <option value="packaging">Packaging</option>
            <option value="other">Other</option>
          </select>
        </div>
        
        <div class="form-group">
          <label for="purchase-date">Purchase Date:</label>
          <input type="date" id="purchase-date" name="purchase_date" required>
        </div>
        
        <div class="form-group">
          <label for="expiry-date">Expiry Date (if applicable):</label>
          <input type="date" id="expiry-date" name="expiry_date">
        </div>
        
        <div class="form-group">
          <label for="quantity">Quantity:</label>
          <input type="number" id="quantity" name="quantity" min="1" required>
        </div>
        
        <div class="form-group">
          <label for="unit-price">Unit Price:</label>
          <input type="number" id="unit-price" name="unit_price" step="0.01" min="0" required>
        </div>
        
        <div class="form-group">
          <label for="supplier">Supplier:</label>
          <input type="text" id="supplier" name="supplier">
        </div>
        
        <div class="form-actions">
          <button type="submit">Save Item</button>
          <button type="button" onclick="hideAddIncomingForm()">Cancel</button>
        </div>
      </form>
    </div>

    <div class="table-container">
      <table id="incoming-table">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Item Type</th>
            <th>Purchase Date</th>
            <th>Expiry Date</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total</th>
            <th>Supplier</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="incoming-list">
          <?php
          require 'db_connect.php';
          $stmt = $conn->prepare("
              SELECT id, item_name, item_type, purchase_date, expiry_date, 
                     quantity, unit_price, (quantity * unit_price) as total, supplier
              FROM purchases
              ORDER BY purchase_date DESC
          ");
          $stmt->execute();
          $result = $stmt->get_result();
          
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                  <td>{$row['item_name']}</td>
                  <td>{$row['item_type']}</td>
                  <td>{$row['purchase_date']}</td>
                  <td>{$row['expiry_date']}</td>
                  <td>{$row['quantity']}</td>
                  <td>{$row['unit_price']}</td>
                  <td>{$row['total']}</td>
                  <td>{$row['supplier']}</td>
                  <td>
                      <button onclick='editIncoming({$row['id']})'>Edit</button>
                      <button onclick='deleteIncoming({$row['id']})'>Delete</button>
                  </td>
              </tr>";
          }
          $stmt->close();
          $conn->close();
          ?>
        </tbody>
      </table>
    </div>
  </main>
</section>

<script src="js/app.js"></script>
<script>
function showAddIncomingForm() {
    document.getElementById('add-incoming-form').classList.remove('hidden');
    document.getElementById('purchase-date').valueAsDate = new Date();
}

function hideAddIncomingForm() {
    document.getElementById('add-incoming-form').classList.add('hidden');
}

function editIncoming(id) {
    // Implement edit functionality
    alert('Edit incoming item with ID: ' + id);
}

function deleteIncoming(id) {
    if (confirm('Are you sure you want to delete this incoming item?')) {
        window.location.href = `delete_incoming.php?id=${id}`;
    }
}
</script>
</body>
</html>