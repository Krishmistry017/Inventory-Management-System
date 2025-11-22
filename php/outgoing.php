<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// Display messages
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Outgoing Items - Inventory App</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .tabs {
      display: flex;
      margin-bottom: 20px;
      border-bottom: 1px solid #ddd;
    }
    .tab-btn {
      padding: 10px 20px;
      background: #f1f1f1;
      border: none;
      cursor: pointer;
      margin-right: 5px;
    }
    .tab-btn.active {
      background: #4CAF50;
      color: white;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    .alert {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
    }
    .alert.success {
      background-color: #dff0d8;
      color: #3c763d;
    }
    .alert.error {
      background-color: #f2dede;
      color: #a94442;
    }
  </style>
</head>
<body>

<!-- OUTGOING ITEMS PAGE -->
<section id="outgoing-page" class="page">
  <div class="sidebar">
    <h2>Inventory App</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="items.php">Items</a>
    <a href="incoming.php">Incoming Items</a>
    <a href="outgoing.php" class="active">Outgoing Items</a>
    <a href="reports.php">Reports</a>
    <a href="logout.php">Logout</a>
  </div>

  <main class="main-content">
    <h1>Outgoing Items</h1>
    
    <?php if ($success === 'sale_recorded'): ?>
    <div class="alert success">
        Sale successfully recorded!
    </div>
    <?php elseif ($success === 'wastage_recorded'): ?>
    <div class="alert success">
        Wastage successfully recorded!
    </div>
    <?php elseif ($error === 'insufficient_stock'): ?>
    <div class="alert error">
        Error: Insufficient stock for this transaction!
    </div>
    <?php endif; ?>
    
    <div class="tabs">
      <button class="tab-btn active" onclick="switchTab('sales')">Sales</button>
      <button class="tab-btn" onclick="switchTab('wastage')">Wastage</button>
    </div>
    
    <!-- SALES TAB -->
    <div id="sales-tab" class="tab-content active">
      <div class="action-bar">
        <button onclick="showAddSaleForm()">Record New Sale</button>
      </div>
      
      <div id="add-sale-form" class="form-container hidden">
        <h2>Record Item Sale</h2>
        <form action="process_sale.php" method="POST">
          <div class="form-group">
            <label for="sale-item">Select Item:</label>
            <select id="sale-item" name="item_id" required onchange="updateAvailableStock()">
              <option value="">Select Item</option>
              <?php
              require 'db_connect.php';
              $stmt = $conn->prepare("SELECT id, name, stock FROM items WHERE stock > 0 ORDER BY name");
              $stmt->execute();
              $result = $stmt->get_result();
              while ($row = $result->fetch_assoc()) {
                  echo "<option value='{$row['id']}' data-stock='{$row['stock']}'>{$row['name']}</option>";
              }
              $stmt->close();
              $conn->close();
              ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="available-stock">Available Stock:</label>
            <input type="text" id="available-stock" readonly>
          </div>
          
          <div class="form-group">
            <label for="sale-date">Sale Date:</label>
            <input type="date" id="sale-date" name="sale_date" required value="<?= date('Y-m-d') ?>">
          </div>
          
          <div class="form-group">
            <label for="sale-quantity">Quantity:</label>
            <input type="number" id="sale-quantity" name="quantity" min="1" required>
          </div>
          
          <div class="form-group">
            <label for="sale-price">Unit Price:</label>
            <input type="number" id="sale-price" name="unit_price" step="0.01" min="0" required>
          </div>
          
          <div class="form-group">
            <label for="customer">Customer:</label>
            <input type="text" id="customer" name="customer">
          </div>
          
          <div class="form-actions">
            <button type="submit">Save Sale</button>
            <button type="button" onclick="hideAddSaleForm()">Cancel</button>
          </div>
        </form>
      </div>

      <div class="table-container">
        <table id="sales-table">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Sale Date</th>
              <th>Quantity</th>
              <th>Unit Price</th>
              <th>Total</th>
              <th>Customer</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="sales-list">
            <?php
            require 'db_connect.php';
            $stmt = $conn->prepare("
                SELECT s.id, i.name, s.sale_date, s.quantity, s.unit_price, s.customer 
                FROM sales s
                JOIN items i ON s.item_id = i.id
                ORDER BY s.sale_date DESC
                LIMIT 50
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $total = $row['quantity'] * $row['unit_price'];
                echo "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['sale_date']}</td>
                    <td>{$row['quantity']}</td>
                    <td>" . number_format($row['unit_price'], 2) . "</td>
                    <td>" . number_format($total, 2) . "</td>
                    <td>{$row['customer']}</td>
                    <td>
                        <button onclick='editSale({$row['id']})'>Edit</button>
                        <button onclick='confirmDeleteSale({$row['id']})'>Delete</button>
                    </td>
                </tr>";
            }
            $stmt->close();
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- WASTAGE TAB -->
    <div id="wastage-tab" class="tab-content">
      <div class="action-bar">
        <button onclick="showAddWastageForm()">Record Wastage</button>
      </div>
      
      <div id="add-wastage-form" class="form-container hidden">
        <h2>Record Wastage</h2>
        <form action="process_wastage.php" method="POST">
          <div class="form-group">
            <label for="wastage-item">Select Item:</label>
            <select id="wastage-item" name="item_id" required onchange="updateWastageAvailableStock()">
              <option value="">Select Item</option>
              <?php
              require 'db_connect.php';
              $stmt = $conn->prepare("SELECT id, name, stock FROM items WHERE stock > 0 ORDER BY name");
              $stmt->execute();
              $result = $stmt->get_result();
              while ($row = $result->fetch_assoc()) {
                  echo "<option value='{$row['id']}' data-stock='{$row['stock']}'>{$row['name']}</option>";
              }
              $stmt->close();
              $conn->close();
              ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="wastage-available-stock">Available Stock:</label>
            <input type="text" id="wastage-available-stock" readonly>
          </div>
          
          <div class="form-group">
            <label for="wastage-date">Date:</label>
            <input type="date" id="wastage-date" name="wastage_date" required value="<?= date('Y-m-d') ?>">
          </div>
          
          <div class="form-group">
            <label for="wastage-quantity">Quantity:</label>
            <input type="number" id="wastage-quantity" name="quantity" min="1" required>
          </div>
          
          <div class="form-group">
            <label for="wastage-reason">Reason:</label>
            <select id="wastage-reason" name="reason" required>
              <option value="">Select Reason</option>
              <option value="expired">Expired</option>
              <option value="damaged">Damaged</option>
              <option value="quality-issue">Quality Issue</option>
              <option value="other">Other</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="wastage-notes">Notes:</label>
            <textarea id="wastage-notes" name="notes"></textarea>
          </div>
          
          <div class="form-actions">
            <button type="submit">Save Wastage</button>
            <button type="button" onclick="hideAddWastageForm()">Cancel</button>
          </div>
        </form>
      </div>

      <div class="table-container">
        <table id="wastage-table">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Date</th>
              <th>Quantity</th>
              <th>Reason</th>
              <th>Cost Impact</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="wastage-list">
            <?php
            require 'db_connect.php';
            $stmt = $conn->prepare("
                SELECT w.id, i.name, w.wastage_date, w.quantity, w.reason, i.unit_price, w.notes 
                FROM wastage w
                JOIN items i ON w.item_id = i.id
                ORDER BY w.wastage_date DESC
                LIMIT 50
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $costImpact = $row['quantity'] * $row['unit_price'];
                echo "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['wastage_date']}</td>
                    <td>{$row['quantity']}</td>
                    <td>{$row['reason']}</td>
                    <td>" . number_format($costImpact, 2) . "</td>
                    <td>
                        <button onclick='editWastage({$row['id']})'>Edit</button>
                        <button onclick='confirmDeleteWastage({$row['id']})'>Delete</button>
                    </td>
                </tr>";
            }
            $stmt->close();
            $conn->close();
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</section>

<script src="js/app.js"></script>
<script>
// Tab switching
function switchTab(tabName) {
    // Update active tab button
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`.tab-btn[onclick="switchTab('${tabName}')"]`).classList.add('active');
    
    // Update active tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`${tabName}-tab`).classList.add('active');
}

