// src/screens/GoalSetterScreen.tsx
import React, { useState } from "react";
import { View, Text, TextInput, Button, StyleSheet } from "react-native";

const GoalSetterScreen = () => {
  const [goal, setGoal] = useState("");
  const [goalDescription, setGoalDescription] = useState("");

  const handleGoalSetting = () => {
    // Implement logic to save the goal to your database or state management
    // You can use Axios or your preferred data management approach

    // Example: Save the goal and description to state
    const newGoal = {
      title: goal,
      description: goalDescription,
    };

    // Display a success message or navigate to another screen
    console.log("Goal set:", newGoal);
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Set Your Fitness Goal</Text>
      <TextInput
        placeholder="Goal Title"
        value={goal}
        onChangeText={(text) => setGoal(text)}
        style={styles.input}
      />
      <TextInput
        placeholder="Goal Description"
        value={goalDescription}
        onChangeText={(text) => setGoalDescription(text)}
        style={styles.input}
        multiline
      />
      <Button title="Set Goal" onPress={handleGoalSetting} />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  title: {
    fontSize: 24,
    marginBottom: 20,
  },
  input: {
    width: "80%",
    height: 40,
    borderColor: "gray",
    borderWidth: 1,
    marginBottom: 20,
    paddingHorizontal: 10,
  },
});

export default GoalSetterScreen;
