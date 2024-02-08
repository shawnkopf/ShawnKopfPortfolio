<?php

class Message {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Function to store a new message
    public function createMessage($senderUsername, $receiverUsername, $encryptedMessage) {
        $stmt = $this->db->prepare('INSERT INTO messages (senderUsername, receiverUsername, messageContent) VALUES (:senderUsername, :receiverUsername, :messageContent)');
        $stmt->bindValue(':senderUsername', $senderUsername, PDO::PARAM_STR);
        $stmt->bindValue(':receiverUsername', $receiverUsername, PDO::PARAM_STR);
        $stmt->bindValue(':messageContent', $encryptedMessage, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Function to retrieve messages between two users
    public function getMessagesBetweenUsers($user1, $user2) {
        $stmt = $this->db->prepare('SELECT * FROM messages WHERE (senderUsername = :user1 AND receiverUsername = :user2) OR (senderUsername = :user2 AND receiverUsername = :user1) ORDER BY createdAt ASC');
        $stmt->bindValue(':user1', $user1, PDO::PARAM_STR);
        $stmt->bindValue(':user2', $user2, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
