// chat.js for handling chat functionality

// Function to send a chat message
function sendMessage() {
    // Get message content from the input field
    var messageContent = document.getElementById("messageInput").value;

    // Validate message content (add your own validation logic here)

    // AJAX request to send the message to the server
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "path/to/chat.php", true); // Adjust the path to your chat.php file
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Message sent successfully, update the chat interface or display a success message
            document.getElementById("messageInput").value = "";
        } else if (xhr.readyState === 4 && xhr.status !== 200) {
            // Handle error, display an error message, or retry sending
            console.error("Failed to send message.");
        }
    };
    xhr.send("senderUsername=senderUser&receiverUsername=receiverUser&messageContent=" + encodeURIComponent(messageContent));
}

// Function to retrieve chat messages (polling or use WebSockets for real-time updates)
function retrieveMessages() {
    // AJAX request to retrieve messages from the server
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "path/to/chat.php?user1=senderUser&user2=receiverUser", true); // Adjust the path and parameters
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Parse and display the retrieved messages in the chat interface
            var messages = JSON.parse(xhr.responseText);
            // Update your chat interface with the messages
        } else if (xhr.readyState === 4 && xhr.status !== 200) {
            // Handle error, display an error message, or retry retrieving
            console.error("Failed to retrieve messages.");
        }
    };
    xhr.send();
}

// Function to periodically retrieve messages (polling)
setInterval(retrieveMessages, 5000); // Adjust the interval as needed

// Add event listener to send button (or form submission)
document.getElementById("sendButton").addEventListener("click", function (e) {
    e.preventDefault();
    sendMessage();
});
