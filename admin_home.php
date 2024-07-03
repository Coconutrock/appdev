<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['userlevel'] != 'admin') {
    header("Location: /AppDevSA1/nct4/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            background-color: #222;
            color: #ccc;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            background-color: #333;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .header,
        .footer {
            text-align: center;
            margin: 20px 0;
            color: #ff7700;
            /* Orange accent */
        }

        .user-info {
            border: 1px solid #444;
            background-color: #555;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-info h2 {
            margin-top: 0;
            color: #ccc;
        }

        .records {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .records th,
        .records td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }

        .records th {
            background-color: #666;
            color: #ccc;
        }

        .status-active {
            color: #77cc77;
            /* Green */
        }

        .status-disabled {
            color: #cc7777;
            /* Red */
        }

        form {
            margin-top: 20px;
        }

        input[type=file],
        input[type=password],
        input[type=submit] {
            background-color: #444;
            border: 1px solid #666;
            color: #ccc;
            padding: 8px;
            margin-right: 10px;
            border-radius: 4px;
        }

        input[type=submit] {
            background-color: #ff7700;
            /* Orange accent */
            color: #fff;
            cursor: pointer;
        }

        input[type=file]:hover,
        input[type=password]:hover,
        input[type=submit]:hover {
            background-color: #666;
        }

        input[type=file]:focus,
        input[type=password]:focus,
        input[type=submit]:focus {
            outline: none;
            box-shadow: 0 0 5px #ff7700;
            /* Orange accent */
        }

        a {
            color: #ff7700;
            /* Orange accent */
            text-decoration: none;
            margin-left: 10px;
        }

        a:hover {
            color: #ccc;
        }

        .profile-picture img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #ff6600;
            /* Accent color background */
            border: none;
            border-radius: 5px;
            color: #fff;
            /* Light text */
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 0 auto;
            display: block;
            float: right; /* Added float property */
        }

        input[type="submit"]:hover {
            background-color: #cc5500;
            /* Darker accent color on hover */
        }
        
    </style>

</head>

<body>

    <div class="container">
        <div class="header">
            <h1>User Information</h1>
        </div>

        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "technical4";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch latest added user information
        $userQuery = "SELECT * FROM users ORDER BY id DESC LIMIT 1";
        $userResult = $conn->query($userQuery);

        if ($userResult->num_rows > 0) {
            $user = $userResult->fetch_assoc();
        } else {
            $user = null;
        }

        // Fetch all user records (including deleted/disabled users)
        $recordsQuery = "SELECT * FROM users";
        $recordsResult = $conn->query($recordsQuery);

        if (isset($_POST['logout'])) {
            session_destroy();
            header("Location: login.php");
            exit();
        }
        ?>

        <div class="user-info">
            <?php if ($user) : ?>
                <div class="user-details">
                    <p>Welcome <strong><?php echo $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name']; ?></strong></p>
                    <p>Userlevel: <strong><?php echo $user['userlevel']; ?></strong></p>
                    <p>Birthday: <strong><?php echo $user['birthday']; ?></strong></p>
                    <p>Contact Details: <strong>Contact #: <?php echo $user['contact']; ?></strong></p>
                    <p>Email: <strong><?php echo $user['email']; ?></strong></p>
                </div>
                <div class="profile-picture">
                    <p>Profile Picture:</p>
                    <?php if ($user['profile_image']) : ?>
                        <img src="<?php echo $user['profile_image']; ?>" alt="Profile Image">
                    <?php else : ?>
                        No image
                    <?php endif; ?>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                        <input type="file" name="profile_image">
                        <button type="submit">Upload Profile Picture</button>
                    </form>
                </div>
            <?php else : ?>
                <p>No user information found.</p>
            <?php endif; ?>
        </div>

        <?php
        // Check if the form has been submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check if the file has been uploaded
            if (isset($_FILES['profile_image'])) {
                $profileImage = $_FILES['profile_image'];
                $allowedTypes = array('image/jpeg', 'image/png', 'image/gif');
                $maxFileSize = 1024 * 1024; // 1MB

                if ($profileImage['error'] == 0 && in_array($profileImage['type'], $allowedTypes) && $profileImage['size'] <= $maxFileSize) {
                    $uploadDir = 'uploads/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = uniqid() . '_' . $profileImage['name'];
                    $filePath = $uploadDir . $fileName;

                    if (move_uploaded_file($profileImage['tmp_name'], $filePath)) {
                        $updateQuery = "UPDATE users SET profile_image = '$filePath' WHERE id = " . $user['id'];
                        $conn->query($updateQuery);

                        echo "Profile picture updated successfully!";
                    } else {
                        echo "Error uploading file.";
                    }
                } else {
                    echo "Invalid file type or size.";
                }
            }
        }
        ?>

        <div class="records">
            <h2>-Records-</h2>
            <a href="admin_adduser.php">Add New User</a>
            <table class="records">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Contact No.</th>
                    <th>Email</th>
                    <th>Birthday</th>
                    <th>Username</th>
                    <th>Access Level</th>
                    <th>Status</th>
                    <th>Profile Image</th>
                </tr>
                <?php
                if ($recordsResult->num_rows > 0) {
                    while ($record = $recordsResult->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $record['id'] . '</td>';
                        echo '<td>' . $record['first_name'] . '</td>';
                        echo '<td>' . $record['middle_name'] . '</td>';
                        echo '<td>' . $record['last_name'] . '</td>';
                        echo '<td>' . $record['contact'] . '</td>';
                        echo '<td>' . $record['email'] . '</td>';
                        echo '<td>' . $record['birthday'] . '</td>';
                        echo '<td>' . $record['username'] . '</td>';
                        echo '<td>' . $record['userlevel'] . '</td>';
                        echo '<td class="status-' . $record['status'] . '">' . ucfirst($record['status']) . '</td>';
                        if ($record['profile_image']) {
                            echo '<td><img src="' . $record['profile_image'] . '" alt="Profile Image" style="width:50px;height:50px;"></td>';
                        } else {
                            echo '<td>No image</td>';
                        }
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="11">No records found</td></tr>';
                }
                ?>
            </table>
        </div>

    </div>

    <h1></h1>
    <div class="container">
        <h1>Change Password</h1>
        <form action="login.php" method="POST">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_new_password">Confirm New Password:</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" required>


            <input type="submit" class="change_pass" value="Change Password">

        </form>
    </div>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "technical4";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmNewPassword = $_POST['confirm_new_password'] ?? '';

        // Retrieve user ID from session
        $userId = $_SESSION['user_id'];

        // Fetch the hashed password from the database for the current user
        $stmt = $conn->prepare("SELECT hashed_password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashedPasswordFromDB = $row['hashed_password'];

            // Verify current password
            if (password_verify($currentPassword, $hashedPasswordFromDB)) {
                // Check if new passwords match
                if ($newPassword === $confirmNewPassword) {
                    // Hash the new password
                    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update password in the database
                    $updateStmt = $conn->prepare("UPDATE users SET hashed_password = ? WHERE id = ?");
                    $updateStmt->bind_param("si", $newHashedPassword, $userId);
                    if ($updateStmt->execute()) {
                        echo "<p>Password updated successfully!</p>";
                    } else {
                        echo "<p>Error updating password: " . htmlspecialchars($conn->error) . "</p>";
                    }
                    $updateStmt->close();
                } else {
                    echo "<p>New passwords do not match.</p>";
                }
            } else {
                echo "<p>Current password is incorrect.</p>";
            }
        } 

        $stmt->close();
    }
    $conn->close();
    ?>

    <form method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

</body>

</html>