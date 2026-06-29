/**
 * Check if user is authenticated (has a token in localStorage).
 * If not, redirect to login page preserving the current URL.
 * Returns true if authenticated, false otherwise.
 */
export function requireAuth(): boolean {
  if (typeof window === "undefined") return false;

  const token = localStorage.getItem("auth_token");
  if (token) return true;

  // Not logged in — redirect to login
  const currentPath = window.location.pathname + window.location.search;
  window.location.href = `/login?redirect=${encodeURIComponent(currentPath)}`;
  return false;
}