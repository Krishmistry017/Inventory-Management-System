<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.html');
    exit;
}

require 'db_connect.php';

// Get report parameters
$reportType = $_GET['report_type'] ?? 'inventory-summary';
$dateRange = $_GET['date_range'] ?? 'this-month';
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$itemId = $_GET['item_id'] ?? null;

// Generate HTML content based on report type
ob_start();
include 'reports/header.php';

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
}

include 'reports/footer.php';
$html = ob_get_clean();

// Generate PDF

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Output the generated PDF
$dompdf->stream("inventory_report_{$reportType}.pdf", [
    "Attachment" => true
]);