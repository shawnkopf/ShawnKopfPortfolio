import express from "express";
import { refreshAccessToken } from "../utils/tokenUtils"; // Import from tokenUtils.ts

import {
  registerUser,
  loginUser,
  updateUser, // Make sure to import the function
  deleteUser, // Make sure to import the function
} from "../controllers/authController";
import { auth } from "../middleware/auth"; // Import the auth middleware

const router = express.Router();

// Existing routes for registration and login
router.post("/register", registerUser);
router.post("/login", loginUser);

// Add a new route for token refresh (No auth middleware applied here)
router.post("/refresh", refreshAccessToken);

// Protected routes (auth middleware applied)
router.put("/update", auth, updateUser);
router.delete("/delete", auth, deleteUser);

export default router;
