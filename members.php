<?php
    include 'config.php';
    
    // Handle delete action
    if (isset($_GET['delete'])) {
        $member_id = mysqli_real_escape_string($conn, $_GET['delete']);
        $sql = "UPDATE members SET is_delete = 1 WHERE member_id = '$member_id'";
        if (mysqli_query($conn, $sql)) {
            header("Location: members.php?msg=deleted");
            exit();
        }
    }

    $sql = "SELECT * FROM members WHERE is_delete = 0 ORDER BY created_at DESC";
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
    <title>Member - Library Management</title>
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
        <div class="member-container">
            <div class="member-header">
                <h2>Member List</h2>
                <a href="member_add.php" class="add-member-btn">Add New Member</a>
            </div>
            <div class="member-list">
            <?php  if (mysqli_num_rows($result) > 0): ?>
                <table border="1" cellpadding="10" cellspacing="0" id="membersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['member_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['address']); ?></td>
                            <td>
                                <a href="member_edit.php?id=<?php echo htmlspecialchars($row['member_id']); ?>" class="edit-btn">Edit</a> | 
                                <a href="members.php?delete=<?php echo htmlspecialchars($row['member_id']); ?>" onclick="return confirm('Are you sure you want to delete this member?');" class="delete-btn">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No members found.</p>
            <?php endif; ?>
        </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#membersTable').DataTable({
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
<?php mysqli_close($conn); ?>