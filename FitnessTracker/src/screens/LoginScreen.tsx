// src/screens/LoginScreen.tsx
import React from "react";
import { View } from "react-native";
import { styles } from "../styles/LoginScreenStyles"; // Import styles
import { loginUser } from "../services/AuthService";
import LoginScreenComponent from "../components/LoginComponent";

const LoginScreen = () => {
  const handleLogin = async (email: string, password: string) => {
    try {
      const response = await loginUser(email, password);
      // Handle successful login, e.g., navigate to another screen
      console.log("Login Successful:", response);
    } catch (error) {
      // Handle login error
      console.error("Login failed", error);
    }
  };

  return (
    <View style={styles.container}>
      <LoginScreenComponent onLogin={handleLogin} />
    </View>
  );
};

export default LoginScreen;
