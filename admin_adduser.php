<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <style>
        /* Resetting default margin and padding for body */
        body,
        html {

            font-family: 'Arial', sans-serif;
            background-color: #232323;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            width: 80%;
            /* Increased width to 80% */
            margin-top: 20px;
            padding: 40px;
            /* Increased padding for more space */
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            background-color: #1a1a1a;
            text-align: center;
        }

        h1 {
            color: #ff6600;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label,
        input {
            margin: 10px 0;
            color: #fff;
        }

        input[type="text"],
        input[type="password"],
        input[type="date"],
        input[type="email"],
        input[type="file"] {
            padding: 8px;
            width: 100%;
            border: none;
            border-radius: 5px;
            background-color: #2d2d2d;
            color: #fff;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.5);
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

        .back-link {
            margin-top: 10px;
            text-align: center;
        }

        .back-link a {
            color: #ff6600;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Add User</h1>
        <form action="register_user.php" method="POST" enctype="multipart/form-data">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>

            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name">

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="contact">Contact Number:</label>
            <input type="text" id="contact" name="contact" required>

            <label for="userlevel">User Level:</label>
            <select id="userlevel" name="userlevel" required>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>
            <h1></h1>

            <label for="profile_image">Profile Image:</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*">

            <input type="submit" value="Submit">
        </form>
        <div class="back-link">
            <a href="admin_home.php">Back</a>
        </div>
    </div>
</body>

</html>