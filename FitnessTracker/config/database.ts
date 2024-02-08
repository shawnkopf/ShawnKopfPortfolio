import mongoose from "mongoose";
import dotenv from "dotenv";

// Import your models
import User from "../models/User";
import Workout from "../models/Workout";
import Goal from "../models/Goal";

// Load environment variables
dotenv.config();

const databaseConfig = {
  url: process.env.MONGODB_URI || "mongodb://localhost/fitness_tracker", // Fallback to localhost if the environment variable is not set
};

const connectDatabase = async () => {
  try {
    // Connect to MongoDB without the deprecated options
    await mongoose.connect(databaseConfig.url);

    console.log("Connected to MongoDB");
  } catch (error) {
    console.error("MongoDB connection failed:", error);
  }
};

// Define and export your models here
export { User, Workout, Goal, connectDatabase };
