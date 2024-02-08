import express from 'express';
import { getWorkouts, createWorkout, updateWorkout, deleteWorkout } from '../controllers/workoutController';

const router = express.Router();

// Get user's workouts
router.get('/workouts', getWorkouts);

// Create a new workout
router.post('/workouts', createWorkout);

// Update an existing workout
router.put('/workouts/:id', updateWorkout);

// Delete a workout
router.delete('/workouts/:id', deleteWorkout);

export default router;
