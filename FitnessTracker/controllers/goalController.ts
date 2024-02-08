import { Request, Response } from "express";
import Goal from "../models/Goal";

// Fetch user's goals
export const getGoals = async (req: Request, res: Response) => {
  try {
    const userId = req.user?.id; // Assuming you have user ID from the auth middleware
    const goals = await Goal.find({ user: userId });
    res.json(goals);
  } catch (error) {
    res.status(500).json({ error: "Failed to fetch goals" });
  }
};

// Create a new goal
export const createGoal = async (req: Request, res: Response) => {
  try {
    const userId = req.user?.id;
    const goal = new Goal({
      ...req.body,
      user: userId,
    });
    const newGoal = await goal.save();
    res.status(201).json(newGoal);
  } catch (error) {
    res.status(500).json({ error: "Failed to create goal" });
  }
};

// Update an existing goal
export const updateGoal = async (req: Request, res: Response) => {
  try {
    const goalId = req.params.id;
    const updatedGoal = await Goal.findByIdAndUpdate(goalId, req.body, {
      new: true,
    });
    res.json(updatedGoal);
  } catch (error) {
    res.status(500).json({ error: "Failed to update goal" });
  }
};

// Delete a goal
export const deleteGoal = async (req: Request, res: Response) => {
  try {
    const goalId = req.params.id;
    await Goal.findByIdAndDelete(goalId);
    res.json({ message: "Goal deleted successfully" });
  } catch (error) {
    res.status(500).json({ error: "Failed to delete goal" });
  }
};
