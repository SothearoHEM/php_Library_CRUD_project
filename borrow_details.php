<?php
	include 'config.php';

	if (!isset($_GET['id'])) {
		header("Location: borrows.php");
		exit();
	}

	$borrow_id = mysqli_real_escape_string($conn, $_GET['id']);

	$borrow_sql = "SELECT b.borrow_id, b.borrow_date, b.return_date, b.total_books, b.status, m.full_name AS member_name
		FROM borrows b
		JOIN members m ON b.member_id = m.member_id
		WHERE b.borrow_id = '$borrow_id' AND b.is_delete = 0";
	$borrow_result = mysqli_query($conn, $borrow_sql);
	if (!$borrow_result) {
		die("Error retrieving borrow record: " . mysqli_error($conn));
	}
	$borrow = mysqli_fetch_assoc($borrow_result);
	if (!$borrow) {
		header("Location: borrows.php");
		exit();
	}

	$details_sql = "SELECT bd.book_id, bd.qty, bd.unit_price, bd.amount, bk.title
		FROM borrow_details bd
		JOIN books bk ON bd.book_id = bk.book_id
		WHERE bd.borrow_id = '$borrow_id'";
	$details_result = mysqli_query($conn, $details_sql);
	if (!$details_result) {
		die("Error retrieving borrow details: " . mysqli_error($conn));
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Borrow Details</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<div class="container">
		<div class="header">
			<div class="header-container">
				<h1>Borrow Details</h1>
				<div class="menu">
					<a href="members.php">Members</a>
					<a href="books.php">Books</a>
					<a href="borrows.php">Borrows</a>
				</div>
			</div>
		</div>
		<div class="details-container">
			<h2>Borrow #<?php echo htmlspecialchars($borrow['borrow_id']); ?></h2>
			<div class="meta">
				<p><strong>Member:</strong> <?php echo htmlspecialchars($borrow['member_name']); ?></p>
				<p><strong>Borrow Date:</strong> <?php echo htmlspecialchars($borrow['borrow_date']); ?></p>
				<p><strong>Return Date:</strong> <?php echo htmlspecialchars($borrow['return_date']); ?></p>
				<p><strong>Total Books:</strong> <?php echo htmlspecialchars($borrow['total_books']); ?></p>
				<p><strong>Status:</strong> <?php echo htmlspecialchars($borrow['status']); ?></p>
			</div>
			<?php if (mysqli_num_rows($details_result) > 0): ?>
				<table>
					<thead>
						<tr>
							<th>Book</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($detail = mysqli_fetch_assoc($details_result)): ?>
							<tr>
								<td><?php echo htmlspecialchars($detail['title']); ?></td>
							<td><?php echo htmlspecialchars($detail['qty']); ?></td>
								<td><?php echo htmlspecialchars($detail['unit_price']); ?></td>
								<td><?php echo htmlspecialchars($detail['amount']); ?></td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			<?php else: ?>
				<p>No borrow details found.</p>
			<?php endif; ?>
			<div class="actions">
				<a href="borrows.php">Back to Borrows</a>
			</div>
		</div>
	</div>
</body>
</html>
