<?php
// Database connection settings
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "customer_messages";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$message = trim($_POST['message']);

// Validate input
if ($name === "" || $email === "" || $message === "") {
    die("Please fill all fields.");
}

// Prepare SQL statement
$stmt = $conn->prepare("INSERT INTO customer_messages (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
    // Redirect back with success message
    header("Location: index.php?success=1");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
