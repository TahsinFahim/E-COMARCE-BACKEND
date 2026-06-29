const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api/v1";

interface ApiOptions extends RequestInit {
  revalidate?: number;
  tags?: string[];
}

function getToken(): string | null {
  if (typeof window === "undefined") return null;
  try { return localStorage.getItem("auth_token"); }
  catch { return null; }
}

export async function api<T>(
  endpoint: string,
  options: ApiOptions = {}
): Promise<T> {
  const { revalidate, tags, ...fetchOptions } = options;
  const token = getToken();

  const headers: Record<string, string> = {
    Accept: "application/json",
    "Content-Type": "application/json",
    ...(fetchOptions.headers as Record<string, string>),
  };

  // Send Bearer token from localStorage for auth
  if (token) {
    headers["Authorization"] = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE_URL}${endpoint}`, {
    ...fetchOptions,
    credentials: "include",
    headers,
    next: revalidate ? { revalidate, tags } : undefined,
    cache: revalidate ? undefined : "no-store",
  });

  if (!response.ok) {
    const errorBody = await response.json().catch(() => ({}));
    throw new Error(errorBody.message || `API Error: ${response.status}`);
  }

  return await response.json();
}