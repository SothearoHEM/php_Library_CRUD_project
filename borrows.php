<?php
    include 'config.php';

    // Handle delete action
    if(isset($_GET['delete'])) {
        $borrow_id = mysqli_real_escape_string($conn, $_GET['delete']);
        $sql = "UPDATE borrows SET is_delete = 1 WHERE borrow_id = '$borrow_id'";
        if (mysqli_query($conn, $sql)) {
            header("Location: borrows.php?msg=deleted");
            exit();
        } else {
            die("Error deleting borrow record: " . mysqli_error($conn));
        }
    }
    // Handle return action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_id'])) {
        $borrow_id = mysqli_real_escape_string($conn, $_POST['borrow_id']);
        
        // Get all borrowed books and their quantities
        $details_sql = "SELECT book_id, qty FROM borrow_details WHERE borrow_id = '$borrow_id'";
        $details_result = mysqli_query($conn, $details_sql);
        
        if ($details_result) {
            // Update stock for each book
            while ($detail = mysqli_fetch_assoc($details_result)) {
                $book_id = mysqli_real_escape_string($conn, $detail['book_id']);
                $quantity = (int)$detail['qty'];
                $update_stock_sql = "UPDATE books SET stock = stock + $quantity WHERE book_id = '$book_id'";
                if (!mysqli_query($conn, $update_stock_sql)) {
                    die("Error updating book stock: " . mysqli_error($conn));
                }
            }
        }
        
        $sql = "UPDATE borrows SET status = 'Returned', return_date = NOW() WHERE borrow_id = '$borrow_id'";
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
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
                <table id="borrowsTable">
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
                                    <a href="borrow_details.php?id=<?= htmlspecialchars($row['borrow_id']) ?>" class="view-btn">View</a>
                                    <?php if ($row['status'] === 'Borrowed'): ?>
                                        <form method="POST" action="borrows.php" style="display:inline;">
                                            <input type="hidden" name="borrow_id" value="<?= htmlspecialchars($row['borrow_id']) ?>">
                                            <input type="submit" value="Return" class="return-btn" onclick="return confirm('Are you sure you want to mark this borrow record as returned?');">
                                        </form>
                                    <?php endif; ?>
                                    <a href="borrows.php?delete=<?= htmlspecialchars($row['borrow_id']) ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this borrow record?');">Delete</a>
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
    <script>
        $(document).ready(function() {
            $('#borrowsTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthChange": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>
</body>  
</html>