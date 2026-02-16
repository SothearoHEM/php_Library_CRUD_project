<?php
    include 'config.php';
    $error = '';
    if (!isset($_GET['id'])) {
        header("Location: members.php");
        exit();
    }
    $member_id = $_GET['id'];
    $sql = "SELECT * FROM members WHERE member_id=$member_id";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Error retrieving member: " . mysqli_error($conn));
    }
    $member = mysqli_fetch_assoc($result);

    if ($_SERVER['REQUEST_METHOD']==='POST') {
        $member_id = $_GET['id'];
        $full_name = $_POST['full_name'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        if (empty($full_name) || empty($gender) || empty($email) || empty($phone) || empty($address)) {
            $error = "All fields are required.";
        } else {
            $sql = "UPDATE members SET full_name='$full_name', gender='$gender', email='$email', phone='$phone', address='$address' WHERE member_id=$member_id";
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
        .add-members-form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            margin-top: 30px;
            align-items: center;
        }
        .add-members-form form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        .add-members-form form h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .add-members-form form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .add-members-form form input[type="text"],
        .add-members-form form input[type="email"],
        .add-members-form form select,
        .add-members-form form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .add-members-form form input[type="submit"],
        .add-members-form form a {
            text-decoration: none;
            color: white;
            background-color: #2195f3cb;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            margin-right: 10px;
            border: none;
        }
        .add-members-form form a.cancel-btn {
            background-color: #f44336;
        }
        .add-members-form form a.cancel-btn:hover {
            background-color: #d32f2f;
        }
        .add-members-form form input[type="submit"]:hover {
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
        <div class="add-members-form">
            <form method="post" action="">
                <h2>Edit Member</h2>
                <?php if ($error): ?>
                    <p style="color: red;"><?php echo $error; ?></p>
                <?php endif; ?>
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo ($member['full_name']); ?>" required><br><br>

                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo ($member['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($member['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
                </select><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo ($member['email']); ?>" required><br><br>

                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo ($member['phone']); ?>" required><br><br>

                <label for="address">Address:</label>
                <textarea id="address" name="address" required><?php echo ($member['address']); ?></textarea><br><br>

                <input type="submit" value="Update Member">
                <a href="members.php" class="cancel-btn">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>