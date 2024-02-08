<?php
// encryption.php for encryption-related functions

class Encryption {
    // AES encryption key (must be the same for both sender and receiver)
    private $encryptionKey;

    public function __construct($key) {
        $this->encryptionKey = $key;
    }

    // Function to encrypt a message
    public function encryptMessage($message) {
        // Generate a random initialization vector (IV)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        // Encrypt the message using AES encryption
        $encryptedMessage = openssl_encrypt($message, 'aes-256-cbc', $this->encryptionKey, 0, $iv);

        // Combine the IV and encrypted message for storage
        $encryptedData = base64_encode($iv . $encryptedMessage);

        return $encryptedData;
    }

    // Function to decrypt a message
    public function decryptMessage($encryptedData) {
        // Decode the base64 encoded data
        $data = base64_decode($encryptedData);

        // Extract the IV and encrypted message
        $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedMessage = substr($data, openssl_cipher_iv_length('aes-256-cbc'));

        // Decrypt the message using AES decryption
        $decryptedMessage = openssl_decrypt($encryptedMessage, 'aes-256-cbc', $this->encryptionKey, 0, $iv);

        return $decryptedMessage;
    }
}

// Usage:
// $encryptionKey should be the same for both sender and receiver
// $encryption = new Encryption($encryptionKey);
// $encryptedMessage = $encryption->encryptMessage("Hello, this is a secure message.");
// $decryptedMessage = $encryption->decryptMessage($encryptedMessage);
?>
