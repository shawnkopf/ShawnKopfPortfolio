import mongoose, { Schema, Document } from "mongoose";

interface IWorkout extends Document {
  userId: string;
  date: Date;
  duration: number;
  // Add other workout fields here
}

const WorkoutSchema: Schema = new Schema({
  userId: { type: String, required: true },
  date: { type: Date, required: true },
  duration: { type: Number, required: true },
  // Define other workout fields here
});

const Workout = mongoose.model<IWorkout>("Workout", WorkoutSchema);

export default Workout;
