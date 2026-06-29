// ============================================================
// Auth Server Actions
// Dual approach:
// 1. Returns token to client for localStorage
// 2. Forwards httpOnly Set-Cookie to browser
// 3. Forwards browser cookies to backend for auth
// ============================================================

"use server";

import { cookies } from "next/headers";
import type {
  AuthFormState,
  PasswordFormState,
  ForgotPasswordFormState,
} from "@/lib/features/auth/auth.types";

const API_BASE_URL = process.env.BACKEND_API_URL || "http://localhost:8000/api/v1";

/**
 * Make server-to-server API call forwarding browser cookies.
 */
async function serverFetch<T>(
  endpoint: string,
  options: RequestInit = {}
): Promise<{ data: T; setCookieHeader: string | null }> {
  const cookieStore = await cookies();
  const allCookies = cookieStore.toString();

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...options,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
      ...(allCookies ? { Cookie: allCookies } : {}),
      ...(options.headers as Record<string, string>),
    },
  });

  const setCookieHeader = response.headers.get("set-cookie");
  const text = await response.text();

  let data: any;
  try {
    data = JSON.parse(text);
  } catch {
    data = { message: "Invalid response from server" };
  }

  if (!response.ok) {
    const error = new Error(data.message || "Something went wrong") as Error & {
      status: number;
      errors?: Record<string, string[]>;
    };
    error.status = response.status;
    error.errors = data.errors;
    throw error;
  }

  return { data: data as T, setCookieHeader };
}

/**
 * Forward Set-Cookie from backend to browser.
 */
async function forwardCookies(setCookieHeader: string | null) {
  if (!setCookieHeader) return;
  const cookieStore = await cookies();

  const cookieHeaders = splitCookies(setCookieHeader);
  for (const cookie of cookieHeaders) {
    const parsed = parseSetCookie(cookie);
    if (parsed) {
      const { name, value, maxAge, path, domain, secure, httpOnly, sameSite } = parsed;

      if (maxAge === 0) {
        cookieStore.delete(name);
      } else {
        cookieStore.set(name, value, {
          maxAge,
          path: path || "/",
          domain,
          secure,
          httpOnly,
          sameSite: sameSite as "strict" | "lax" | "none" | undefined,
        });
      }
    }
  }
}

function splitCookies(header: string): string[] {
  const results: string[] = [];
  let current = "";
  let inQuotedPair = false;
  let i = 0;

  while (i < header.length) {
    const char = header[i];
    if (char === '"' && (i === 0 || header[i - 1] !== '\\')) {
      inQuotedPair = !inQuotedPair;
      current += char;
      i++;
      continue;
    }
    if (!inQuotedPair && char === ',') {
      const afterComma = header.slice(i + 1).trim();
      if (afterComma.includes('=') && !afterComma.startsWith(' ')) {
        results.push(current.trim());
        current = "";
        i++;
        continue;
      }
    }
    current += char;
    i++;
  }
  if (current.trim()) results.push(current.trim());
  return results.length > 0 ? results : [header];
}

function parseSetCookie(cookieString: string): {
  name: string;
  value: string;
  maxAge?: number;
  path?: string;
  domain?: string;
  secure?: boolean;
  httpOnly?: boolean;
  sameSite?: string;
} | null {
  const parts = cookieString.split(";").map((p) => p.trim());
  const [nameValue, ...attrs] = parts;
  if (!nameValue) return null;
  const eqIndex = nameValue.indexOf("=");
  if (eqIndex === -1) return null;
  const name = nameValue.slice(0, eqIndex).trim();
  const value = nameValue.slice(eqIndex + 1).trim();
  const result: any = { name, value, secure: false, httpOnly: false };

  for (const attr of attrs) {
    const [key, ...valParts] = attr.split("=");
    const val = valParts.join("=").trim();
    switch (key.toLowerCase()) {
      case "max-age": const p = parseInt(val, 10); if (!isNaN(p)) result.maxAge = p; break;
      case "path": result.path = val; break;
      case "domain": result.domain = val; break;
      case "secure": result.secure = true; break;
      case "httponly": result.httpOnly = true; break;
      case "samesite": result.sameSite = val.toLowerCase(); break;
    }
  }
  return result;
}

// ============================================================
// Public Server Actions
// ============================================================

