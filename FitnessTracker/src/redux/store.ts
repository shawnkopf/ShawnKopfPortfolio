import { configureStore, ThunkAction } from "@reduxjs/toolkit";
import { combineReducers } from "redux";
import authReducer from "./reducers/authReducer";
import { AuthActionTypes } from "./types/authTypes";

// Define your RootState type
export type RootState = {
  auth: AuthState; // Assuming AuthState is defined in authTypes.ts
};

// Define your AuthState type
export interface AuthState {
  isAuthenticated: boolean;
}

const rootReducer = combineReducers({
  auth: authReducer,
});

// Define Thunk type for your actions
export type AppThunk<ReturnType = void> = ThunkAction<
  ReturnType,
  RootState,
  unknown,
  AuthActionTypes // Use AuthActionTypes directly here
>;

const store = configureStore({
  reducer: rootReducer,
});

export default store;