// Sale form functions
function showAddSaleForm() {
    document.getElementById('add-sale-form').classList.remove('hidden');
    document.getElementById('sale-date').valueAsDate = new Date();
    updateAvailableStock();
}

function hideAddSaleForm() {
    document.getElementById('add-sale-form').classList.add('hidden');
}

function updateAvailableStock() {
    const itemSelect = document.getElementById('sale-item');
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');
    document.getElementById('available-stock').value = stock || '0';
}

// Wastage form functions
function showAddWastageForm() {
    document.getElementById('add-wastage-form').classList.remove('hidden');
    document.getElementById('wastage-date').valueAsDate = new Date();
    updateWastageAvailableStock();
}

function hideAddWastageForm() {
    document.getElementById('add-wastage-form').classList.add('hidden');
}

function updateWastageAvailableStock() {
    const itemSelect = document.getElementById('wastage-item');
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    const stock = selectedOption.getAttribute('data-stock');
    document.getElementById('wastage-available-stock').value = stock || '0';
}

// Delete confirmation functions
function confirmDeleteSale(id) {
    if (confirm('Are you sure you want to delete this sale record? This action cannot be undone.')) {
        window.location.href = `delete_sale.php?id=${id}`;
    }
}

function confirmDeleteWastage(id) {
    if (confirm('Are you sure you want to delete this wastage record? This action cannot be undone.')) {
        window.location.href = `delete_wastage.php?id=${id}`;
    }
}

// Placeholder edit functions
function editSale(id) {
    alert('Edit functionality for sale ID ' + id + ' will be implemented here');
    // You would typically fetch the sale data and populate the form for editing
}

function editWastage(id) {
    alert('Edit functionality for wastage ID ' + id + ' will be implemented here');
    // You would typically fetch the wastage data and populate the form for editing
}
</script>
</body>
</html>