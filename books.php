<?php
    include 'config.php';

    // Handle delete action
    if (isset($_GET['delete'])){
        $book_id = $_GET['delete'];
        $sql = "UPDATE books SET is_delete = 1 WHERE book_id = $book_id";
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
        .book-container {
            width: 80%;
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            align-items: center;
            margin: auto;

        }
        .book-container .book-header {
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
        .book-container .book-header h2 {
            color: #333;
        }
        .book-container .book-header .add-book-btn {
            text-decoration: none;
            background-color: #e06b21;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .book-container .book-header .add-book-btn:hover {
            background-color: #c25a1a;
        }
        .book-container .book-list {
            width: 100%;
        }
        .book-container .book-list table {
            width: 100%; 
            background: white; 
            border-collapse: collapse; 
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .book-container .book-list table th,
        .book-container .book-list table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }   
        .book-container .book-list table th {
            background-color: #e06b21;
            color: white;
        }
        .book-container .book-list table td {
            color: #333;
        }
         .book-container .book-list table tr:hover {
            background-color: #f1f1f1;
        }
        .book-container .book-list table td a {
            text-decoration: none;
            color: white;
            border-radius: 8px;
            padding: 5px 10px;
            transition: background-color 0.3s, color 0.3s;
        }
        .book-container .book-list table td a.delete-btn {
            background-color: #f44336;
        }
        .book-container .book-list table td a.delete-btn:hover {
            background-color: #da190b;
        }
        .book-container .book-list table td a.edit-btn {
            background-color: #2196F3;
        }
        .book-container .book-list table td a.edit-btn:hover {
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
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Published Year</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['book_id']; ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['author']; ?></td>
                                <td><?php echo $row['publish_year']; ?></td>
                                <td><?php echo $row['stock']; ?></td>
                                <td>
                                    <a href="book_edit.php?id=<?php echo $row['book_id']; ?>" class="edit-btn">Edit</a> | 
                                    <a href="books.php?delete=<?php echo $row['book_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>No books found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>