<?php
	include 'config.php';

	if (!isset($_GET['id'])) {
		header("Location: borrows.php");
		exit();
	}

	$borrow_id = $_GET['id'];

	$borrow_sql = "SELECT b.borrow_id, b.borrow_date, b.return_date, b.total_books, b.status, m.full_name AS member_name
		FROM borrows b
		JOIN members m ON b.member_id = m.member_id
		WHERE b.borrow_id = $borrow_id AND b.is_delete = 0";
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
		WHERE bd.borrow_id = $borrow_id";
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
		.details-container {
			width: 80%;
			margin: 30px auto;
			background-color: #ffffff;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 0 10px rgba(0,0,0,0.1);
		}
		.details-container h2 {
			margin-bottom: 15px;
			color: #333;
		}
		.details-container .meta {
			margin-bottom: 20px;
			color: #555;
		}
		.details-container table {
			width: 100%;
			border-collapse: collapse;
			background: white;
			border-radius: 8px;
			overflow: hidden;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		}
		.details-container th,
		.details-container td {
			padding: 12px 15px;
			text-align: left;
			border-bottom: 1px solid #ddd;
		}
		.details-container th {
			background-color: #4CAF50;
			color: white;
		}
		.details-container tr:hover {
			background-color: #f1f1f1;
		}
		.details-container .actions {
			margin-top: 20px;
		}
		.details-container .actions a {
			text-decoration: none;
			color: white;
			background-color: #2195f3cb;
			padding: 10px 15px;
			border-radius: 5px;
			font-weight: bold;
		}
		.details-container .actions a:hover {
			background-color: #3a75b0;
		}
	</style>
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
