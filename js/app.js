  function addNewItem() {
  document.getElementById('add-item-form').classList.remove('hidden');
}
function saveNewItem(event) {
  event.preventDefault();

  const name = document.getElementById('new-item-name').value;
  const type = document.getElementById('new-item-type').value;
  const stock = parseInt(document.getElementById('new-item-stock').value);
  const unitPrice = parseFloat(document.getElementById('new-item-price').value);
  const expiryDate = document.getElementById('new-item-expiry').value;

  const newItem = { name, type, stock, unitPrice, expiryDate };

  // Add to global itemsData if using it, otherwise store locally
  if (!window.itemsData) window.itemsData = [];
  window.itemsData.push(newItem);

  // Update UI
  updateAllViews();

  // Reset and hide form
  document.getElementById('add-item-form').reset();
  document.getElementById('add-item-form').classList.add('hidden');
}

function updateItemDropdowns() {
  const saleItemSelect = document.getElementById('sale-item');
  const wastageItemSelect = document.getElementById('wastage-item');
  const incomingItemSelect = document.getElementById('incoming-item');

  [saleItemSelect, wastageItemSelect, incomingItemSelect].forEach(select => {
    if (!select) return;
    select.innerHTML = '<option value="">Select Item</option>';
    itemsData.forEach(item => {
      const option = document.createElement('option');
      option.value = item.id;
      option.textContent = item.name;
      select.appendChild(option);
    });
  });
}
// 
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
function updateOutgoingForms() {
  const saleDropdown = document.getElementById('sale-item');
  const wastageDropdown = document.getElementById('wastage-item');

  saleDropdown.innerHTML = '<option value="">Select Item</option>' + itemsData.map((item, index) => `<option value="${index}">${item.name}</option>`).join('');
  wastageDropdown.innerHTML = saleDropdown.innerHTML;
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

// Sample JavaScript functions for the inventory system
function login(event) {
  event.preventDefault();
  // Implement login functionality
  const username = document.getElementById('username').value;
  const password = document.getElementById('password').value;
  
  // For demo purposes, just show dashboard
  document.getElementById('login-page').classList.add('hidden');
  document.getElementById('dashboard-page').classList.remove('hidden');
}

function logout() {
  // Implement logout functionality
  document.getElementById('dashboard-page').classList.add('hidden');
  document.getElementById('items-page').classList.add('hidden');
  document.getElementById('incoming-page').classList.add('hidden');
  document.getElementById('outgoing-page').classList.add('hidden');
  document.getElementById('reports-page').classList.add('hidden');
  document.getElementById('login-page').classList.remove('hidden');
}

function showPage(page) {
  // Hide all pages
  document.getElementById('dashboard-page').classList.add('hidden');
  document.getElementById('items-page').classList.add('hidden');
  document.getElementById('incoming-page').classList.add('hidden');
  document.getElementById('outgoing-page').classList.add('hidden');
  document.getElementById('reports-page').classList.add('hidden');
  
  // Show selected page
  if (page === 'dashboard') {
    document.getElementById('dashboard-page').classList.remove('hidden');
  } else if (page === 'items') {
    document.getElementById('items-page').classList.remove('hidden');
    loadItems();
  } else if (page === 'incoming') {
    document.getElementById('incoming-page').classList.remove('hidden');
    loadIncomingItems();
  } else if (page === 'outgoing') {
    document.getElementById('outgoing-page').classList.remove('hidden');
    loadOutgoingItems();
  } else if (page === 'reports') {
    document.getElementById('reports-page').classList.remove('hidden');
  }
}

// Items Management
function addNewItem() {
  // Implement add new item functionality
  alert('Add new item form would appear here');
}

function loadItems() {
  // Implement loading items from database
  const itemsList = document.getElementById('items-list');
  
  // For demo purposes, show some sample data
  itemsList.innerHTML = `
    <tr>
      <td>Laptop</td>
      <td>Electronics</td>
      <td>15</td>
      <td>N/A</td>
      <td><span class="status in-stock">In Stock</span></td>
      <td>
        <button class="action-btn edit">Edit</button>
        <button class="action-btn delete">Delete</button>
      </td>
    </tr>
    <tr>
      <td>Milk</td>
      <td>Grocery</td>
      <td>5</td>
      <td>2025-05-10</td>
      <td><span class="status low-stock">Low Stock</span></td>
      <td>
        <button class="action-btn edit">Edit</button>
        <button class="action-btn delete">Delete</button>
      </td>
    </tr>
  `;
}

// Incoming Items Management
function showAddIncomingForm() {
  document.getElementById('add-incoming-form').classList.remove('hidden');
}

function hideAddIncomingForm() {
  document.getElementById('add-incoming-form').classList.add('hidden');
}

function saveIncomingItem(event) {
  event.preventDefault();
  
  // Get form values
  const itemName = document.getElementById('item-name').value;
  const itemType = document.getElementById('item-type').value;
  const purchaseDate = document.getElementById('purchase-date').value;
  const expiryDate = document.getElementById('expiry-date').value;
  const quantity = document.getElementById('quantity').value;
  const unitPrice = document.getElementById('unit-price').value;
  const supplier = document.getElementById('supplier').value;
  
  // Save to database (to be implemented)
  console.log('Saving incoming item:', {
    itemName,
    itemType,
    purchaseDate,
    expiryDate,
    quantity,
    unitPrice,
    supplier
  });
  
  // Update UI
  const incomingList = document.getElementById('incoming-list');
  const newRow = document.createElement('tr');
 newRow.innerHTML = `
  <td>${itemName}</td>
  <td>${itemType}</td>
  <td>${purchaseDate}</td>
  <td>${expiryDate || 'N/A'}</td>
  <td>${quantity}</td>
  <td>${parseFloat(unitPrice).toFixed(2)}</td>
  <td>${(parseFloat(unitPrice) * parseInt(quantity)).toFixed(2)}</td>
  <td>
    <button class="action-btn edit">Edit</button>
    <button class="action-btn delete">Delete</button>
  </td>
`;

  incomingList.appendChild(newRow);
  
  // Reset form and hide it
  document.getElementById('add-incoming-form').reset();
  hideAddIncomingForm();
}

function loadIncomingItems() {
  // Implement loading incoming items from database
  const incomingList = document.getElementById('incoming-list');
  
  // For demo purposes, show some sample data
  incomingList.innerHTML = `
    <tr>
      <td>Laptop</td>
      <td>Electronics</td>
      <td>2025-04-15</td>
      <td>N/A</td>
      <td>10</td>
      <td>800.00</td>
      <td>8,000.00</td>
      <td>
        <button class="action-btn edit">Edit</button>
        <button class="action-btn delete">Delete</button>
      </td>
    </tr>
    <tr>
      <td>Milk</td>
      <td>Grocery</td>
      <td>2025-04-20</td>
      <td>2025-05-10</td>
      <td>20</td>
      <td>2.50</td>
      <td>50.00</td>
      <td>
        <button class="action-btn edit">Edit</button>
        <button class="action-btn delete">Delete</button>
      </td>
    </tr>
    <tr>
      <td>Panner</td>
      <td>Grocery</td>
      <td>2025-04-20</td>
      <td>2025-05-10</td>
      <td>20</td>
      <td>2.50</td>
      <td>50.00</td>
      <td>
        <button class="action-btn edit">Edit</button>
        <button class="action-btn delete">Delete</button>
      </td>
    </tr>
  `;
}

// Outgoing Items Management
function switchTab(tab) {
  // Hide all tab contents
  document.getElementById('sales-tab').classList.add('hidden');
  document.getElementById('wastage-tab').classList.add('hidden');
  
  // Show selected tab
  document.getElementById(`${tab}-tab`).classList.remove('hidden');
  
  // Update tab buttons
  const tabButtons = document.querySelectorAll('.tab-btn');
  tabButtons.forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
}

function showAddSaleForm() {
  document.getElementById('add-sale-form').classList.remove('hidden');
  loadItemsForSale();
}

function hideAddSaleForm() {
  document.getElementById('add-sale-form').classList.add('hidden');
}

function loadItemsForSale() {
  // Load items for sale dropdown
  const saleItemDropdown = document.getElementById('sale-item');
  
  // For demo purposes, add some options
  saleItemDropdown.innerHTML = `
    <option value="">Select Item</option>
    <option value="1">Laptop</option>
    <option value="2">Milk</option>
  `;
}

function updateAvailableStock() {
  const itemId = document.getElementById('sale-item').value;
  const availableStockInput = document.getElementById('available-stock');
  
  // For demo purposes, show fixed values
  if (itemId === '1') {
    availableStockInput.value = '15';
  } else if (itemId === '2') {
    availableStockInput.value = '5';
  } else {
    availableStockInput.value = '';
  }
}

function saveSaleItem(event) {
  event.preventDefault();
  
  // Get form values
  const itemId = document.getElementById('sale-item').value;
  const itemName = document.getElementById('sale-item').options[document.getElementById('sale-item').selectedIndex].text;
  const saleDate = document.getElementById('sale-date').value;
  const quantity = document.getElementById('sale-quantity').value;
  const unitPrice = document.getElementById('sale-price').value;
  const customer = document.getElementById('customer').value;
  
  // Save to database (to be implemented)
  console.log('Saving sale:', {
    itemId,
    saleDate,
    quantity,
    unitPrice,
    customer
  });
  
  // Update UI
  const salesList = document.getElementById('sales-list');
  const newRow = document.createElement('tr');
  newRow.innerHTML = `
    <td>{itemName}</td>
    <td>{saleDate}</td>
    <td>{quantity}</td>
    <td>${parseFloat(unitPrice).toFixed(2)}</td>
    <td>${(parseFloat(unitPrice) * parseInt(quantity)).toFixed(2)}</td>
    <td>{customer || 'N/A'}</td>
    <td>
      <button class="action-btn edit">Edit</button>
      <button class="action-btn delete">Delete</button>
    </td>
  `;
  salesList.appendChild(newRow);
  
  // Reset form and hide it
  document.getElementById('add-sale-form').reset();
  hideAddSaleForm();
}

function showAddWastageForm() {
  document.getElementById('add-wastage-form').classList.remove('hidden');
  loadItemsForWastage();
}

function hideAddWastageForm() {
  document.getElementById('add-wastage-form').classList.add('hidden');
}

function loadItemsForWastage() {
  // Load items for wastage dropdown
  const wastageItemDropdown = document.getElementById('wastage-item');
  
  // For demo purposes, add some options
  wastageItemDropdown.innerHTML = `
    <option value="">Select Item</option>
    <option value="1">Laptop</option>
    <option value="2">Milk</option>
  `;
}

function updateWastageAvailableStock() {
  const itemId = document.getElementById('wastage-item').value;
  const availableStockInput = document.getElementById('wastage-available-stock');
  
  // For demo purposes, show fixed values
  if (itemId === '1') {
    availableStockInput.value = '15';
  } else if (itemId === '2') {
    availableStockInput.value = '5';
  } else {
    availableStockInput.value = '';
  }
}

function saveWastageItem(event) {
  event.preventDefault();
  
  // Get form values
  const itemId = document.getElementById('wastage-item').value;
  const itemName = document.getElementById('wastage-item').options[document.getElementById('wastage-item').selectedIndex].text;
  const wastageDate = document.getElementById('wastage-date').value;
  const quantity = document.getElementById('wastage-quantity').value;
  const reason = document.getElementById('wastage-reason').value;
  const notes = document.getElementById('wastage-notes').value;
  
  // Save to database (to be implemented)
  console.log('Saving wastage:', {
    itemId,
    wastageDate,
    quantity,
    reason,
    notes
  });
  
  // Calculate cost impact (for demo purposes)
  let costImpact = 0;
  if (itemId === '1') {
    costImpact = 800 * parseInt(quantity);
  } else if (itemId === '2') {
    costImpact = 2.5 * parseInt(quantity);
  }
  
  // Update UI
  const wastageList = document.getElementById('wastage-list');
  const newRow = document.createElement('tr');
  newRow.innerHTML = `
    <td>{itemName}</td>
    <td>{wastageDate}</td>
    <td>{quantity}</td>
    <td>{document.getElementById('wastage-reason').options[document.getElementById('wastage-reason').selectedIndex].text}</td>
    <td>${costImpact.toFixed(2)}</td>
    <td>
      <button class="action-btn view" title="View Details">View</button>
      <button class="action-btn delete">Delete</button>
    </td>
  `;
  wastageList.appendChild(newRow);
  
  // Reset form and hide it
  document.getElementById('add-wastage-form').reset();
  hideAddWastageForm();
}

function loadOutgoingItems() {
  // Implement loading outgoing items from database
  const salesList = document.getElementById('sales-list');
  const wastageList = document.getElementById('wastage-list');
  
  // For demo purposes, show some sample data
  salesList.innerHTML = `
    <tr>
      <td>Laptop</td>
      <td>2025-04-18</td>
      <td>2</td>
      <td>950.00</td>
      <td>1,900.00</td>
      <td>TechCorp Inc.</td>
      <td>
        <button class="action-btn edit">Edit</button>
        <button class="action-btn delete">Delete</button>
      </td>
    </tr>
    <tr>
      <td>Milk</td>
      <td>2025-04-22</td>
      <td>10</td>
      <td>3.50</td>
      <td>35.00</td>
      <td>Local Cafe</td>
      <td>
        <button class="action-btn edit">Edit</button>
        <button class="action-btn delete">Delete</button>
      </td>
    </tr>
  `;
  
  wastageList.innerHTML = `
    <tr>
      <td>Milk</td>
      <td>2025-04-23</td>
      <td>5</td>
      <td>Expired</td>
      <td>12.50</td>
      <td>
        <button class="action-btn view" title="View Details">View</button>
        <button class="action-btn delete">Delete</button>
      </td>
    </tr>
  `;
}

// Reports Management
function changeReportType() {
  const reportType = document.getElementById('report-type').value;
  
  // Show/hide custom date range based on report type
  if (reportType === 'inventory') {
    document.getElementById('date-range').parentElement.classList.add('hidden');
    document.getElementById('custom-date-range').classList.add('hidden');
  } else {
    document.getElementById('date-range').parentElement.classList.remove('hidden');
  }
}

function generateReport() {
  const reportType = document.getElementById('report-type').value;
  const dateRange = document.getElementById('date-range').value;
  const reportContainer = document.getElementById('report-container');
  
  // For demo purposes, show different report templates based on selection
  if (reportType === 'inventory') {
    reportContainer.innerHTML = `
      <h2>Current Inventory Report</h2
        <h2>Current Inventory Report</h2>
      <table class="report-table">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Item Type</th>
            <th>Current Stock</th>
            <th>Stock Value</th>
            <th>Expiry Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Laptop</td>
            <td>Electronics</td>
            <td>15</td>
            <td>12,000.00</td>
            <td>N/A</td>
            <td><span class="status in-stock">In Stock</span></td>
          </tr>
          <tr>
            <td>Milk</td>
            <td>Grocery</td>
            <td>5</td>
            <td>12.50</td>
            <td>2025-05-10</td>
            <td><span class="status low-stock">Low Stock</span></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3"><strong>Total Inventory Value</strong></td>
            <td><strong>12,012.50</strong></td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
    `;
  } else if (reportType === 'stock-movement') {
    reportContainer.innerHTML = `
      <h2>Stock Movement Report (This Month)</h2>
      <table class="report-table">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Opening Stock</th>
            <th>Incoming</th>
            <th>Outgoing (Sales)</th>
            <th>Outgoing (Wastage)</th>
            <th>Closing Stock</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Laptop</td>
            <td>7</td>
            <td>10</td>
            <td>2</td>
            <td>0</td>
            <td>15</td>
          </tr>
          <tr>
            <td>Milk</td>
            <td>0</td>
            <td>20</td>
            <td>10</td>
            <td>5</td>
            <td>5</td>
          </tr>
        </tbody>
      </table>
      <div class="chart-container">
        <p>Stock Movement Chart would appear here</p>
      </div>
    ;
  } else if (reportType === 'expiry') {
    reportContainer.innerHTML = 
      <h2>Expiring Items Report</h2>
      <table class="report-table">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Current Stock</th>
            <th>Expiry Date</th>
            <th>Days Left</th>
            <th>Stock Value</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Milk</td>
            <td>5</td>
            <td>2025-05-10</td>
            <td>12</td>
            <td>12.50</td>
            <td><button class="action-btn">Plan Sale</button></td>
          </tr>
        </tbody>
      </table>
    ;
  } else if (reportType === 'wastage') {
    reportContainer.innerHTML = 
      <h2>Wastage Analysis Report (This Month)</h2>
      <div class="summary-cards">
        <div class="summary-card">
          <h3>Total Wastage Items</h3>
          <div class="summary-value">5</div>
        </div>
        <div class="summary-card">
          <h3>Total Cost Impact</h3>
          <div class="summary-value">12.50</div>
        </div>
        <div class="summary-card">
          <h3>Primary Reason</h3>
          <div class="summary-value">Expired (100%)</div>
        </div>
      </div>
      <table class="report-table">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Wastage Date</th>
            <th>Quantity</th>
            <th>Reason</th>
            <th>Cost Impact</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Milk</td>
            <td>2025-04-23</td>
            <td>5</td>
            <td>Expired</td>
            <td>12.50</td>
          </tr>
        </tbody>
      </table>
      <div class="chart-container">
        <p>Wastage by Reason Chart would appear here</p>
      </div>
    ;
  } else if (reportType === 'sales') {
    reportContainer.innerHTML = 
      <h2>Sales Summary Report (This Month)</h2>
      <div class="summary-cards">
        <div class="summary-card">
          <h3>Total Sales</h3>
          <div class="summary-value">1,935.00</div>
        </div>
        <div class="summary-card">
          <h3>Total Items Sold</h3>
          <div class="summary-value">12</div>
        </div>
        <div class="summary-card">
          <h3>Best Selling Item</h3>
          <div class="summary-value">Milk (10 units)</div>
        </div>
      </div>
      <table class="report-table">
        <thead>
          <tr>
            <th>Item Name</th>
            <th>Quantity Sold</th>
            <th>Revenue</th>
            <th>% of Total</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Laptop</td>
            <td>2</td>
            <td>1,900.00</td>
            <td>98.2%</td>
          </tr>
          <tr>
            <td>Milk</td>
            <td>10</td>
            <td>35.00</td>
            <td>1.8%</td>
          </tr>
        </tbody>
      </table>
      <div class="chart-container">
        <p>Sales Trend Chart would appear here</p>
      </div>
    `;
  }
}

function exportReport() {
  alert('Report export functionality would be implemented here');
}

// Date range selection handling
document.getElementById('date-range').addEventListener('change', function() {
  if (this.value === 'custom') {
    document.getElementById('custom-date-range').classList.remove('hidden');
  } else {
    document.getElementById('custom-date-range').classList.add('hidden');
  }
});
async function loadItems() {
  const response = await fetch('http://localhost:3000/items');
  const items = await response.json();
  const itemsList = document.getElementById('items-list');
  itemsList.innerHTML = items.map(item => `
    <tr>
      <td>{item.name}</td>
      <td>{item.type}</td>
      <td>{item.stock}</td>
      <td>{item.expiry_date || 'N/A'}</td>
      <td><span class="status ${item.stock < 10 ? 'low-stock' : 'in-stock'}">{item.stock < 10 ? 'Low Stock' : 'In Stock'}</span></td>
      <td><button class="action-btn edit">Edit</button><button class="action-btn delete">Delete</button></td>
    </tr>`).join('');}

// Initialize UI
document.addEventListener('DOMContentLoaded', function() {
  changeReportType();
});
let item = itemsData.find(i => i.name.toLowerCase() === itemName.toLowerCase());
if (item) {
  item.stock += parseInt(quantity);
} else {
  // If item not found, add it
  item = {
    id: nextId.items++,
    name: itemName,
    type: itemType,
    stock: parseInt(quantity),
    unitPrice: parseFloat(unitPrice),
    expiryDate: expiryDate || null
  };
  itemsData.push(item);
}
function updateItemDropdowns() {
  const incomingItemSelect = document.getElementById('incoming-item');
  const saleItemSelect = document.getElementById('sale-item');
  const wastageItemSelect = document.getElementById('wastage-item');

  [incomingItemSelect, saleItemSelect, wastageItemSelect].forEach(select => {
    if (!select) return;
    select.innerHTML = '<option value="">Select Item</option>';
    itemsData.forEach(item => {
      const option = document.createElement('option');
      option.value = item.id;
      option.textContent = item.name;
      select.appendChild(option);
    });
  });
}

updateItemDropdowns();
function updateAvailableStock() {
  const itemId = parseInt(document.getElementById('sale-item').value, 10);
  const item = itemsData.find(i => i.id === itemId);
  document.getElementById('available-stock').value = item ? item.stock : '';
}
