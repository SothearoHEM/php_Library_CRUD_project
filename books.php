<?php
    include 'config.php';

    // Handle delete action
    if (isset($_GET['delete'])){
        $book_id = mysqli_real_escape_string($conn, $_GET['delete']);
        $sql = "UPDATE books SET is_delete = 1 WHERE book_id = '$book_id'";
        if(mysqli_query($conn,$sql)){
            header("Location: books.php");
            exit();
        }
    }

    $sql = "SELECT * FROM books WHERE is_delete = 0 ORDER BY created_at DESC";
    $result = mysqli_query($conn,$sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books - Library Management System</title>
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
                <h1>Library Management</h1>
                <div class="menu">
                    <a href="members.php">Members</a>
                    <a href="books.php">Books</a>
                    <a href="borrows.php">Borrows</a>
                </div>
            </div>
        </div>
        <div class="book-container">
            <div class="book-header">
                <h2>Book List</h2>
                <a href="book_add.php" class="add-book-btn">Add New Book</a>
            </div>
            <div class="book-list">
                <?php if(mysqli_num_rows($result)>0): ?>
                    <table id="booksTable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Published Year</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['book_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['author']); ?></td>
                                <td><?php echo htmlspecialchars($row['publish_year']); ?></td>
                                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                                <td>
                                    <a href="book_edit.php?id=<?php echo htmlspecialchars($row['book_id']); ?>" class="edit-btn">Edit</a> | 
                                    <a href="books.php?delete=<?php echo htmlspecialchars($row['book_id']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No books found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#booksTable').DataTable({
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