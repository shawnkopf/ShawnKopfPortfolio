<?php

// db.php (Ensure this path is correct)
require_once 'path/to/db.php'; // Adjust this path as needed

// User.php (Ensure this path is correct)
require_once 'models/User.php';

// Assuming $db is your database connection from db.php
$userModel = new User($db);

// To create a new user
$createResult = $userModel->createUser("username", "userPublicKey");

if ($createResult) {
    echo "User created successfully.\n";
} else {
    echo "Failed to create user.\n";
}

// To fetch a user by username
$user = $userModel->getUserByUsername("username");

if ($user) {
    echo "User found: " . print_r($user, true);
} else {
    echo "User not found.\n";
}

?>
<?php
// SQLite version of User.php
require_once 'path/to/db.php'; // Adjust the path as needed
require_once 'models/User.php';

// Assuming $db is your database connection from db.php
$userModel = new User($db);

// To create a new user
$userModel->createUser("username", "userPublicKey");

// To fetch a user by username
$user = $userModel->getUserByUsername("username");


class User {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function createUser($username, $publicKey) {
        $stmt = $this->db->prepare('INSERT INTO users (username, publicKey) VALUES (:username, :publicKey)');
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':publicKey', $publicKey, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getUserByUsername($username) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

// Function to create the users table
    public static function createUsersTable($db) {
        $query = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(255) NOT NULL UNIQUE,
            publicKey TEXT NOT NULL
        )";
        $db->exec($query);
    }
       // Function to create the messages table
    public static function createMessagesTable($db) {
        $query = "CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            senderUsername VARCHAR(255) NOT NULL,
            receiverUsername VARCHAR(255) NOT NULL,
            messageContent TEXT NOT NULL,
            createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($query);
    }
}
