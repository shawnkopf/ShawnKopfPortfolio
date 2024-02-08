import jwt from "jsonwebtoken";
import { Request, Response } from "express"; // Import Request and Response

const JWT_SECRET = process.env.JWT_SECRET || "your_jwt_secret";

export function generateAccessToken(payload: object): string {
  return jwt.sign(payload, JWT_SECRET, { expiresIn: "15m" });
}

export function verifyAccessToken(token: string): object | null {
  try {
    return jwt.verify(token, JWT_SECRET) as object;
  } catch (error) {
    return null;
  }
}

export const refreshAccessToken = async (
  req: Request,
  res: Response
): Promise<void> => {
  const { refreshToken } = req.body;
  if (!refreshToken) {
    res.status(401).json({ error: "Refresh Token Required" });
    return;
  }

  try {
    const payload = jwt.verify(
      refreshToken,
      process.env.JWT_SECRET || "your_jwt_secret"
    ) as jwt.JwtPayload;
    const accessToken = jwt.sign(
      { user: payload.user },
      process.env.JWT_SECRET || "your_jwt_secret",
      { expiresIn: "15m" }
    );
    res.json({ accessToken });
  } catch (error) {
    res.status(403).json({ error: "Invalid or Expired Refresh Token" });
  }
};
