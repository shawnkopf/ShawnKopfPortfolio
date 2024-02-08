// encryption.js for encryption and decryption logic

// Function to generate an AES encryption key
async function generateEncryptionKey() {
    const key = await window.crypto.subtle.generateKey(
        {
            name: "AES-GCM",
            length: 256,
        },
        true,
        ["encrypt", "decrypt"]
    );
    return key;
}

// Function to encrypt a message
async function encryptMessage(message, encryptionKey) {
    const encoder = new TextEncoder();
    const data = encoder.encode(message);

    const iv = window.crypto.getRandomValues(new Uint8Array(12));
    const encryptedData = await window.crypto.subtle.encrypt(
        {
            name: "AES-GCM",
            iv: iv,
        },
        encryptionKey,
        data
    );

    return { iv: iv, encryptedData: encryptedData };
}

// Function to decrypt a message
async function decryptMessage(encryptedMessage, encryptionKey) {
    const iv = encryptedMessage.iv;
    const encryptedData = encryptedMessage.encryptedData;

    const decryptedData = await window.crypto.subtle.decrypt(
        {
            name: "AES-GCM",
            iv: iv,
        },
        encryptionKey,
        encryptedData
    );

    const decoder = new TextDecoder();
    const decryptedMessage = decoder.decode(decryptedData);

    return decryptedMessage;
}

// Usage example:
// const encryptionKey = await generateEncryptionKey();
// const encryptedMessage = await encryptMessage("Hello, this is a secure message.", encryptionKey);
// const decryptedMessage = await decryptMessage(encryptedMessage, encryptionKey);
