<?php
    include 'config.php';

    // Handle delete action
    if(isset($_GET['delete'])) {
        $borrow_id = $_GET['delete'];
        $sql = "UPDATE borrows SET is_delete = 1 WHERE borrow_id = $borrow_id";
        if (mysqli_query($conn, $sql)) {
            header("Location: borrows.php?msg=deleted");
            exit();
        } else {
            die("Error deleting borrow record: " . mysqli_error($conn));
        }
    }
    // Handle return action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_id'])) {
        $borrow_id = $_POST['borrow_id'];
        
        // Get all borrowed books and their quantities
        $details_sql = "SELECT book_id, qty FROM borrow_details WHERE borrow_id = $borrow_id";
        $details_result = mysqli_query($conn, $details_sql);
        
        if ($details_result) {
            // Update stock for each book
            while ($detail = mysqli_fetch_assoc($details_result)) {
                $book_id = $detail['book_id'];
                $quantity = $detail['qty'];
                $update_stock_sql = "UPDATE books SET stock = stock + $quantity WHERE book_id = $book_id";
                if (!mysqli_query($conn, $update_stock_sql)) {
                    die("Error updating book stock: " . mysqli_error($conn));
                }
            }
        }
        
        $sql = "UPDATE borrows SET status = 'Returned', return_date = NOW() WHERE borrow_id = $borrow_id";
        if (mysqli_query($conn, $sql)) {
            header("Location: borrows.php?msg=returned");
            exit();
        } else {
            die("Error updating borrow record: " . mysqli_error($conn));
        }
    }
        $sql = "SELECT b.borrow_id,
               m.full_name AS member_name,
               b.borrow_date,
               b.return_date,
               b.total_books,
               b.status
            FROM borrows b
            JOIN members m ON b.member_id = m.member_id
            WHERE b.is_delete = 0
            ORDER BY b.borrow_date DESC";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Records</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; 
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }
        .container {
            margin: 0 auto;
        }
        .container .header {    
            display: flex;
            background-color: #4d4d4d76;
            padding: 20px 0;
            align-items: center;
        }
        .container .header .header-container {
            display: flex;
            width: 80%;
            justify-content: space-between;
            align-items: center;
            margin: 0 auto;
        } 
        .container .header h1 {
            color: #262424;
            border-bottom: 3px solid #2196F3;
            padding-bottom: 10px;
        }
        .container .header .menu a {
            text-decoration: none;
            color: white;
            background-color: #2195f3cb;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            margin-left: 15px;
        }
        .container .header .menu a:hover {
            background-color: #3a75b0;
        }
        .borrow-container {
            width: 80%;
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            align-items: center;
            margin: auto;

        }
        .borrow-container .borrow-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            margin-top: 30px;
            background-color: #ffffff76;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .borrow-container .borrow-header h2 {
            color: #333;
        }
        .borrow-container .borrow-header .add-borrow-btn {
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .borrow-container .borrow-header .add-borrow-btn:hover {
            background-color: #45a049;
        }
        .borrow-container .borrow-list {
            width: 100%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .borrow-container .borrow-list table {
            width: 100%; 
            background: white; 
            border-collapse: collapse; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .borrow-container .borrow-list table th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .borrow-container .borrow-list table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .borrow-container .borrow-list table tr:hover {
            background-color: #f1f1f1;
        }
        .borrow-container .borrow-list table a {
            text-decoration: none;
            color: white;
            border-radius: 8px;
            padding: 5px 10px;
            transition: background-color 0.3s, color 0.3s;
        }
        .borrow-container .borrow-list table a.view-btn {
            background-color: #2196F3;
        }
        .borrow-container .borrow-list table a.view-btn:hover {
            background-color: #1976D2;
        }
        .borrow-container .borrow-list table input.return-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }
        .borrow-container .borrow-list table input.return-btn:hover {
            background-color: #45a049;
        }
        .borrow-container .borrow-list table a.delete-btn {
            background-color: #f44336;
        }
        .borrow-container .borrow-list table a.delete-btn:hover {
            background-color: #da190b;
        }
        </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-container">
                <h1>Borrow Records</h1>
                <div class="menu">
                    <a href="members.php">Members</a>
                    <a href="books.php">Books</a>
                    <a href="borrows.php">Borrows</a>
                </div>
            </div>
        </div>
        <div class="borrow-container">
            <div class="borrow-header">
                <h2>Borrow Records</h2>
                <a href="borrow_add.php" class="add-borrow-btn">Add New Borrow Record</a>
            </div>
            <div class="borrow-list">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Borrow ID</th>
                            <th>Member Name</th>
                            <th>Borrow Date</th>
                            <th>Return Date</th>
                            <th>Total Books</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['borrow_id']) ?></td>
                                <td><?= htmlspecialchars($row['member_name']) ?></td>
                                <td><?= htmlspecialchars($row['borrow_date']) ?></td>
                                <td><?= htmlspecialchars($row['return_date']) ?></td>
                                <td><?= htmlspecialchars($row['total_books']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td class="actions">
                                    <a href="borrow_details.php?id=<?= $row['borrow_id'] ?>" class="view-btn">View</a>
                                    <?php if ($row['status'] === 'Borrowed'): ?>
                                        <form method="POST" action="borrows.php" style="display:inline;">
                                            <input type="hidden" name="borrow_id" value="<?= $row['borrow_id'] ?>">
                                            <input type="submit" value="Return" class="return-btn" onclick="return confirm('Are you sure you want to mark this borrow record as returned?');">
                                        </form>
                                    <?php endif; ?>
                                    <a href="borrows.php?delete=<?= $row['borrow_id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this borrow record?');">Delete</a>
                                </td>  
                            </tr>  
                        <?php endwhile; ?>
                    </tbody>  
                </table>  
            <?php else: ?>  
                No borrow records found.
            <?php endif; ?>  
            </div>
        </div>  
    </div>  
</body>  
</html>