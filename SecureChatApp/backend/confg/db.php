<?php
// db.php for SQLite
try {
    // Create (or open if it already exists) an SQLite database file
    $database = new PDO('sqlite:../db/securechat.db');
    // Set error mode to throw exceptions
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Include the table creation queries from User.php
    require_once 'models/User.php';
    createUsersTable($database);

    // Include the table creation queries from Message.php
    require_once 'models/Message.php';
    createMessagesTable($database);
} catch (PDOException $e) {
    // Handle error
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
<?php
// db.php for SQLite
try {
    // Create (or open if it already exists) an SQLite database file
    $database = new PDO('sqlite:../db/securechat.db');
    // Set error mode to throw exceptions
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create tables if they don't exist
    $query = "CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(255) NOT NULL UNIQUE,
        publicKey TEXT NOT NULL
    )";
    $database->exec($query);
} catch (PDOException $e) {
    // Handle error
    echo "Database connection failed: " . $e->getMessage();
    exit;
}

CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    senderUsername VARCHAR(255) NOT NULL,
    receiverUsername VARCHAR(255) NOT NULL,
    messageContent TEXT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
);


?>
