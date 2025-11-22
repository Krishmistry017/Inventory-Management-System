<?php
$query = "SELECT 
            i.name,
            SUM(CASE WHEN p.purchase_date BETWEEN ? AND ? THEN p.quantity ELSE 0 END) as incoming,
            SUM(CASE WHEN s.sale_date BETWEEN ? AND ? THEN s.quantity ELSE 0 END) as outgoing_sales,
            SUM(CASE WHEN w.wastage_date BETWEEN ? AND ? THEN w.quantity ELSE 0 END) as outgoing_wastage,
            (SUM(CASE WHEN p.purchase_date BETWEEN ? AND ? THEN p.quantity ELSE 0 END) - 
             (SUM(CASE WHEN s.sale_date BETWEEN ? AND ? THEN s.quantity ELSE 0 END) + 
              SUM(CASE WHEN w.wastage_date BETWEEN ? AND ? THEN w.quantity ELSE 0 END))) as net_movement
          FROM items i
          LEFT JOIN purchases p ON i.name = p.item_name
          LEFT JOIN sales s ON i.id = s.item_id
          LEFT JOIN wastage w ON i.id = w.item_id
          GROUP BY i.id, i.name
          ORDER BY net_movement DESC";

$stmt = $conn->prepare($query);
$params = array_fill(0, 12, $startDate);
$params = array_merge($params, array_fill(0, 12, $endDate));
$stmt->bind_param(str_repeat('s', 24), ...array_merge($params, $params));
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Stock Movement Report</h2>
<p>Date Range: <?= date('M j, Y', strtotime($startDate)) ?> to <?= date('M j, Y', strtotime($endDate)) ?></p>

<table class="report-table">
  <thead>
    <tr>
      <th>Item Name</th>
      <th>Incoming</th>
      <th>Outgoing (Sales)</th>
      <th>Outgoing (Wastage)</th>
      <th>Net Movement</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= $row['incoming'] ?></td>
      <td><?= $row['outgoing_sales'] ?></td>
      <td><?= $row['outgoing_wastage'] ?></td>
      <td><?= $row['net_movement'] ?></td>
      <td class="<?= $row['net_movement'] > 0 ? 'good' : 'critical' ?>">
        <?= $row['net_movement'] > 0 ? 'Positive' : 'Negative' ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>