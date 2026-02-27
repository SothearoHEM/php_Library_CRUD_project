<?php
    include 'config.php';
    $error = '';
    if (!isset($_GET['id'])) {
        header("Location: members.php");
        exit();
    }
    $member_id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM members WHERE member_id='$member_id'";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Error retrieving member: " . mysqli_error($conn));
    }
    $member = mysqli_fetch_assoc($result);

    if ($_SERVER['REQUEST_METHOD']==='POST') {
        $member_id = mysqli_real_escape_string($conn, $_GET['id']);
        $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        if (empty($full_name) || empty($gender) || empty($email) || empty($phone) || empty($address)) {
            $error = "All fields are required.";
        } else {
            $sql = "UPDATE members SET full_name='$full_name', gender='$gender', email='$email', phone='$phone', address='$address' WHERE member_id='$member_id'";
            if  (mysqli_query($conn, $sql)) {
                header("Location: members.php");
                exit();
            } else {
                die("Error updating member: " . mysqli_error($conn));
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member - Library Management</title>
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
        <div class="add-members-form">
            <form method="post" action="">
                <h2>Edit Member</h2>
                <?php if ($error): ?>
                    <p class="error-msg"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($member['full_name']); ?>" required><br><br>

                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo ($member['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($member['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                </select><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required><br><br>

                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>" required><br><br>

                <label for="address">Address:</label>
                <textarea id="address" name="address" required><?php echo htmlspecialchars($member['address']); ?></textarea><br><br>

                <input type="submit" value="Update Member">
                <a href="members.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>