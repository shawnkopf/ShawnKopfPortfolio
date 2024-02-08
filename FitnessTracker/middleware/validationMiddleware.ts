import { body, ValidationChain } from "express-validator";

export const validateUser: ValidationChain[] = [
  body("email").isEmail().withMessage("Must be a valid email address"),
  body("password")
    .isLength({ min: 8 })
    .withMessage("Password must be at least 8 characters long")
    .matches(/\d/)
    .withMessage("Password must contain a number")
    .matches(/[a-z]/)
    .withMessage("Password must contain a lowercase letter")
    .matches(/[A-Z]/)
    .withMessage("Password must contain an uppercase letter")
    .matches(/[\@\!\#\$\%\^\&\*\(\)\_\+\.\,\;\:]/)
    .withMessage(
      "Password must contain a special character (@, !, #, $, etc.)"
    ),
];
