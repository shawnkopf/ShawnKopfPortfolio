// src/components/LoginScreenComponent.tsx
import React, { useState } from "react";
import { Text, TextInput, Button } from "react-native";
import { styles } from "../styles/LoginScreenStyles"; // Import styles

interface LoginComponentProps {
  onLogin: (email: string, password: string) => void;
}

// isValidEmail function definition
const isValidEmail = (email: string): boolean => {
  // Regular expression pattern for basic email validation
  const emailPattern = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;
  return emailPattern.test(email);
};

const LoginComponent: React.FC<LoginComponentProps> = ({ onLogin }) => {
  const [email, setEmail] = useState<string>("");
  const [password, setPassword] = useState<string>("");

  const handleLogin = () => {
    if (!email || !password) {
      // Check if email or password is empty
      console.error("Email and password are required.");
      return; // Exit the function to prevent further execution
    }

    // You can add more validation rules here if needed
    if (!isValidEmail(email)) {
      console.error("Invalid email address.");
      return;
    }
    onLogin(email, password);
  };

  return (
    <>
      <Text style={styles.title}>Login</Text>
      <TextInput
        placeholder="Email"
        value={email}
        onChangeText={(text) => setEmail(text)}
        style={styles.input}
      />
      <TextInput
        placeholder="Password"
        value={password}
        onChangeText={(text) => setPassword(text)}
        secureTextEntry
        style={styles.input}
      />
      <Button title="Login" onPress={handleLogin} />
    </>
  );
};

export default LoginComponent;
