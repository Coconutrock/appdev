<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "technical4";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    die("User not found.");
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Password reset functionality
if (isset($_POST['reset_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        // Check if new password matches confirm password
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update password in the database
            $update_sql = "UPDATE users SET password='$hashed_password' WHERE id='$user_id'";
            if ($conn->query($update_sql) === TRUE) {
                echo "Password updated successfully.";
            } else {
                echo "Error updating password: " . $conn->error;
            }
        } else {
            echo "New password and confirm password do not match.";
        }
    } else {
        echo "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #232323;
            /* Dark background */
            margin: 0;
            padding: 0;
            color: #fff;
            /* Light text */
        }

        .container {
            width: 70%;
            margin: 0 auto;
            background-color: #1a1a1a;
            /* Darker container background */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            margin-top: 20px;
            display: flex;
            /* Use flexbox for layout */
            flex-direction: column;
            /* Stack elements vertically */
            align-items: center;
            /* Center items horizontally */
        }

        .header,
        .footer {
            text-align: center;
            margin: 20px 0;
        }

        .user-info {
            border: 1px solid #444;
            /* Darker border */
            padding: 20px;
            margin-bottom: 20px;
            width: 100%;
            /* Occupy full width */
            display: flex;
            /* Use flexbox for inner layout */
            justify-content: space-between;
            /* Distribute items evenly */
            align-items: center;
            /* Center items vertically */
        }

        .user-info h2 {
            margin-top: 0;
            color: #ff6600;
            /* Accent color */
        }

        .user-info p {
            margin: 10px 0;
            color: #ccc;
            /* Lighter text */
        }

        .user-info img {
            border-radius: 50%;
            border: 4px solid #ff6600;
            /* Accent color border */
            width: 150px;
            height: 150px;
        }

        form {
            text-align: center;
            margin-top: 20px;
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
        }

        input[type="submit"]:hover {
            background-color: #cc5500;
            /* Darker accent color on hover */
        }

        .footer {
            background-color: #1a1a1a;
            /* Dark footer background */
            color: #ccc;
            /* Light footer text */
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .footer p {
            margin: 0;
            text-align: center;
        }

        .password-reset {
            background-color: #2a2a2a;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .password-reset input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #1a1a1a;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>User Profile</h1>
        </div>

        <div class="user-info">
            <div class="user-details">
                <h2><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></h2>
                <p>Username: <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
                <p>Email: <strong><?php echo htmlspecialchars($user['email']); ?></strong></p>
                <p>Contact: <strong><?php echo htmlspecialchars($user['contact']); ?></strong></p>
                <p>Birthday: <strong><?php echo htmlspecialchars($user['birthday']); ?></strong></p>
                <p>Userlevel: <strong><?php echo htmlspecialchars($user['userlevel']); ?></strong></p>
            </div>
            <div class="profile-pic">
                <?php if ($user['profile_image']) : ?>
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image">
                <?php endif; ?>
            </div>
        </div>

        <div class="password-reset">
            <h2>Reset Password</h2>
            <form method="post">
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <input type="submit" name="reset_password" value="Reset Password">
            </form>
        </div>

        <form method="post">
            <input type="submit" name="logout" value="Logout">
        </form>


    </div>
</body>

</html>

<?php
$conn->close();
?>