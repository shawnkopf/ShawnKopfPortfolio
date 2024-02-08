// src/screens/HomeScreen.tsx
import React from "react";
import { View, Text, Button, StyleSheet } from "react-native";

const HomeScreen = ({ navigation }: { navigation: any }) => {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Fitness Tracker</Text>
      <Button
        title="Log Workout"
        onPress={() => navigation.navigate("WorkoutTracker")}
      />
      <Button
        title="View Statistics"
        onPress={() => navigation.navigate("StatisticsViewer")}
      />
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

export default HomeScreen;
