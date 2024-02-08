import express from "express";
import {
  getGoals,
  createGoal,
  updateGoal,
  deleteGoal,
} from "../controllers/goalController";

const router = express.Router();

// Get user's goals
router.get("/goals", getGoals);

// Create a new goal
router.post("/goals", createGoal);

// Update an existing goal
router.put("/goals/:id", updateGoal);

// Delete a goal
router.delete("/goals/:id", deleteGoal);

export default router;
