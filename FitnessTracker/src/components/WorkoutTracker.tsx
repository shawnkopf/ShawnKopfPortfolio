import React, { useState } from "react";
import { View, Text, Button } from "react-native";
import { styles } from "../styles/WorkoutTrackerStyles"; // Import styles

const WorkoutTracker: React.FC = () => {
  const [workoutCount, setWorkoutCount] = useState(0);

  const handleAddWorkout = () => {
    setWorkoutCount(workoutCount + 1);
  };

  return (
    <View style={styles.container}>
      {" "}
      {/* Use styles */}
      <Text style={styles.title}>Workout Tracker</Text>
      <Text>Total Workouts: {workoutCount}</Text>
      <Button title="Add Workout" onPress={handleAddWorkout} />
    </View>
  );
};

export default WorkoutTracker;
