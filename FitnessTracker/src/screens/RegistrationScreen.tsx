import React, { useState } from "react";
import { View, Text, TextInput, Button } from "react-native";
import { styles } from "../styles/RegistrationScreenStyles"; // Import styles
import { registerUser } from "../services/AuthService";

const RegistrationScreen = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const handleRegister = async () => {
    try {
      const response = await registerUser(email, password);
      // Handle successful registration, e.g., navigate to another screen
      console.log("Registration Successful:", response);
    } catch (error) {
      // Handle registration error
      console.error("Registration failed", error);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Registration</Text>
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
      <Button title="Register" onPress={handleRegister} />
    </View>
  );
};

export default RegistrationScreen;
