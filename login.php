<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "technical4";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    if (!$stmt) {
        die("Statement preparation failed: " . htmlspecialchars($conn->error));
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password_from_db = $row['hashed_password'];

        if (password_verify($password, $hashed_password_from_db)) {
            $role = $row['userlevel'];
            $user_id = $row['id'];

            // Set session variables based on user role
            $_SESSION['user_id'] = $user_id;
            $_SESSION['userlevel'] = $role;

            // Redirect to appropriate page based on user role
            if ($role === 'admin') {
                header('Location: /AppDevSA1/nct4/admin_home.php');
                exit;
            } elseif ($role === 'user') {
                header('Location: /AppDevSA1/nct4/profile.php');
                exit;
            } else {
                $error = "Unknown role.";
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that username.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #232323;
            color: #fff;
        }

        .container {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            background-color: #1a1a1a;
        }

        h1 {
            text-align: center;
            color: #ff6600;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label,
        input {
            margin-bottom: 10px;
            color: #fff;
        }

        input[type="submit"] {
            align-self: center;
            padding: 10px 20px;
            background-color: #ff6600;
            border: none;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #cc5500;
        }

        .error {
            color: red;
            text-align: center;
        }

        .username_input {
            color: #1a1a1a
        }

        .password_input {
            color: #1a1a1a;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <?php if ($error) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="username_input" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="password_input" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>

</html>