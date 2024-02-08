// middleware/auth.ts
import { Request, Response, NextFunction } from "express";
import jwt from "jsonwebtoken";

interface DecodedUser {
  user: {
    id: string;
  };
  iat?: number;
  exp?: number;
}

declare global {
  namespace Express {
    interface Request {
      user?: DecodedUser["user"]; // Extend the Request type to include the user property
    }
  }
}

const JWT_SECRET = process.env.JWT_SECRET || "your_jwt_secret";

export const auth = (req: Request, res: Response, next: NextFunction): void => {
  // Get token from header
  const token = req.header("x-auth-token");

  // Check if not token
  if (!token) {
    res.status(401).json({ msg: "No token, authorization denied" });
    return;
  }

  // Verify token
  try {
    const decoded = jwt.verify(token, JWT_SECRET) as DecodedUser;
    req.user = decoded.user;
    next();
  } catch (err) {
    res.status(401).json({ msg: "Token is not valid" });
  }
};
