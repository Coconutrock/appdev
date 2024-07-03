<?php
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

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $userlevel = $_POST['userlevel'];

    // Check if passwords match
    if ($password != $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle file upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check file size (optional)
        if ($_FILES["profile_image"]["size"] > 500000) {
            die("Sorry, your file is too large.");
        }

        // Allow certain file formats (optional)
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowed_types)) {
            die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }

        if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            die("Sorry, there was an error uploading your file.");
        }
    } else {
        $target_file = null; // No file uploaded
    }

    // Insert data into the users table
    $user_sql = "INSERT INTO users (first_name, middle_name, last_name, username, hashed_password, birthday, email, contact, userlevel, profile_image, status)
                 VALUES ('$first_name', '$middle_name', '$last_name', '$username', '$hashed_password', '$birthday', '$email', '$contact', '$userlevel', '$target_file', 'active')";

    if ($conn->query($user_sql) === TRUE) {
        // Insert data into the records table
        $record_sql = "INSERT INTO records (first_name, middle_name, last_name, username, birthday, email, contact)
                       VALUES ('$first_name', '$middle_name', '$last_name', '$username', '$birthday', '$email', '$contact')";

        if ($conn->query($record_sql) === TRUE) {
            // Redirect to index.php after successful registration
            header("Location: admin_home.php");
            exit; // Ensure that no other content is sent
        } else {
            echo "Error inserting into records table: " . $conn->error;
        }
    } else {
        echo "Error inserting into users table: " . $conn->error;
    }

    $conn->close();
}
