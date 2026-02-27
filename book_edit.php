<?php
    include 'config.php';

    $error = '';

    if (!isset($_GET['id'])) {
        header("Location: books.php");
        exit();
    }
    $book_id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM books WHERE book_id='$book_id'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Error retrieving book: " . mysqli_error($conn));
    }
    $book = mysqli_fetch_assoc($result);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $book_id = mysqli_real_escape_string($conn, $_GET['id']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $author = mysqli_real_escape_string($conn, $_POST['author']);
        $publish_year = mysqli_real_escape_string($conn, $_POST['publish_year']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $stock = mysqli_real_escape_string($conn, $_POST['stock']);

        if (empty($title) || empty($author) || empty($publish_year) || empty($price) || empty($stock)) {
            $error = "All fields are required.";
        } else {
            $sql = "UPDATE books SET title='$title', author='$author', publish_year='$publish_year', price='$price', stock='$stock' WHERE book_id='$book_id'";
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
        <div class="add-books-form">
            <form method="POST" action="">
                <h2>Edit Book</h2>
                <?php if ($error): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>

                <label for="author">Author:</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>

                <label for="publish_year">Published Year:</label>
                <input type="number" id="publish_year" name="publish_year" value="<?php echo htmlspecialchars($book['publish_year']); ?>" required>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($book['price']); ?>" required>

                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($book['stock']); ?>" required>

                <input type="submit" value="Update Book">
                <a href="books.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>