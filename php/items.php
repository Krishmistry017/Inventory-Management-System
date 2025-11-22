<?php
require_once 'config.php';
requireLogin();

session_start();
if (!isset($_SESSION['loggedin'])) {
    header("Location: index.html");
    exit;
}
// Rest of your page content

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item']) && verifyCsrfToken($_POST['csrf_token'])) {
        // Add new item
        try {
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO items (name, type, stock, price, cost, expiry_date, min_stock_level)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['type'],
                $_POST['stock'],
                $_POST['price'],
                $_POST['cost'],
                !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
                $_POST['min_stock_level']
            ]);
            
            $_SESSION['success'] = "Item added successfully";
            header("Location: items.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error adding item: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['update_item']) && verifyCsrfToken($_POST['csrf_token'])) {
        // Update existing item
        try {
            $db = getDB();
            $stmt = $db->prepare("
                UPDATE items 
                SET name = ?, type = ?, stock = ?, price = ?, cost = ?, expiry_date = ?, min_stock_level = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['type'],
                $_POST['stock'],
                $_POST['price'],
                $_POST['cost'],
                !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
                $_POST['min_stock_level'],
                $_POST['id']
            ]);
            
            $_SESSION['success'] = "Item updated successfully";
            header("Location: items.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error updating item: " . $e->getMessage();
        }
    }
    elseif (isset($_GET['delete']) && verifyCsrfToken($_GET['csrf_token'])) {
        // Delete item
        try {
            $db = getDB();
            $stmt = $db->prepare("DELETE FROM items WHERE id = ?");
            $stmt->execute([$_GET['delete']]);
            
            $_SESSION['success'] = "Item deleted successfully";
            header("Location: items.php");
            exit();
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error deleting item: " . $e->getMessage();
        }
    }
}

// Get all items
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM items ORDER BY name");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Check for edit request
$editItem = null;
if (isset($_GET['edit'])) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$_GET['edit']]);
        $editItem = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error fetching item: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items | Inventory System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <section id="items-page" class="page">
        <div class="sidebar">
            <h2>Inventory App</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="items.php" class="active">Items</a>
            <a href="incoming.php">Incoming Items</a>
            <a href="outgoing.php">Outgoing Items</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Logout</a>
        </div>

        <main class="main-content">
            <h1>Manage Items</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <div class="action-bar">
                <button onclick="document.getElementById('item-form').classList.toggle('hidden')">
                    <?php echo $editItem ? 'Cancel Edit' : 'Add New Item'; ?>
                </button>
            </div>
            
            <div id="item-form" class="form-container <?php echo $editItem ? '' : 'hidden'; ?>">
                <h2><?php echo $editItem ? 'Edit Item' : 'Add New Item'; ?></h2>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <?php if ($editItem): ?>
                        <input type="hidden" name="id" value="<?php echo $editItem['id']; ?>">
                        <input type="hidden" name="update_item" value="1">
                    <?php else: ?>
                        <input type="hidden" name="add_item" value="1">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Item Name:</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo $editItem ? htmlspecialchars($editItem['name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="type">Item Type:</label>
                        <input type="text" id="type" name="type" required
                               value="<?php echo $editItem ? htmlspecialchars($editItem['type']) : ''; ?>">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock">Current Stock:</label>
                            <input type="number" id="stock" name="stock" required min="0"
                                   value="<?php echo $editItem ? $editItem['stock'] : '0'; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="min_stock_level">Min Stock Level:</label>
                            <input type="number" id="min_stock_level" name="min_stock_level" required min="0"
                                   value="<?php echo $editItem ? $editItem['min_stock_level'] : '5'; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Selling Price:</label>
                            <input type="number" id="price" name="price" required min="0" step="0.01"
                                   value="<?php echo $editItem ? $editItem['price'] : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cost">Cost Price:</label>
                            <input type="number" id="cost" name="cost" min="0" step="0.01"
                                   value="<?php echo $editItem ? $editItem['cost'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="expiry_date">Expiry Date (if applicable):</label>
                        <input type="date" id="expiry_date" name="expiry_date"
                               value="<?php echo $editItem ? $editItem['expiry_date'] : ''; ?>">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <?php echo $editItem ? 'Update Item' : 'Add Item'; ?>
                        </button>
                        <button type="button" onclick="document.getElementById('item-form').classList.add('hidden')">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr class="<?php echo $item['stock'] <= $item['min_stock_level'] ? 'warning' : ''; ?>">
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['type']); ?></td>
                                <td><?php echo $item['stock']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['expiry_date'] ?: 'N/A'; ?></td>
                                <td>
                                    <?php if ($item['stock'] <= 0): ?>
                                        <span class="badge danger">Out of Stock</span>
                                    <?php elseif ($item['stock'] <= $item['min_stock_level']): ?>
                                        <span class="badge warning">Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge success">In Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="items.php?edit=<?php echo $item['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="items.php?delete=<?php echo $item['id']; ?>&csrf_token=<?php echo generateCsrfToken(); ?>" 
                                       class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </section>
</body>
</html>