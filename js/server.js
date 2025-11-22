const express = require('express');
const mysql = require('mysql');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
app.use(cors());
app.use(bodyParser.json());

const db = mysql.createConnection({
    host: 'localhost',
    user: 'your_user',
    password: 'your_password',
    database: 'inventory_db'
});

db.connect(err => {
    if (err) throw err;
    console.log('MySQL Connected');
});

// Example route to get all items
app.get('/items', (req, res) => {
    db.query('SELECT * FROM items', (err, results) => {
        if (err) throw err;
        res.json(results);
    });
});

// Example route to add item
app.post('/items', (req, res) => {
    const { name, type, stock, expiry_date } = req.body;
    db.query('INSERT INTO items (name, type, stock, expiry_date) VALUES (?, ?, ?, ?)',
        [name, type, stock, expiry_date],
        (err, result) => {
            if (err) throw err;
            res.json({ id: result.insertId });
        });
});

app.listen(3000, () => console.log('Server started on port 3000'));
function login(event) {
    event.preventDefault();
    // Simple login (no real authentication)
    const username = document.getElementById('username').value;
    if(username) {
      showPage('dashboard');
    }
  }
  
  function logout() {
    showPage('login');
  }
  
  function showPage(page) {
    document.getElementById('login-page').classList.add('hidden');
    document.getElementById('dashboard-page').classList.add('hidden');
    document.getElementById('items-page').classList.add('hidden');
  
    if(page === 'login') {
      document.getElementById('login-page').classList.remove('hidden');
    } else if(page === 'dashboard') {
      document.getElementById('dashboard-page').classList.remove('hidden');
    } else if(page === 'items') {
      document.getElementById('items-page').classList.remove('hidden');
    }
  }
  const itemsData = [];

function saveIncomingItem(event) {
  event.preventDefault();

  const item = {
    name: document.getElementById('item-name').value,
    type: document.getElementById('item-type').value,
    purchaseDate: document.getElementById('purchase-date').value,
    expiryDate: document.getElementById('expiry-date').value,
    quantity: parseInt(document.getElementById('quantity').value),
    unitPrice: parseFloat(document.getElementById('unit-price').value),
    supplier: document.getElementById('supplier').value,
    stock: parseInt(document.getElementById('quantity').value),
  };

  itemsData.push(item);
  updateAllViews();

  document.getElementById('add-incoming-form').reset();
  hideAddIncomingForm();
}
function updateAllViews() {
  updateItemsTable();
  updateOutgoingForms();
  updateReportData();
}
function updateItemsTable() {
  const tableBody = document.getElementById('items-list');
  tableBody.innerHTML = itemsData.map(item => `
    <tr>
      <td>${item.name}</td>
      <td>${item.type}</td>
      <td>${item.stock}</td>
      <td>${item.expiryDate || 'N/A'}</td>
      <td><span class="status ${item.stock < 10 ? 'low-stock' : 'in-stock'}">${item.stock < 10 ? 'Low Stock' : 'In Stock'}</span></td>
      <td><button class="action-btn edit">Edit</button><button class="action-btn delete">Delete</button></td>
    </tr>
  `).join('');
}
function updateOutgoingForms() {
  const saleDropdown = document.getElementById('sale-item');
  const wastageDropdown = document.getElementById('wastage-item');

  saleDropdown.innerHTML = '<option value="">Select Item</option>' + itemsData.map((item, index) => `<option value="${index}">${item.name}</option>`).join('');
  wastageDropdown.innerHTML = saleDropdown.innerHTML;
}
function updateReportData() {
  const reportType = document.getElementById('report-type').value;
  const reportContainer = document.getElementById('report-container');

  if (reportType === 'inventory') {
    const rows = itemsData.map(item => `
      <tr>
        <td>${item.name}</td>
        <td>${item.type}</td>
        <td>${item.stock}</td>
        <td>${(item.unitPrice * item.stock).toFixed(2)}</td>
        <td>${item.expiryDate || 'N/A'}</td>
        <td><span class="status ${item.stock < 10 ? 'low-stock' : 'in-stock'}">${item.stock < 10 ? 'Low Stock' : 'In Stock'}</span></td>
      </tr>`).join('');

    const totalValue = itemsData.reduce((sum, item) => sum + (item.unitPrice * item.stock), 0);

    reportContainer.innerHTML = `
      <h2>Current Inventory Report</h2>
      <table class="report-table">
        <thead>
          <tr>
            <th>Item Name</th><th>Item Type</th><th>Current Stock</th><th>Stock Value</th><th>Expiry Date</th><th>Status</th>
          </tr>
        </thead>
        <tbody>${rows}</tbody>
        <tfoot>
          <tr>
            <td colspan="3"><strong>Total Inventory Value</strong></td>
            <td><strong>${totalValue.toFixed(2)}</strong></td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>`;
  }
}