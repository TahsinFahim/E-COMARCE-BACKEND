// ============================================================
// Auth Service - API calls for authentication
// Dual auth: httpOnly cookie (server actions) + Bearer token (client-side)
// ============================================================

import type {
  AuthSuccessResponse,
  UserResponse,
  LogoutResponse,
  MessageResponse,
  RegisterRequest,
  LoginRequest,
  ForgotPasswordRequest,
  ResetPasswordRequest,
  ChangePasswordRequest,
} from "@/lib/features/auth/auth.types";

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api/v1";

/**
 * Get auth token from localStorage (client-side only).
 */
export function getClientToken(): string | null {
  if (typeof window === "undefined") return null;
  try {
    return localStorage.getItem("auth_token");
  } catch {
    return null;
  }
}

/**
 * Store auth token in localStorage.
 */
export function setClientToken(token: string): void {
  if (typeof window === "undefined") return;
  try {
    localStorage.setItem("auth_token", token);
  } catch {
    // ignore
  }
}

/**
 * Remove auth token from localStorage.
 */
export function removeClientToken(): void {
  if (typeof window === "undefined") return;
  try {
    localStorage.removeItem("auth_token");
  } catch {
    // ignore
  }
}

/**
 * Base fetch wrapper with Bearer token auth.
 * Sends httpOnly cookie AND Bearer token for dual auth.
 */
async function authFetch<T>(
  endpoint: string,
  options: RequestInit = {}
): Promise<T> {
  const token = getClientToken();

  const headers: Record<string, string> = {
    Accept: "application/json",
    "Content-Type": "application/json",
    ...(options.headers as Record<string, string>),
  };

  // Add Bearer token if available (primary auth for client-side calls)
  if (token) {
    headers["Authorization"] = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...options,
    credentials: "include",
    headers,
  });

  const data = await response.json();

  if (!response.ok) {
    const error = new Error(data.message || "Something went wrong") as Error & {
      status: number;
      errors?: Record<string, string[]>;
    };
    error.status = response.status;
    error.errors = data.errors;
    throw error;
  }

  return data as T;
}

export async function registerUser(data: RegisterRequest): Promise<AuthSuccessResponse> {
  return authFetch<AuthSuccessResponse>("/register", {
    method: "POST",
    body: JSON.stringify(data),
  });
}

export async function loginUser(data: LoginRequest): Promise<AuthSuccessResponse> {
  return authFetch<AuthSuccessResponse>("/login", {
    method: "POST",
    body: JSON.stringify(data),
  });
}

export async function forgotPassword(data: ForgotPasswordRequest): Promise<MessageResponse> {
  return authFetch<MessageResponse>("/forgot-password", {
    method: "POST",
    body: JSON.stringify(data),
  });
}

export async function resetPassword(data: ResetPasswordRequest): Promise<MessageResponse> {
  return authFetch<MessageResponse>("/reset-password", {
    method: "POST",
    body: JSON.stringify(data),
  });
}

export async function getAuthenticatedUser(): Promise<UserResponse> {
  return authFetch<UserResponse>("/user", { method: "GET" });
}

export async function logoutUser(): Promise<LogoutResponse> {
  return authFetch<LogoutResponse>("/logout", { method: "POST" });
}

export async function logoutAllDevices(): Promise<LogoutResponse> {
  return authFetch<LogoutResponse>("/logout-all", { method: "POST" });
}

export async function changePassword(data: ChangePasswordRequest): Promise<MessageResponse> {
  return authFetch<MessageResponse>("/change-password", {
    method: "POST",
    body: JSON.stringify(data),
  });
}

export async function refreshToken(): Promise<MessageResponse> {
  return authFetch<MessageResponse>("/refresh", { method: "POST" });
}