// src/screens/PasswordResetScreen.tsx
import React from "react";
import { View } from "react-native";
import axios from "axios"; // Import Axios
import PasswordResetComponent from "../components/PasswordResetComponent";
import styles from "../styles/PasswordResetScreenStyles"; // Import styles

const PasswordResetScreen = () => {
  const handlePasswordReset = async (email: string) => {
    try {
      // Implement logic to send a password reset email using email
      // You can use Axios or your authentication service for this purpose

      // Example: Sending a password reset request using Axios (replace with actual logic)
      const response = await axios.post("your-reset-api-endpoint", { email });

      // Check the response status or data to handle success or failure
      if (response.status === 200) {
        console.log("Password reset email sent to:", email);
        // Navigate to a success screen or show a success message
      } else {
        console.error("Password reset failed");
        // Handle the failure scenario, display an error message, etc.
      }
    } catch (error) {
      // Handle any network or other errors
      console.error("Password reset failed", error);
      // You can show an error message to the user if needed
    }
  };

  return (
    <View style={styles.container}>
      <PasswordResetComponent onPasswordReset={handlePasswordReset} />
    </View>
  );
};

export default PasswordResetScreen;
