import { Request, Response } from "express";
import bcrypt from "bcrypt";
import jwt from "jsonwebtoken";
import { body, validationResult } from "express-validator";
import User from "../models/User";
import { auth } from "../middleware/auth"; // Import the auth middleware
import { validateUser } from "../middleware/validationMiddleware"; // Import the validateUser middleware
import { generateAccessToken, refreshAccessToken } from "../utils/tokenUtils"; // Import token-related functions

// Environment variables for JWT
const JWT_SECRET = process.env.JWT_SECRET || "your_jwt_secret"; // Ensure you have this in your .env file



export const registerUser = async (req: Request, res: Response) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }

  const { email, password } = req.body;
  try {
    let user = await User.findOne({ email });
    if (user) {
      return res.status(400).json({ msg: "Email already in use" });
    }

    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(password, salt);

    user = new User({ email, password: hashedPassword });
    await user.save();

    // Exclude password when sending the response
    res.status(201).json({ message: "User registered successfully" });
  } catch (error) {
    console.error("Registration error: ", error);
    res.status(500).json({ error: "Server error during registration" });
  }
};

export const loginUser = async (req: Request, res: Response) => {
  // Validate user input using the imported validateUser middleware
  validateUser.forEach((validation) => validation(req, res, () => {}));

  const { email, password } = req.body;

  try {
    const user = await User.findOne({ email });
    if (!user) {
      return res.status(400).json({ msg: "Invalid Credentials" });
    }

    const isMatch = await bcrypt.compare(password, user.password);
    if (!isMatch) {
      return res.status(400).json({ msg: "Invalid Credentials" });
    }

    const payload = {
      user: {
        id: user.id,
      },
    };

    jwt.sign(payload, JWT_SECRET, { expiresIn: 3600 }, (err, token) => {
      if (err) throw err;
      // Exclude password when sending the response
      res.json({ token });
    });
  } catch (error) {
    if (typeof error === "string") {
      console.log(error);
    } else if (error instanceof Error) {
      console.log(error.message);
    } else {
      console.log("An unknown error occurred");
    }
    res.status(500).send("Server error");
  }
};

// Apply the auth middleware here
export const updateUser = async (req: Request, res: Response) => {
  try {
    const userId = req.user?.id; // Assuming user ID is available from the authentication middleware
    const updatedUser = await User.findByIdAndUpdate(userId, req.body, {
      new: true,
    }).select("-password"); // Exclude password when sending the response
    res.json(updatedUser);
  } catch (error) {
    res.status(500).json({ error: "Failed to update user" });
  }
};

export const deleteUser = async (req: Request, res: Response) => {
  try {
    const userId = req.user?.id;
    await User.findByIdAndDelete(userId);
    res.json({ message: "User deleted successfully" });
  } catch (error) {
    res.status(500).json({ error: "Failed to delete user" });
  }
};

// Logout functionality would be handled client-side by removing the JWT token from storage.
