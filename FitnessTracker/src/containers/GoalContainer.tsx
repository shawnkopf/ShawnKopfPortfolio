import React from "react";
import { View, Text, Button, StyleSheet } from "react-native";

interface GoalContainerProps {
  goal: string;
  onDelete: () => void;
}

const GoalContainer: React.FC<GoalContainerProps> = ({ goal, onDelete }) => {
  return (
    <View style={styles.container}>
      <Text style={styles.goalText}>{goal}</Text>
      <Button title="Delete" onPress={onDelete} color="red" />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginVertical: 8,
  },
  goalText: {
    fontSize: 16,
  },
});

export default GoalContainer;
