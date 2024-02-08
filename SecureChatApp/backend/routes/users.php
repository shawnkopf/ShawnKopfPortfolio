<?php
// users.php for user management

// Include the necessary files and initialize the database connection
require_once '../db.php'; // Adjust the path as needed
require_once '../models/User.php';

// Initialize the database connection
try {
    $database = new PDO('sqlite:../db/securechat.db');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle database connection error
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

// Create an instance of the User class
$userModel = new User($database);

// Check the type of request (e.g., POST for registration, GET for key retrieval)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle user registration
    if (isset($_POST['username'], $_POST['publicKey'])) {
        $username = $_POST['username'];
        $publicKey = $_POST['publicKey'];

        // Check if the username already exists
        $existingUser = $userModel->getUserByUsername($username);

        if ($existingUser) {
            // Handle username already exists
            echo "Username already exists.";
        } else {
            // Register the new user
            $userModel->createUser($username, $publicKey);
            echo "Registration successful.";
        }
    } else {
        // Handle incomplete POST data
        echo "Incomplete data received.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle key retrieval for a user
    if (isset($_GET['username'])) {
        $username = $_GET['username'];

        // Check if the user exists in the database
        $existingUser = $userModel->getUserByUsername($username);

        if (!$existingUser) {
            // Handle invalid username
            echo "User not found.";
        } else {
            // Return the user's public key (you should implement RSA key retrieval here)
            echo $existingUser['publicKey']; // Placeholder for key retrieval logic
        }
    } else {
        // Handle incomplete GET data
        echo "Incomplete data received.";
    }
} else {
    // Handle unsupported HTTP methods
    echo "Unsupported HTTP method.";
}
?>
