// LoginContainer.tsx
import React from "react";
import LoginComponent from "../components/LoginComponent"; // Import the LoginComponent
import { useDispatch } from "react-redux"; // Import any necessary dependencies for state management
import { loginUser } from "../services/AuthService"; // Import the loginUser function from the AuthService

const LoginContainer: React.FC = () => {
  const dispatch = useDispatch();

  const handleLogin = async (email: string, password: string) => {
    try {
      // Call your API function to log in the user
      const response = await loginUser(email, password);

      // Handle the response from the API (e.g., dispatch a success action)
      dispatch({ type: "LOGIN_SUCCESS", payload: response.data });
    } catch (error: any) {
      // Specify the type as Error
      // Handle login failure (e.g., dispatch an error action)
      dispatch({ type: "LOGIN_FAILURE", payload: error.message });
    }
  };

  return <LoginComponent onLogin={handleLogin} />;

  return <LoginComponent onLogin={handleLogin} />;
};

export default LoginContainer;
