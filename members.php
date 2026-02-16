<?php
    include 'config.php';
    
    // Handle delete action
    if (isset($_GET['delete'])) {
        $member_id = $_GET['delete'];        
        $sql = "UPDATE members SET is_delete = 1 WHERE member_id = $member_id";
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
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            margin-left: 15px;
        }
        .container .header .menu a:hover {
            background-color: #3a75b0;
        }
        .member-container {
            width: 80%;
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            align-items: center;
            margin: auto;

        }
        .member-container .member-header {
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
        .member-container .member-header h2 {
            color: #333;
        }
        .member-container .member-header .add-member-btn {
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .member-container .member-header .add-member-btn:hover {
            background-color: #45a049;
        }
        .member-container .member-list {
            width: 100%;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .member-container .member-list table {
            width: 100%; 
            background: white; 
            border-collapse: collapse; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .member-container .member-list table th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .member-container .member-list table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .member-container .member-list table tr:hover {
            background-color: #f1f1f1;
        }
        .member-container .member-list table a {
            text-decoration: none;
            color: white;
            border-radius: 8px;
            padding: 5px 10px;
            transition: background-color 0.3s, color 0.3s;
        }
        .member-container .member-list table a.delete-btn {
            background-color: #f44336;
        }
        .member-container .member-list table a.delete-btn:hover {
            background-color: #da190b;
        }
        .member-container .member-list table a.edit-btn {
            background-color: #2196F3;
        }
        .member-container .member-list table a.edit-btn:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-container">
                <h1>Library Management</h1>
                <div class="menu">
                    <a href="members.php">Members</a>
                    <a href="books.php">Books</a>
                    <a href="borrow.php">Borrow</a>
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
                <table border="1" cellpadding="10" cellspacing="0">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['member_id']; ?></td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td>
                                <a href="member_edit.php?id=<?php echo $row['member_id']; ?>" class="edit-btn">Edit</a> | 
                                <a href="members.php?delete=<?php echo $row['member_id']; ?>" onclick="return confirm('Are you sure you want to delete this member?');" class="delete-btn">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No members found.</p>
            <?php endif; ?>
        </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>