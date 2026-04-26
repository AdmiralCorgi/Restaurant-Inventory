<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #ffb7c5 ; }
        .tab-content { margin-top: 20px; }
        .nav-tabs .nav-link.active { background-color: #ff2f57 !important; }
        .form-control, .btn { margin-bottom: 10px; }
    </style>
</head>
<body>

<h2 class="text-center">Restaurant Inventory Management System</h2>

<ul class="nav nav-tabs" id="tabMenu">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#ingredients">Ingredients</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#transactions">Transactions</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#orders">Purchase Orders</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#suppliers">Suppliers</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reports">Reports</a></li>
</ul>

<div class="tab-content">
    <!-- Existing Tabs (Ingredients, Transactions, Orders, Suppliers) --> 
<!-- INGREDIENTS TAB -->
    <div class="tab-pane fade show active" id="ingredients">
        <h4>Ingredients</h4>
        <form method="post">
            <input type="hidden" name="edit_ingredient_id" value="<?php echo $_GET['edit_ingredient'] ?? ''; ?>">
            <input type="text" name="iname" class="form-control" placeholder="Ingredient Name" value="<?php echo $_GET['iname'] ?? ''; ?>" required>
            <input type="text" name="iunit" class="form-control" placeholder="Unit (kg, liters, etc.)" value="<?php echo $_GET['iunit'] ?? ''; ?>" required>
            <input type="number" step="0.01" name="istock" class="form-control" placeholder="Stock Level" value="<?php echo $_GET['istock'] ?? ''; ?>" required>
            <input type="number" step="0.01" name="ithreshold" class="form-control" placeholder="Reorder Threshold" value="<?php echo $_GET['ithreshold'] ?? ''; ?>" required>
            <button type="submit" name="save_ingredient" class="btn btn-primary">Save Ingredient</button>
        </form>
        <?php
        if (isset($_POST['save_ingredient'])) {
            if (!empty($_POST['edit_ingredient_id'])) {
                $stmt = $conn->prepare("UPDATE Ingredients SET name=?, unit=?, stock_level=?, reorder_threshold=? WHERE ingredient_id=?");
                $stmt->bind_param("ssddi", $_POST['iname'], $_POST['iunit'], $_POST['istock'], $_POST['ithreshold'], $_POST['edit_ingredient_id']);
            } else {
                $stmt = $conn->prepare("INSERT INTO Ingredients (name, unit, stock_level, reorder_threshold) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssdd", $_POST['iname'], $_POST['iunit'], $_POST['istock'], $_POST['ithreshold']);
            }
            $stmt->execute();
        }
        if (isset($_GET['delete_ingredient'])) {
            $stmt = $conn->prepare("DELETE FROM Ingredients WHERE ingredient_id = ?");
            $stmt->bind_param("i", $_GET['delete_ingredient']);
            
        }
        ?>
        <table class="table table-striped">
            <thead><tr><th>ID</th><th>Name</th><th>Unit</th><th>Stock</th><th>Threshold</th><th>Actions</th></tr></thead>
            <tbody>
            <?php
            $res = $conn->query("SELECT * FROM Ingredients");
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['ingredient_id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['unit']}</td>
                        <td>{$row['stock_level']}</td>
                        <td>{$row['reorder_threshold']}</td>
                        <td>
                            <a href='?edit_ingredient={$row['ingredient_id']}&iname={$row['name']}&iunit={$row['unit']}&istock={$row['stock_level']}&ithreshold={$row['reorder_threshold']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='?delete_ingredient={$row['ingredient_id']}' class='btn btn-danger btn-sm'>Delete</a>
                        </td>
                      </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>


    <!-- TRANSACTIONS -->
    <div class="tab-pane fade" id="transactions">
        <h4>Inventory Transactions</h4>
        <form method="post">
            <input type="number" name="t_ingredient" class="form-control" placeholder="Ingredient ID" required>
            <select name="t_type" class="form-control" required>
                <option value="IN">IN</option>
                <option value="OUT">OUT</option>
            </select>
            <input type="number" step="0.01" name="t_quantity" class="form-control" placeholder="Quantity" required>
            <button type="submit" name="add_transaction" class="btn btn-primary">Add Transaction</button>
        </form>
        <?php
        if (isset($_POST['add_transaction'])) {
            $stmt = $conn->prepare("INSERT INTO Inventory_Transactions (ingredient_id, type, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("isd", $_POST['t_ingredient'], $_POST['t_type'], $_POST['t_quantity']);
            $stmt->execute();
        }
        if (isset($_GET['delete_transaction'])) {
            $stmt = $conn->prepare("DELETE FROM Inventory_Transactions WHERE transaction_id = ?");
            $stmt->bind_param("i", $_GET['delete_transaction']);
            $stmt->execute();
        }
        ?>
        <table class="table table-striped">
            <thead><tr><th>ID</th><th>Ingredient ID</th><th>Type</th><th>Qty</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php
            $res = $conn->query("SELECT * FROM Inventory_Transactions ORDER BY transaction_date DESC");
            while ($row = $res->fetch_assoc()) {
                echo "<tr><td>{$row['transaction_id']}</td><td>{$row['ingredient_id']}</td><td>{$row['type']}</td><td>{$row['quantity']}</td><td>{$row['transaction_date']}</td><td><a href='?delete_transaction={$row['transaction_id']}' class='btn btn-danger btn-sm'>Delete</a></td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- PURCHASE ORDERS -->
    <div class="tab-pane fade" id="orders">
        <h4>Purchase Orders</h4>
        <form method="post">
            <input type="number" name="po_ingredient" class="form-control" placeholder="Ingredient ID" required>
            <input type="number" name="po_supplier" class="form-control" placeholder="Supplier ID" required>
            <input type="number" step="0.01" name="po_qty" class="form-control" placeholder="Quantity" required>
            <input type="date" name="po_date" class="form-control" required>
            <button type="submit" name="add_order" class="btn btn-primary">Add Order</button>
        </form>
        <?php
        if (isset($_POST['add_order'])) {
            $stmt = $conn->prepare("INSERT INTO PurchaseOrders (ingredient_id, supplier_id, order_quantity, order_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iids", $_POST['po_ingredient'], $_POST['po_supplier'], $_POST['po_qty'], $_POST['po_date']);
            $stmt->execute();
        }
        if (isset($_GET['delete_order'])) {
            $stmt = $conn->prepare("DELETE FROM PurchaseOrders WHERE order_id = ?");
            $stmt->bind_param("i", $_GET['delete_order']);
            $stmt->execute();
        }
        ?>
        <table class="table table-striped">
            <thead><tr><th>ID</th><th>Ingredient ID</th><th>Supplier ID</th><th>Quantity</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php
            $res = $conn->query("SELECT * FROM PurchaseOrders");
            while ($row = $res->fetch_assoc()) {
                echo "<tr><td>{$row['order_id']}</td><td>{$row['ingredient_id']}</td><td>{$row['supplier_id']}</td><td>{$row['order_quantity']}</td><td>{$row['order_date']}</td><td><a href='?delete_order={$row['order_id']}' class='btn btn-danger btn-sm'>Delete</a></td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- SUPPLIERS -->
    <div class="tab-pane fade" id="suppliers">
        <h4>Suppliers</h4>
        <form method="post">
            <input type="text" name="sname" class="form-control" placeholder="Supplier Name" required>
            <input type="email" name="semail" class="form-control" placeholder="Email">
            <input type="text" name="sphone" class="form-control" placeholder="Phone">
            <button type="submit" name="add_supplier" class="btn btn-primary">Add Supplier</button>
        </form>
        <?php
        if (isset($_POST['add_supplier'])) {
            $stmt = $conn->prepare("INSERT INTO Suppliers (name, contact_email, contact_phone) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_POST['sname'], $_POST['semail'], $_POST['sphone']);
            $stmt->execute();
        }
        if (isset($_GET['delete_supplier'])) {
            $stmt = $conn->prepare("DELETE FROM Suppliers WHERE supplier_id = ?");
            $stmt->bind_param("i", $_GET['delete_supplier']);
            $stmt->execute();
        }
        ?>
        <table class="table table-striped">
            <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Action</th></tr></thead>
            <tbody>
            <?php
            $res = $conn->query("SELECT * FROM Suppliers");
            while ($row = $res->fetch_assoc()) {
                echo "<tr><td>{$row['supplier_id']}</td><td>{$row['name']}</td><td>{$row['contact_email']}</td><td>{$row['contact_phone']}</td><td><a href='?delete_supplier={$row['supplier_id']}' class='btn btn-danger btn-sm'>Delete</a></td></tr>";
            }
            ?>
              </tbody>
              </table>
      </div>
  
    <!-- REPORTS -->
    <div class="tab-pane fade" id="reports">
        <h4>Reports</h4>

        <h5>Low Stock Ingredients</h5>
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Name</th><th>Stock</th><th>Threshold</th></tr></thead>
            <tbody>
            <?php
            $lowStock = $conn->query("SELECT ingredient_id, name, stock_level, reorder_threshold FROM Ingredients WHERE stock_level <= reorder_threshold");
            while ($row = $lowStock->fetch_assoc()) {
                echo "<tr><td>{$row['ingredient_id']}</td><td>{$row['name']}</td><td>{$row['stock_level']}</td><td>{$row['reorder_threshold']}</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <h5>Recent Inventory Transactions (Past 7 Days)</h5>
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Ingredient</th><th>Type</th><th>Quantity</th><th>Date</th></tr></thead>
            <tbody>
            <?php
            $recentTransactions = $conn->query("SELECT t.transaction_id, i.name AS ingredient_name, t.type, t.quantity, t.transaction_date FROM Inventory_Transactions t JOIN Ingredients i ON t.ingredient_id = i.ingredient_id WHERE t.transaction_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY t.transaction_date DESC");
            while ($row = $recentTransactions->fetch_assoc()) {
                echo "<tr><td>{$row['transaction_id']}</td><td>{$row['ingredient_name']}</td><td>{$row['type']}</td><td>{$row['quantity']}</td><td>{$row['transaction_date']}</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <h5>Purchase Orders Summary</h5>
        <table class="table table-bordered">
            <thead><tr><th>Order ID</th><th>Ingredient</th><th>Supplier</th><th>Quantity</th><th>Date</th></tr></thead>
            <tbody>
            <?php
            $ordersSummary = $conn->query("SELECT p.order_id, i.name AS ingredient_name, s.name AS supplier_name, p.order_quantity, p.order_date FROM PurchaseOrders p JOIN Ingredients i ON p.ingredient_id = i.ingredient_id JOIN Suppliers s ON p.supplier_id = s.supplier_id ORDER BY p.order_date DESC");
            while ($row = $ordersSummary->fetch_assoc()) {
                echo "<tr><td>{$row['order_id']}</td><td>{$row['ingredient_name']}</td><td>{$row['supplier_name']}</td><td>{$row['order_quantity']}</td><td>{$row['order_date']}</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>