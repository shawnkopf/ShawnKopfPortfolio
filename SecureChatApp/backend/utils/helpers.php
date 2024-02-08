<?php
// helpers.php for other helper functions

// Function to sanitize user input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to display an error message
function showError($errorMessage) {
    echo "<div class='error'>$errorMessage</div>";
}

// Function to redirect to a different page
function redirectTo($page) {
    header("Location: $page");
    exit();
}

// Function to generate a random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Function to check if a user is logged in (you can customize this)
function isLoggedIn() {
    // Check your authentication logic here and return true if logged in, false otherwise
    return false;
}

// Function to log out the user (you can customize this)
function logout() {
    // Implement your logout logic here
    // Destroy session, clear cookies, etc.
}

// Function to display a success message
function showSuccess($message) {
    echo "<div class='success'>$message</div>";
}

// ... Other helper functions ...

?>
