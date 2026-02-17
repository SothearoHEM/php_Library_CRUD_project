<?php
    include 'config.php';

    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $member_id = $_POST['member_id'];
        $borrow_date = $_POST['borrow_date'];
        $book_ids = isset($_POST['book_id']) ? $_POST['book_id'] : [];
        $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];
        $unit_prices = isset($_POST['unit_price']) ? $_POST['unit_price'] : [];

        if (empty($member_id) || empty($borrow_date) || empty($book_ids) || empty($quantities) || empty($unit_prices)) {
            $error = "All fields are required.";
        } else {
            $total_books = 0;
            foreach ($quantities as $qty) {
                $total_books += (int)$qty;
            }

            $sql = "INSERT INTO borrows (member_id, borrow_date, total_books, status) VALUES ('$member_id', '$borrow_date', '$total_books', 'Borrowed')";
            if (mysqli_query($conn, $sql)) {
                $borrow_id = mysqli_insert_id($conn);
                
                // Aggregate duplicate books by summing quantities
                $book_data = [];
                for ($i = 0; $i < count($book_ids); $i++) {
                    $book_id = $book_ids[$i];
                    $quantity = (int)$quantities[$i];
                    $unit_price = (float)$unit_prices[$i];
                    if (empty($book_id) || $quantity <= 0 || $unit_price <= 0) {
                        continue;
                    }
                    if (isset($book_data[$book_id])) {
                        // If book already exists, add to quantity
                        $book_data[$book_id]['qty'] += $quantity;
                        $book_data[$book_id]['amount'] = $book_data[$book_id]['qty'] * $book_data[$book_id]['unit_price'];
                    } else {
                        // New book entry
                        $book_data[$book_id] = [
                            'qty' => $quantity,
                            'unit_price' => $unit_price,
                            'amount' => $quantity * $unit_price
                        ];
                    }
                }
                
                // Insert aggregated book data
                foreach ($book_data as $book_id => $data) {
                    $detail_sql = "INSERT INTO borrow_details (borrow_id, book_id, qty, unit_price, amount) VALUES ('$borrow_id', '$book_id', '{$data['qty']}', '{$data['unit_price']}', '{$data['amount']}')";
                    if (!mysqli_query($conn, $detail_sql)) {
                        die("Error adding borrow detail: " . mysqli_error($conn));
                    }
                    // Update book stock
                    $update_stock_sql = "UPDATE books SET stock = stock - {$data['qty']} WHERE book_id = $book_id";
                    if (!mysqli_query($conn, $update_stock_sql)) {
                        die("Error updating book stock: " . mysqli_error($conn));
                    }
                }
                header("Location: borrows.php?msg=added");
                exit();
            } else {
                die("Error adding borrow record: " . mysqli_error($conn));
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Borrow Record</title>
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
        .form-container {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            margin-top: 30px;
            align-items: center;
        }
        .form-container form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
        }
        .form-container form h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .member-name-borrow-date {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            gap: 15px;
        }
        .member-name-borrow-date label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .member-name-borrow-date select,
        .member-name-borrow-date input[type="datetime-local"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }
        .total-books {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .total-books .book-to-borrow {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .total-books .book-to-borrow label {
            margin-right: 10px;
            font-weight: bold;
        }
        .total-books .book-to-borrow select,
        .total-books .book-to-borrow input[type="number"],
        .total-books .book-to-borrow input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }
        .total-books button {
            padding: 10px 15px;
            width: fit-content;
            background-color: #2195f3cb;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            margin-bottom: 20px;
            cursor: pointer;
        }
        .total-books button:hover {
            background-color: #3a75b0;
        }
        .total-books button.remove-row {
            background-color: #f44336;
            padding: 8px 12px;
            margin-bottom: 0;
        }
        .total-books button.remove-row:hover {
            background-color: #da190b;
        }
        .form-container form input[type="submit"]{
            text-decoration: none;
            color: white;
            background-color: #2195f3cb;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            margin-right: 10px;
            border: none;
        }
        .form-container form input[type="submit"]:hover {
            background-color: #3a75b0;
        }
        .form-container form a.cancel-btn {
            text-decoration: none;
            color: white;
            background-color: #f44336;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }   
        .form-container form a.cancel-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-container">
                <h1>Add Borrow Record</h1>
                <div class="menu">
                    <a href="members.php">Members</a>
                    <a href="books.php">Books</a>
                    <a href="borrows.php">Borrows</a>
                </div>
            </div>
        </div>
        <div class="form-container">
            <form method="POST" action="">
                <h2>Add Borrow Record</h2>
                <?php if ($error): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <div class="member-name-borrow-date">
                    <label for="member_id">Member:</label>
                    <select name="member_id" id="member_id" required>
                        <option value="">Select Member</option>
                        <?php
                            $members_sql = "SELECT member_id, full_name,email FROM members where is_delete = 0 ORDER BY full_name ASC";
                            $members_result = mysqli_query($conn, $members_sql);
                            while ($member = mysqli_fetch_assoc($members_result)) {
                                echo "<option value='" . $member['member_id'] . "'>" . htmlspecialchars($member['full_name']) . " - " . htmlspecialchars($member['email']) . "</option>";
                            }
                        ?>
                    </select>
                    <label for="borrow_date">Borrow Date:</label>
                    <input type="datetime-local" name="borrow_date" id="borrow_date" required>
                </div>
                <div class="total-books">
                    <div class="book-to-borrow" data-row>
                        <label>Book to Borrow:</label>
                        <select name="book_id[]" class="book-select" required>
                            <option value="">Select Book</option>
                            <?php
                                $books_sql = "SELECT book_id, title, price FROM books where is_delete = 0 ORDER BY title ASC";
                                $books_result = mysqli_query($conn, $books_sql);
                                while ($book = mysqli_fetch_assoc($books_result)) {
                                    echo "<option value='" . $book['book_id'] . "' data-price='" . $book['price'] . "'>" . htmlspecialchars($book['title']) . "</option>";
                                }
                            ?>
                        </select>
                        <input type="number" name="quantity[]" class="quantity-input" value="1" min="1" placeholder="Quantity" required>
                        <input type="number" name="unit_price[]" class="unit-price-input" placeholder="Price" step="0.01" required>
                        <input type="number" name="amount[]" class="amount-input" placeholder="Amount" step="0.01" readonly>
                        <button type="button" class="remove-row">Remove</button>
                    </div>
                    <button type="button" class="add-book-btn">Add Book</button>
                </div>
                <input type="submit" value="Add Borrow Record">
                <a href="borrows.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateRowAmount(row) {
                const bookSelect = row.querySelector('.book-select');
                const quantityInput = row.querySelector('.quantity-input');
                const unitPriceInput = row.querySelector('.unit-price-input');
                const amountInput = row.querySelector('.amount-input');
                const selectedOption = bookSelect.options[bookSelect.selectedIndex];
                const price = selectedOption ? selectedOption.getAttribute('data-price') : '';
                if (price) {
                    unitPriceInput.value = price;
                }
                const quantity = parseInt(quantityInput.value || '0', 10);
                const unitPrice = parseFloat(unitPriceInput.value || '0');
                amountInput.value = (quantity * unitPrice).toFixed(2);
            }

            function bindRowEvents(row) {
                const bookSelect = row.querySelector('.book-select');
                const quantityInput = row.querySelector('.quantity-input');
                const unitPriceInput = row.querySelector('.unit-price-input');
                const removeBtn = row.querySelector('.remove-row');
                
                bookSelect.addEventListener('change', () => updateRowAmount(row));
                quantityInput.addEventListener('input', () => updateRowAmount(row));
                unitPriceInput.addEventListener('input', () => updateRowAmount(row));
                removeBtn.addEventListener('click', () => removeBookRow(row));
                
                updateRowAmount(row);
            }

            function addBookToBorrow() {
                const container = document.querySelector('.total-books');
                const firstRow = container.querySelector('[data-row]');
                const newRow = firstRow.cloneNode(true);
                
                // Clear the cloned values
                newRow.querySelector('.book-select').value = '';
                newRow.querySelector('.quantity-input').value = '1';
                newRow.querySelector('.unit-price-input').value = '';
                newRow.querySelector('.amount-input').value = '';
                
                const addButton = container.querySelector('.add-book-btn');
                container.insertBefore(newRow, addButton);
                bindRowEvents(newRow);
            }

            function removeBookRow(row) {
                const container = document.querySelector('.total-books');
                const rows = container.querySelectorAll('[data-row]');
                if (rows.length > 1) {
                    row.remove();
                } else {
                    alert('You must have at least one book to borrow.');
                }
            }

            // Initialize existing rows
            document.querySelectorAll('[data-row]').forEach(bindRowEvents);
            
            // Bind add book button
            document.querySelector('.add-book-btn').addEventListener('click', addBookToBorrow);
        });
    </script>
</body>
</html>