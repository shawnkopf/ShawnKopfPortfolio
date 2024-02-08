// src/components/StatisticsViewer.tsx
import React from "react";
import { View, Text, StyleSheet } from "react-native";

const StatisticsViewer: React.FC = () => {
  // Implement your statistics logic here

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Statistics Viewer</Text>
      {/* Display your statistics here */}
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

export default StatisticsViewer;
