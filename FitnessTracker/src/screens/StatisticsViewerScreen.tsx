// src/screens/StatisticsViewerScreen.tsx
import React from "react";
import { View, Text, StyleSheet } from "react-native";
import StatisticsViewer from "../components/StatisticsViewer"; // Import the StatisticsViewer component

const StatisticsViewerScreen = () => {
  return (
    <View style={styles.container}>
      <Text style={styles.title}>Statistics Viewer</Text>
      <StatisticsViewer /> {/* Render the StatisticsViewer component */}
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

export default StatisticsViewerScreen;
