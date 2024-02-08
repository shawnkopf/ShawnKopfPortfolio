// src/redux/actions/authActions.ts
import { AuthActionTypes } from "../types/authTypes";

export const login = (): AuthActionTypes => ({
  type: "LOGIN",
});

export const logout = (): AuthActionTypes => ({
  type: "LOGOUT",
});
