// src/screens/WorkoutTrackerScreen.tsx
import React from "react";
import { View, Text, Button, StyleSheet } from "react-native";
import WorkoutTracker from "../components/WorkoutTracker"; // Import the WorkoutTracker component

const WorkoutTrackerScreen = () => {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Log Workout</Text>
      <WorkoutTracker /> {/* Render the WorkoutTracker component */}
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
});

export default WorkoutTrackerScreen;
