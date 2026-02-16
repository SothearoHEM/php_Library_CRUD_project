<?php
    include 'config.php';

    $error = '';

    if (!isset($_GET['id'])) {
        header("Location: books.php");
        exit();
    }
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM books WHERE book_id=$book_id";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Error retrieving book: " . mysqli_error($conn));
    }
    $book = mysqli_fetch_assoc($result);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $book_id = $_GET['id'];
        $title = $_POST['title'];
        $author = $_POST['author'];
        $publish_year = $_POST['publish_year'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        if (empty($title) || empty($author) || empty($publish_year) || empty($price) || empty($stock)) {
            $error = "All fields are required.";
        } else {
            $sql = "UPDATE books SET title='$title', author='$author', publish_year='$publish_year', price='$price', stock='$stock' WHERE book_id=$book_id";
            if (mysqli_query($conn, $sql)) {
                header("Location: books.php");
                exit();
            } else {
                die("Error updating book: " . mysqli_error($conn));
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
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
        .add-books-form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            margin-top: 30px;
            align-items: center;
        }
        .add-books-form form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        .add-books-form form h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .add-books-form form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .add-books-form form input[type="text"],
        .add-books-form form input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .add-books-form form input[type="submit"],
        .add-books-form form a {
            text-decoration: none;
            color: white;
            background-color: #2195f3cb;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            margin-right: 10px;
            border: none;
        }
        .add-books-form form a.cancel-btn {
            background-color: #f44336;
        }
        .add-books-form form a.cancel-btn:hover {
            background-color: #d32f2f;
        }
        .add-books-form form input[type="submit"]:hover {
            background-color: #3a75b0;
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
        <div class="add-books-form">
            <form method="POST" action="">
                <h2>Edit Book</h2>
                <?php if ($error): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo ($book['title']); ?>" required>

                <label for="author">Author:</label>
                <input type="text" id="author" name="author" value="<?php echo ($book['author']); ?>" required>

                <label for="publish_year">Published Year:</label>
                <input type="number" id="publish_year" name="publish_year" value="<?php echo ($book['publish_year']); ?>" required>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo ($book['price']); ?>" required>

                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" value="<?php echo ($book['stock']); ?>" required>

                <input type="submit" value="Update Book">
                <a href="books.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>