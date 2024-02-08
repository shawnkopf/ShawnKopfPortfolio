import React, { useEffect } from "react";
import { View, Text, Button } from "react-native";
import {
  requestNotificationPermission,
  scheduleWorkoutNotification,
} from "../utils/NotificationManager";

const WorkoutReminderScreen = () => {
  useEffect(() => {
    // Request notification permissions when the screen mounts
    requestNotificationPermission();
  }, []);

  const handleScheduleNotification = () => {
    // Schedule a workout notification
    const workoutTime = new Date().getTime() + 10000; // Example: 10 seconds from now
    scheduleWorkoutNotification(workoutTime);
  };

  return (
    <View>
      <Text>Workout Reminder Screen</Text>
      <Button
        title="Schedule Notification"
        onPress={handleScheduleNotification}
      />
    </View>
  );
};

export default WorkoutReminderScreen;
