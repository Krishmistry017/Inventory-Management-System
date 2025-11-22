<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

// Database connection
require 'db_connect.php';

// Default report type
$reportType = isset($_GET['report_type']) ? $_GET['report_type'] : 'inventory-summary';
$dateRange = isset($_GET['date_range']) ? $_GET['date_range'] : 'this-month';

// Calculate dates based on range
$startDate = '';
$endDate = date('Y-m-d');

switch ($dateRange) {
    case 'today':
        $startDate = $endDate = date('Y-m-d');
        break;
    case 'this-week':
        $startDate = date('Y-m-d', strtotime('monday this week'));
        break;
    case 'this-month':
        $startDate = date('Y-m-01');
        break;
    case 'last-month':
        $startDate = date('Y-m-01', strtotime('first day of last month'));
        $endDate = date('Y-m-t', strtotime('last month'));
        break;
    case 'this-year':
        $startDate = date('Y-01-01');
        break;
    case 'custom':
        $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        break;
    default:
        $startDate = date('Y-m-01');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory Reports</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .report-table th, .report-table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    .report-table th {
      background-color: #f2f2f2;
    }
    .report-table tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    .report-table tr:hover {
      background-color: #f1f1f1;
    }
    .total-row {
      font-weight: bold;
      background-color: #e6f7ff !important;
    }
    .critical {
      color: red;
      font-weight: bold;
    }
    .warning {
      color: orange;
      font-weight: bold;
    }
    .good {
      color: green;
    }
    .chart-container {
      width: 100%;
      height: 400px;
      margin: 20px 0;
    }
    .report-filters {
      background: #f5f5f5;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 10px;
    }
    .hidden {
      display: none;
    }
  </style>
</head>
<body>

<section id="reports-page" class="page">
  <div class="sidebar">
    <h2>Inventory App</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="items.php">Items</a>
    <a href="incoming.php">Incoming Items</a>
    <a href="outgoing.php">Outgoing Items</a>
    <a href="reports.php" class="active">Reports</a>
    <a href="logout.php">Logout</a>
  </div>

  <main class="main-content">
    <h1>Inventory Reports</h1>
    
    <form method="GET" action="reports.php" class="report-filters">
      <div class="form-group">
        <label for="report-type">Report Type:</label>
        <select id="report-type" name="report_type" onchange="changeReportType()">
          <option value="inventory-summary" <?= $reportType == 'inventory-summary' ? 'selected' : '' ?>>Inventory Summary</option>
          <option value="stock-movement" <?= $reportType == 'stock-movement' ? 'selected' : '' ?>>Stock Movement</option>
          <option value="expiry-analysis" <?= $reportType == 'expiry-analysis' ? 'selected' : '' ?>>Expiry Analysis</option>
          <option value="valuation" <?= $reportType == 'valuation' ? 'selected' : '' ?>>Inventory Valuation</option>
          <option value="low-stock" <?= $reportType == 'low-stock' ? 'selected' : '' ?>>Low Stock Alert</option>
          <option value="item-history" <?= $reportType == 'item-history' ? 'selected' : '' ?>>Item History</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="date-range">Date Range:</label>
        <select id="date-range" name="date_range" onchange="toggleCustomDateRange()">
          <option value="today" <?= $dateRange == 'today' ? 'selected' : '' ?>>Today</option>
          <option value="this-week" <?= $dateRange == 'this-week' ? 'selected' : '' ?>>This Week</option>
          <option value="this-month" <?= $dateRange == 'this-month' ? 'selected' : '' ?>>This Month</option>
          <option value="last-month" <?= $dateRange == 'last-month' ? 'selected' : '' ?>>Last Month</option>
          <option value="this-year" <?= $dateRange == 'this-year' ? 'selected' : '' ?>>This Year</option>
          <option value="custom" <?= $dateRange == 'custom' ? 'selected' : '' ?>>Custom Range</option>
        </select>
      </div>
      
      <div id="custom-date-range" class="<?= $dateRange == 'custom' ? '' : 'hidden' ?>">
        <div class="form-group">
          <label for="start-date">Start Date:</label>
          <input type="date" id="start-date" name="start_date" value="<?= $startDate ?>">
        </div>
        <div class="form-group">
          <label for="end-date">End Date:</label>
          <input type="date" id="end-date" name="end_date" value="<?= $endDate ?>">
        </div>
      </div>
      
      <div id="item-selector" class="form-group <?= $reportType == 'item-history' ? '' : 'hidden' ?>">
        <label for="item-id">Select Item:</label>
        <select id="item-id" name="item_id">
          <option value="">All Items</option>
          <?php
          $items = $conn->query("SELECT id, name FROM items ORDER BY name");
          while ($item = $items->fetch_assoc()) {
              $selected = (isset($_GET['item_id']) && $_GET['item_id'] == $item['id']) ? 'selected' : '';
              echo "<option value='{$item['id']}' $selected>{$item['name']}</option>";
          }
          ?>
        </select>
      </div>
      
      <button type="submit">Generate Report</button>
      <button type="button" onclick="exportReport()">Export to PDF</button>
      <button type="button" onclick="printReport()">Print Report</button>
    </form>
    
    <div id="report-container">
      <?php
      switch ($reportType) {
          case 'inventory-summary':
              include 'reports/inventory_summary.php';
              break;
          case 'stock-movement':
              include 'reports/stock_movement.php';
              break;
          case 'expiry-analysis':
              include 'reports/expiry_analysis.php';
              break;
          case 'valuation':
              include 'reports/inventory_valuation.php';
              break;
          case 'low-stock':
              include 'reports/low_stock.php';
              break;
          case 'item-history':
              include 'reports/item_history.php';
              break;
          default:
              echo '<p>Select report type to generate a report.</p>';
      }
      ?>
    </div>
  </main>
</section>

<script src="js/app.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function changeReportType() {
    const reportType = document.getElementById('report-type').value;
    const itemSelector = document.getElementById('item-selector');
    
    if (reportType === 'item-history') {
        itemSelector.classList.remove('hidden');
    } else {
        itemSelector.classList.add('hidden');
    }
}

function toggleCustomDateRange() {
    const dateRange = document.getElementById('date-range').value;
    const customRangeDiv = document.getElementById('custom-date-range');
    
    if (dateRange === 'custom') {
        customRangeDiv.classList.remove('hidden');
    } else {
        customRangeDiv.classList.add('hidden');
    }
}

function exportReport() {
    const reportType = document.getElementById('report-type').value;
    const dateRange = document.getElementById('date-range').value;
    let url = `export_report.php?report_type=${reportType}&date_range=${dateRange}`;
    
    if (dateRange === 'custom') {
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        url += `&start_date=${startDate}&end_date=${endDate}`;
    }
    
    if (reportType === 'item-history') {
        const itemId = document.getElementById('item-id').value;
        if (itemId) url += `&item_id=${itemId}`;
    }
    
    window.open(url, '_blank');
}

function printReport() {
    window.print();
}
</script>
</body>
</html>