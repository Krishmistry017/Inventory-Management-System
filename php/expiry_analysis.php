<?php
$query = "SELECT 
            name, 
            type, 
            stock, 
            expiry_date, 
            DATEDIFF(expiry_date, CURDATE()) as days_until_expiry,
            (stock * unit_price) as total_value
          FROM items
          WHERE expiry_date IS NOT NULL
          ORDER BY days_until_expiry";

$result = $conn->query($query);
?>

<h2>Expiry Analysis Report</h2>
<p>Generated on: <?= date('F j, Y') ?></p>

<table class="report-table">
  <thead>
    <tr>
      <th>Item Name</th>
      <th>Type</th>
      <th>Current Stock</th>
      <th>Expiry Date</th>
      <th>Days Until Expiry</th>
      <th>Total Value</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $expiringSoonValue = 0;
    $expiredValue = 0;
    
    while ($row = $result->fetch_assoc()):
        $status = '';
        $statusClass = '';
        
        if ($row['days_until_expiry'] <= 0) {
            $status = 'Expired';
            $statusClass = 'critical';
            $expiredValue += $row['total_value'];
        } elseif ($row['days_until_expiry'] <= 30) {
            $status = 'Expiring Soon';
            $statusClass = 'warning';
            $expiringSoonValue += $row['total_value'];
        } else {
            $status = 'Good';
            $statusClass = 'good';
        }
    ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['type']) ?></td>
      <td><?= $row['stock'] ?></td>
      <td><?= date('M j, Y', strtotime($row['expiry_date'])) ?></td>
      <td><?= $row['days_until_expiry'] ?></td>
      <td><?= number_format($row['total_value'], 2) ?></td>
      <td class="<?= $statusClass ?>"><?= $status ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<div class="summary">
  <h3>Summary</h3>
  <p>Total Value of Expired Items: <span class="critical"><?= number_format($expiredValue, 2) ?></span></p>
  <p>Total Value of Items Expiring Soon (within 30 days): <span class="warning"><?= number_format($expiringSoonValue, 2) ?></span></p>
</div>