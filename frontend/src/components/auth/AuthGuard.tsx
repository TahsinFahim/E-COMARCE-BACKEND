"use client";

import { useEffect, useState, useCallback } from "react";
import { useRouter } from "next/navigation";
import * as authService from "@/services/auth.service";
import type { User } from "@/lib/features/auth/auth.types";

interface AuthGuardProps {
  children: React.ReactNode;
  requireGuest?: boolean;
  fallback?: React.ReactNode;
}

export default function AuthGuard({
  children,
  requireGuest = false,
  fallback,
}: AuthGuardProps) {
  const router = useRouter();
  const [status, setStatus] = useState<"loading" | "authenticated" | "guest">("loading");
  const [user, setUser] = useState<User | null>(null);

  const checkAuth = useCallback(async () => {
    try {
      // Use client-side auth service which sends Bearer token from localStorage
      // This works because the token is stored in localStorage on login/register
      const response = await authService.getAuthenticatedUser();
      setUser(response.user);
      setStatus("authenticated");
      if (requireGuest) router.push("/");
    } catch {
      setUser(null);
      setStatus("guest");
      if (!requireGuest) {
        router.push("/login?redirect=" + encodeURIComponent(window.location.pathname));
      }
    }
  }, [router, requireGuest]);

  useEffect(() => {
    checkAuth();
  }, [checkAuth]);

  if (status === "loading") {
    return (
      fallback || (
        <div className="min-h-screen flex items-center justify-center bg-gray-50">
          <div className="text-center">
            <div className="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-[var(--color-primary)] border-r-transparent" />
            <p className="mt-2 text-sm text-gray-600">Loading...</p>
          </div>
        </div>
      )
    );
  }

  if (requireGuest && status === "authenticated") return null;
  if (!requireGuest && status === "guest") return null;

  return (
    <AuthContext.Provider value={{ user, isAuthenticated: status === "authenticated" }}>
      {children}
    </AuthContext.Provider>
  );
}

import { createContext, useContext } from "react";

interface AuthContextValue {
  user: User | null;
  isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextValue>({ user: null, isAuthenticated: false });

export function useAuth() {
  return useContext(AuthContext);
}