// src/redux/types/authTypes.ts
export interface AuthState {
  isAuthenticated: boolean;
}

// Action Types
export const LOGIN = "LOGIN";
export const LOGOUT = "LOGOUT";
export const LOGIN_SUCCESS = "LOGIN_SUCCESS";

// Action Interfaces
interface LoginAction {
  type: typeof LOGIN;
}

interface LogoutAction {
  type: typeof LOGOUT;
}

interface LoginSuccessAction {
  type: typeof LOGIN_SUCCESS;
}

export type AuthActionTypes = LoginAction | LogoutAction | LoginSuccessAction;
