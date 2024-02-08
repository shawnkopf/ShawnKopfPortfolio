// src/navigation/AppNavigator.js
import React from "react";
import { NavigationContainer } from "@react-navigation/native";
import { createBottomTabNavigator } from "@react-navigation/bottom-tabs";
import { createDrawerNavigator } from "@react-navigation/drawer";
import { createStackNavigator } from "@react-navigation/stack";
import HomeScreen from "../screens/HomeScreen";
import WorkoutTrackerScreen from "../screens/WorkoutTrackerScreen";
import StatisticsViewerScreen from "../screens/StatisticsViewerScreen";

const Tab = createBottomTabNavigator();
const Drawer = createDrawerNavigator();
const Stack = createStackNavigator();

const TabNavigator = () => {
  return (
    <Tab.Navigator>
      <Tab.Screen name="Home" component={HomeScreen} />
      <Tab.Screen name="Workout Tracker" component={WorkoutTrackerScreen} />
      <Tab.Screen name="Statistics Viewer" component={StatisticsViewerScreen} />
    </Tab.Navigator>
  );
};

const AppStackNavigator = () => {
  return (
    <Stack.Navigator>
      <Stack.Screen
        name="TabNavigator"
        component={TabNavigator}
        options={{ headerShown: false }}
      />
    </Stack.Navigator>
  );
};

const AppDrawerNavigator = () => {
  return (
    <Drawer.Navigator>
      <Drawer.Screen name="AppStackNavigator" component={AppStackNavigator} />
    </Drawer.Navigator>
  );
};

const AppNavigator = () => {
  return (
    <NavigationContainer>
      <AppDrawerNavigator />
    </NavigationContainer>
  );
};

export default AppNavigator;
