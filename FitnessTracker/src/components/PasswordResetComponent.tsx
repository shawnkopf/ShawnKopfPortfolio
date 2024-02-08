// src/components/PasswordResetComponent.tsx
import React, { useState } from "react";
import { Text, TextInput, Button } from "react-native";
import { styles } from "../styles/PasswordResetComponentStyles"; // Import styles

interface PasswordResetComponentProps {
  onPasswordReset: (email: string) => void;
}

// isValidEmail function definition
const isValidEmail = (email: string): boolean => {
  // Regular expression pattern for basic email validation
  const emailPattern = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
  return emailPattern.test(email);
};

const PasswordResetComponent: React.FC<PasswordResetComponentProps> = ({
  onPasswordReset,
}) => {
  const [email, setEmail] = useState<string>("");

  const handlePasswordReset = () => {
    if (!email) {
      // Check if email is empty
      console.error("Email is required.");
      return; // Exit the function to prevent further execution
    }

    // Check if email is valid using the isValidEmail function
    if (!isValidEmail(email)) {
      console.error("Invalid email address.");
      return;
    }

    onPasswordReset(email);
  };

  return (
    <>
      <Text style={styles.title}>Password Reset</Text>
      <TextInput
        placeholder="Email"
        value={email}
        onChangeText={(text) => setEmail(text)}
        style={styles.input}
      />
      <Button title="Reset Password" onPress={handlePasswordReset} />
    </>
  );
};

export default PasswordResetComponent;
