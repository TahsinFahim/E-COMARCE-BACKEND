import { createSlice, createAsyncThunk, type PayloadAction } from "@reduxjs/toolkit";
import type { User } from "./auth.types";
import * as authService from "@/services/auth.service";
import { getClientToken } from "@/services/auth.service";

export interface AuthState {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  isInitialized: boolean;
  error: string | null;
}

// Check localStorage for existing token on init
const hasToken = typeof window !== "undefined" ? !!getClientToken() : false;

const initialState: AuthState = {
  user: null,
  isAuthenticated: hasToken,
  isLoading: false,
  isInitialized: false,
  error: null,
};

export const fetchCurrentUser = createAsyncThunk(
  "auth/fetchCurrentUser",
  async (_, { rejectWithValue }) => {
    try {
      const response = await authService.getAuthenticatedUser();
      return response.user;
    } catch (error: any) {
      if (error.status === 401) return rejectWithValue(null);
      return rejectWithValue(error.message || "Failed to fetch user");
    }
  }
);

export const loginThunk = createAsyncThunk(
  "auth/login",
  async (credentials: { email: string; password: string }, { rejectWithValue }) => {
    try {
      const response = await authService.loginUser(credentials);
      return response.user;
    } catch (error: any) {
      return rejectWithValue({ message: error.message, errors: error.errors });
    }
  }
);

export const registerThunk = createAsyncThunk(
  "auth/register",
  async (data: { first_name: string; last_name: string; email: string; phone?: string; password: string; password_confirmation: string }, { rejectWithValue }) => {
    try {
      const response = await authService.registerUser(data);
      return response.user;
    } catch (error: any) {
      return rejectWithValue({ message: error.message, errors: error.errors });
    }
  }
);

export const logoutThunk = createAsyncThunk(
  "auth/logout",
  async (_, { rejectWithValue }) => {
    try {
      await authService.logoutUser();
      return true;
    } catch (error: any) {
      return rejectWithValue(error.message || "Logout failed");
    }
  }
);

const authSlice = createSlice({
  name: "auth",
  initialState,
  reducers: {
    setUser(state, action: PayloadAction<User>) {
      state.user = action.payload;
      state.isAuthenticated = true;
      state.isInitialized = true;
    },
    clearUser(state) {
      state.user = null;
      state.isAuthenticated = false;
      state.isInitialized = true;
      state.error = null;
    },
    setInitialized(state) { state.isInitialized = true; },
    clearError(state) { state.error = null; },
  },
  extraReducers: (builder) => {
    builder.addCase(fetchCurrentUser.pending, (state) => { state.isLoading = true; })
      .addCase(fetchCurrentUser.fulfilled, (state, action) => {
        state.user = action.payload; state.isAuthenticated = true; state.isLoading = false; state.isInitialized = true; state.error = null;
      })
      .addCase(fetchCurrentUser.rejected, (state, action) => {
        state.user = null; state.isAuthenticated = false; state.isLoading = false; state.isInitialized = true; state.error = action.payload as string | null;
      });
    builder.addCase(loginThunk.pending, (state) => { state.isLoading = true; state.error = null; })
      .addCase(loginThunk.fulfilled, (state, action) => { state.user = action.payload; state.isAuthenticated = true; state.isLoading = false; state.error = null; })
      .addCase(loginThunk.rejected, (state, action) => { state.isLoading = false; state.error = (action.payload as any)?.message || "Login failed"; });
    builder.addCase(registerThunk.pending, (state) => { state.isLoading = true; state.error = null; })
      .addCase(registerThunk.fulfilled, (state, action) => { state.user = action.payload; state.isAuthenticated = true; state.isLoading = false; state.error = null; })
      .addCase(registerThunk.rejected, (state, action) => { state.isLoading = false; state.error = (action.payload as any)?.message || "Registration failed"; });
    builder.addCase(logoutThunk.pending, (state) => { state.isLoading = true; })
      .addCase(logoutThunk.fulfilled, (state) => { state.user = null; state.isAuthenticated = false; state.isLoading = false; state.error = null; })
      .addCase(logoutThunk.rejected, (state) => { state.user = null; state.isAuthenticated = false; state.isLoading = false; });
  },
});

export const { setUser, clearUser, setInitialized, clearError } = authSlice.actions;
export default authSlice.reducer;