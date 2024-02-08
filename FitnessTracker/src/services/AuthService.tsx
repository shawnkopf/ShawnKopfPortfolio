// src/services/AuthService.ts
import axios from 'axios';

const API_BASE_URL = 'https://your-auth-api-url.com';

export async function registerUser(email: string, password: string) {
  try {
    const response = await axios.post(`${API_BASE_URL}/register`, {
      email,
      password,
    });
    return response.data;
  } catch (error) {
    throw error;
  }
}

export async function loginUser(email: string, password: string) {
  try {
    const response = await axios.post(`${API_BASE_URL}/login`, {
      email,
      password,
    });
    return response.data;
  } catch (error) {
    throw error;
  }
}
