<?php
$query = "SELECT 
            i.id, 
            i.name, 
            i.type, 
            i.stock, 
            i.unit_price, 
            (i.stock * i.unit_price) as total_value,
            i.expiry_date,
            DATEDIFF(i.expiry_date, CURDATE()) as days_until_expiry,
            (SELECT SUM(quantity) FROM purchases WHERE item_name = i.name) as total_purchased,
            (SELECT SUM(quantity) FROM sales WHERE item_id = i.id) as total_sold,
            (SELECT SUM(quantity) FROM wastage WHERE item_id = i.id) as total_wastage
          FROM items i
          ORDER BY i.name";

$result = $conn->query($query);
?>

<h2>Inventory Summary Report</h2>
<p>Generated on: <?= date('F j, Y') ?></p>

<div class="chart-container">
  <canvas id="inventoryChart"></canvas>
</div>

<table class="report-table">
  <thead>
    <tr>
      <th>Item Name</th>
      <th>Type</th>
      <th>Current Stock</th>
      <th>Unit Price</th>
      <th>Total Value</th>
      <th>Expiry Date</th>
      <th>Status</th>
      <th>Total Purchased</th>
      <th>Total Sold</th>
      <th>Total Wastage</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $grandTotal = 0;
    $chartLabels = [];
    $chartData = [];
    
    while ($row = $result->fetch_assoc()):
        $status = '';
        $statusClass = '';
        
        if ($row['expiry_date']) {
            $days = $row['days_until_expiry'];
            if ($days <= 0) {
                $status = 'Expired';
                $statusClass = 'critical';
            } elseif ($days <= 30) {
                $status = 'Expiring Soon';
                $statusClass = 'warning';
            } else {
                $status = 'Good';
                $statusClass = 'good';
            }
        }
        
        $grandTotal += $row['total_value'];
        $chartLabels[] = $row['name'];
        $chartData[] = $row['stock'];
    ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['type']) ?></td>
      <td><?= $row['stock'] ?></td>
      <td><?= number_format($row['unit_price'], 2) ?></td>
      <td><?= number_format($row['total_value'], 2) ?></td>
      <td><?= $row['expiry_date'] ? date('M j, Y', strtotime($row['expiry_date'])) : 'N/A' ?></td>
      <td class="<?= $statusClass ?>"><?= $status ?></td>
      <td><?= $row['total_purchased'] ?? 0 ?></td>
      <td><?= $row['total_sold'] ?? 0 ?></td>
      <td><?= $row['total_wastage'] ?? 0 ?></td>
    </tr>
    <?php endwhile; ?>
    <tr class="total-row">
      <td colspan="4">Grand Total Value</td>
      <td colspan="6"><?= number_format($grandTotal, 2) ?></td>
    </tr>
  </tbody>
</table>

<script>
const ctx = document.getElementById('inventoryChart').getContext('2d');
const inventoryChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Current Stock Levels',
            data: <?= json_encode($chartData) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Quantity'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Items'
                }
            }
        }
    }
});
</script>