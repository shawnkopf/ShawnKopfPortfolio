import express from "express";
import mongoose from "mongoose";
import { MongoClientOptions } from "mongodb";
import cors from "cors";

// Import your route files
import authRoutes from "./routes/authRoutes";
import workoutRoutes from "./routes/workoutRoutes";
import goalRoutes from "./routes/goalRoutes";

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());

// MongoDB connection
mongoose
  .connect("mongodb://localhost/fitness_tracker", {
    useCreateIndex: true, // To suppress deprecation warnings about ensureIndex
    useNewUrlParser: true,
    useUnifiedTopology: true,
  } as MongoClientOptions)
  .then(() => {
    console.log("Connected to MongoDB");
  })
  .catch((error) => {
    console.error("MongoDB connection failed:", error);
  });

// Define routes using the imported route files
app.use("/auth", authRoutes); // Prefix the routes with '/auth'
app.use("/workouts", workoutRoutes); // Prefix the routes with '/workouts'
app.use("/goals", goalRoutes); // Prefix the routes with '/goals'

// Default route
app.get("/", (req, res) => {
  res.send("Fitness Tracker API");
});

// Start server
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
