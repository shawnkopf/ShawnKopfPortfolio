// src/redux/reducers/authReducer.ts
import { AuthActionTypes, AuthState } from "../types/authTypes";

const initialState: AuthState = {
  isAuthenticated: false,
};

const authReducer = (
  state = initialState,
  action: AuthActionTypes
): AuthState => {
  switch (action.type) {
    case "LOGIN":
      return {
        ...state,
        isAuthenticated: true,
      };
    case "LOGOUT":
      return {
        ...state,
        isAuthenticated: false,
      };
    default:
      return state;
  }
};

export default authReducer;
