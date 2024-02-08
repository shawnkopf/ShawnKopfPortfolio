import React, { useState, useEffect } from "react";
import { View, Text, TextInput, Button, StyleSheet } from "react-native";
import axios from "axios"; // Import Axios for making API requests
import { styles } from "../styles/GoalSetterStyles";
import GoalContainer from "../containers/GoalContainer";

const GoalSetter: React.FC = () => {
  const [goal, setGoal] = useState("");
  const [goals, setGoals] = useState<string[]>([]);

  // Function to fetch goals from the server
  const fetchGoals = async () => {
    try {
      const response = await axios.get("YOUR_API_ENDPOINT/goals"); // Replace with your API endpoint for fetching goals
      setGoals(response.data); // Update the goals state with data from the server
    } catch (error) {
      console.error("Error fetching goals:", error);
    }
  };

  // Function to create a new goal
  const createNewGoal = async () => {
    if (goal.trim()) {
      try {
        await axios.post("YOUR_API_ENDPOINT/goals", { goal }); // Replace with your API endpoint for creating goals
        setGoal(""); // Clear the input field
        fetchGoals(); // Fetch updated goals after creating a new one
      } catch (error) {
        console.error("Error creating goal:", error);
      }
    }
  };

  // Function to delete a goal
  const deleteGoal = async (goalId: string) => {
    try {
      await axios.delete(`YOUR_API_ENDPOINT/goals/${goalId}`); // Replace with your API endpoint for deleting goals
      fetchGoals(); // Fetch updated goals after deleting one
    } catch (error) {
      console.error("Error deleting goal:", error);
    }
  };

  useEffect(() => {
    // Fetch goals when the component mounts
    fetchGoals();
  }, []);

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Goal Setter</Text>
      <TextInput
        placeholder="Set a Goal"
        value={goal}
        onChangeText={(text) => setGoal(text)}
        style={styles.input}
      />
      <Button title="Add Goal" onPress={createNewGoal} />
      <Text>Your Goals:</Text>
      {goals.map((g, index) => (
        <GoalContainer key={index} goal={g} onDelete={() => deleteGoal(g)} />
      ))}
    </View>
  );
};

export default GoalSetter;