export async function registerUser(
  prevState: AuthFormState,
  formData: FormData
): Promise<AuthFormState> {
  try {
    const { data, setCookieHeader } = await serverFetch<any>("/register", {
      method: "POST",
      body: JSON.stringify({
        first_name: formData.get("first_name"),
        last_name: formData.get("last_name"),
        email: formData.get("email"),
        phone: formData.get("phone") || undefined,
        password: formData.get("password"),
        password_confirmation: formData.get("password_confirmation"),
      }),
    });

    await forwardCookies(setCookieHeader);

    return {
      success: true,
      message: data.message || "Registration successful.",
      user: data.user,
      token: data.token ?? data.access_token ?? null,
    };
  } catch (error: any) {
    return {
      success: false,
      message: error.message || "Registration failed.",
      errors: error.errors,
      fieldValues: {
        first_name: formData.get("first_name") as string,
        last_name: formData.get("last_name") as string,
        email: formData.get("email") as string,
        phone: formData.get("phone") as string,
      },
    };
  }
}

export async function loginUser(
  prevState: AuthFormState,
  formData: FormData
): Promise<AuthFormState> {
  try {
    const { data, setCookieHeader } = await serverFetch<any>("/login", {
      method: "POST",
      body: JSON.stringify({
        email: formData.get("email"),
        password: formData.get("password"),
      }),
    });

    await forwardCookies(setCookieHeader);

    return {
      success: true,
      message: data.message || "Login successful.",
      user: data.user,
      token: data.token ?? data.access_token ?? null,
    };
  } catch (error: any) {
    return {
      success: false,
      message: error.message || "Login failed.",
      errors: error.errors,
      fieldValues: {
        email: formData.get("email") as string,
      },
    };
  }
}

export async function forgotPasswordAction(
  prevState: ForgotPasswordFormState,
  formData: FormData
): Promise<ForgotPasswordFormState> {
  try {
    const { data } = await serverFetch<any>("/forgot-password", {
      method: "POST",
      body: JSON.stringify({ email: formData.get("email") }),
    });
    return { success: true, message: data.message || "Reset link sent." };
  } catch (error: any) {
    return { success: false, message: error.message || "Failed.", errors: error.errors };
  }
}

export async function resetPasswordAction(
  prevState: PasswordFormState,
  formData: FormData
): Promise<PasswordFormState> {
  try {
    const { data } = await serverFetch<any>("/reset-password", {
      method: "POST",
      body: JSON.stringify({
        email: formData.get("email"),
        token: formData.get("token"),
        password: formData.get("password"),
        password_confirmation: formData.get("password_confirmation"),
      }),
    });
    return { success: true, message: data.message || "Password reset successful." };
  } catch (error: any) {
    return { success: false, message: error.message || "Failed.", errors: error.errors };
  }
}

// ============================================================
// Protected Server Actions
// ============================================================

export async function logoutUserAction(): Promise<{ success: boolean; message: string }> {
  try {
    const { setCookieHeader } = await serverFetch<any>("/logout", { method: "POST" });
    await forwardCookies(setCookieHeader);
    return { success: true, message: "Logged out." };
  } catch (error: any) {
    try { const c = await cookies(); c.delete("auth_token"); } catch (_) {}
    return { success: true, message: "Logged out." };
  }
}

export async function logoutAllDevicesAction(): Promise<{ success: boolean; message: string }> {
  try {
    const { setCookieHeader } = await serverFetch<any>("/logout-all", { method: "POST" });
    await forwardCookies(setCookieHeader);
    return { success: true, message: "Logged out from all devices." };
  } catch (error: any) {
    try { const c = await cookies(); c.delete("auth_token"); } catch (_) {}
    return { success: true, message: "Logged out." };
  }
}

export async function changePasswordAction(
  prevState: PasswordFormState,
  formData: FormData
): Promise<PasswordFormState> {
  try {
    const { data } = await serverFetch<any>("/change-password", {
      method: "POST",
      body: JSON.stringify({
        current_password: formData.get("current_password"),
        password: formData.get("password"),
        password_confirmation: formData.get("password_confirmation"),
      }),
    });
    return { success: true, message: data.message || "Password changed." };
  } catch (error: any) {
    return { success: false, message: error.message || "Failed.", errors: error.errors };
  }
}

export async function getAuthenticatedUserAction(): Promise<{
  success: boolean;
  user?: any;
  message?: string;
}> {
  try {
    const { data } = await serverFetch<any>("/user", { method: "GET" });
    return { success: true, user: data.user };
  } catch (error: any) {
    return { success: false, message: "Not authenticated." };
  }
}