import mongoose, { Schema, Document } from "mongoose";

interface IGoal extends Document {
  userId: string;
  target: number;
  // Add other goal fields here
}

const GoalSchema: Schema = new Schema({
  userId: { type: String, required: true },
  target: { type: Number, required: true },
  // Define other goal fields here
});

const Goal = mongoose.model<IGoal>("Goal", GoalSchema);

export default Goal;
