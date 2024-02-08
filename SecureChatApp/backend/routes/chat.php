<?php
// chat.php for handling chat messages

// Include the necessary files and initialize the database connection
require_once '../db.php'; // Adjust the path as needed
require_once '../models/User.php';
require_once '../models/Message.php';

// Initialize the database connection
try {
    $database = new PDO('sqlite:../db/securechat.db');
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle database connection error
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

// Create instances of the User and Message classes
$userModel = new User($database);
$messageModel = new Message($database);

// Check the type of request (e.g., POST for sending messages, GET for retrieving messages)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle sending a new message
    if (isset($_POST['senderUsername'], $_POST['receiverUsername'], $_POST['messageContent'])) {
        $senderUsername = $_POST['senderUsername'];
        $receiverUsername = $_POST['receiverUsername'];
        $messageContent = $_POST['messageContent'];

        // Check if the sender and receiver usernames exist in the database
        $senderUser = $userModel->getUserByUsername($senderUsername);
        $receiverUser = $userModel->getUserByUsername($receiverUsername);

        if (!$senderUser || !$receiverUser) {
            // Handle invalid usernames
            echo "Invalid sender or receiver.";
        } else {
            // Encrypt the message content (you should implement AES encryption here)
            $encryptedMessage = $messageContent; // Placeholder for encryption logic

            // Store the encrypted message in the database
            $messageModel->createMessage($senderUsername, $receiverUsername, $encryptedMessage);
            echo "Message sent successfully.";
        }
    } else {
        // Handle incomplete POST data
        echo "Incomplete data received.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle retrieving messages between two users
    if (isset($_GET['user1'], $_GET['user2'])) {
        $user1 = $_GET['user1'];
        $user2 = $_GET['user2'];

        // Check if the users exist in the database
        $existingUser1 = $userModel->getUserByUsername($user1);
        $existingUser2 = $userModel->getUserByUsername($user2);

        if (!$existingUser1 || !$existingUser2) {
            // Handle invalid usernames
            echo "Invalid sender or receiver.";
        } else {
            // Retrieve messages between the two users
            $messages = $messageModel->getMessagesBetweenUsers($user1, $user2);

            // Send the messages as JSON (you should decrypt them here)
            echo json_encode($messages);
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
