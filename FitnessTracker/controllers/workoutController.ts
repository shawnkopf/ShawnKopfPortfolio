import { Request, Response } from "express";
import Workout from "../models/Workout";

// Fetch user's workouts
export const getWorkouts = async (req: Request, res: Response) => {
  try {
    const userId = req.user?.id; // Use user ID from auth middleware
    const workouts = await Workout.find({ user: userId });
    res.json(workouts);
  } catch (error) {
    res.status(500).json({ error: "Failed to fetch workouts" });
  }
};

// Create a new workout
export const createWorkout = async (req: Request, res: Response) => {
  try {
    const userId = req.user?.id;
    const workout = new Workout({
      ...req.body,
      user: userId,
    });
    const newWorkout = await workout.save();
    res.status(201).json(newWorkout);
  } catch (error) {
    res.status(500).json({ error: "Failed to create workout" });
  }
};

// Update an existing workout
export const updateWorkout = async (req: Request, res: Response) => {
  try {
    const workoutId = req.params.id;
    const updatedWorkout = await Workout.findByIdAndUpdate(
      workoutId,
      req.body,
      { new: true }
    );
    res.json(updatedWorkout);
  } catch (error) {
    res.status(500).json({ error: "Failed to update workout" });
  }
};

// Delete a workout
export const deleteWorkout = async (req: Request, res: Response) => {
  try {
    const workoutId = req.params.id;
    await Workout.findByIdAndDelete(workoutId);
    res.json({ message: "Workout deleted successfully" });
  } catch (error) {
    res.status(500).json({ error: "Failed to delete workout" });
  }
};
