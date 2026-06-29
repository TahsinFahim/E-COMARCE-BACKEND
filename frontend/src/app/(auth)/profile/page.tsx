// ============================================================
// Profile Page - Protected route
// Shows authenticated user's information.
// ============================================================

"use client";

import { useActionState, useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import AuthGuard, { useAuth } from "@/components/auth/AuthGuard";
import { logoutUserAction, logoutAllDevicesAction } from "@/app/actions/auth";
import type { User } from "@/lib/features/auth/auth.types";

function ProfileContent() {
  const { user } = useAuth();
  const router = useRouter();
  const [showLogoutConfirm, setShowLogoutConfirm] = useState(false);

  const [logoutState, logoutAction, logoutPending] = useActionState(
    async () => {
      const result = await logoutUserAction();
      if (result.success) {
        if (typeof window !== "undefined") {
        }
        router.push("/login");
      }
      return result;
    },
    { success: false, message: "" }
  );

  const [logoutAllState, logoutAllAction, logoutAllPending] = useActionState(
    async () => {
      const result = await logoutAllDevicesAction();
      if (result.success) {
        if (typeof window !== "undefined") {
        }
        router.push("/login");
      }
      return result;
    },
    { success: false, message: "" }
  );

  if (!user) return null;

  const fullName = `${user.first_name} ${user.last_name}`;
  const roleNames = user.roles?.map((r) => r.name).join(", ") || "No role";
  const statusColors: Record<string, string> = {
    active: "bg-green-100 text-green-800",
    inactive: "bg-yellow-100 text-yellow-800",
    suspended: "bg-red-100 text-red-800",
  };

  return (
    <div className="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-2xl mx-auto space-y-8">
        {/* Header */}
        <div className="text-center">
          <Link href="/" className="inline-block">
            <h1 className="text-3xl font-bold text-[var(--color-primary)]">Shopio</h1>
          </Link>
          <h2 className="mt-4 text-2xl font-semibold text-gray-900">
            My Profile
          </h2>
        </div>

        {/* User Info Card */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          {/* Avatar & Name */}
          <div className="bg-gradient-to-r from-[var(--color-primary)] to-[var(--color-primary)] px-6 py-8 text-center">
            <div className="inline-flex items-center justify-center h-20 w-20 rounded-full bg-white/20 text-white text-3xl font-bold mb-3">
              {user.first_name.charAt(0)}
              {user.last_name.charAt(0)}
            </div>
            <h3 className="text-xl font-semibold text-white">{fullName}</h3>
            <p className="text-sm text-white/80">{user.email}</p>
          </div>

          {/* Details */}
          <div className="px-6 py-6 space-y-5">
            {/* Status */}
            <div className="flex items-center justify-between">
              <span className="text-sm font-medium text-gray-500">Status</span>
              <span
                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                  statusColors[user.status] || "bg-gray-100 text-gray-800"
                }`}
              >
                {user.status.charAt(0).toUpperCase() + user.status.slice(1)}
              </span>
            </div>

            <hr className="border-gray-100" />

            {/* Name */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <span className="block text-xs font-medium text-gray-500 uppercase tracking-wider">
                  First name
                </span>
                <span className="block mt-1 text-sm text-gray-900">
                  {user.first_name}
                </span>
              </div>
              <div>
                <span className="block text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Last name
                </span>
                <span className="block mt-1 text-sm text-gray-900">
                  {user.last_name}
                </span>
              </div>
            </div>

            <hr className="border-gray-100" />

            {/* Email */}
            <div>
              <span className="block text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </span>
              <span className="block mt-1 text-sm text-gray-900">
                {user.email}
              </span>
            </div>

            {/* Phone */}
            {user.phone && (
              <>
                <hr className="border-gray-100" />
                <div>
                  <span className="block text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Phone
                  </span>
                  <span className="block mt-1 text-sm text-gray-900">
                    {user.phone}
                  </span>
                </div>
              </>
            )}

            <hr className="border-gray-100" />

            {/* Roles */}
            <div>
              <span className="block text-xs font-medium text-gray-500 uppercase tracking-wider">
                Roles
              </span>
              <span className="block mt-1 text-sm text-gray-900">
                {roleNames}
              </span>
            </div>

            {/* Last Login */}
            {user.last_login_at && (
              <>
                <hr className="border-gray-100" />
                <div>
                  <span className="block text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last login
                  </span>
                  <span className="block mt-1 text-sm text-gray-900">
                    {new Date(user.last_login_at).toLocaleString()}
                  </span>
                </div>
              </>
            )}

            {/* Member Since */}
            <hr className="border-gray-100" />
            <div>
              <span className="block text-xs font-medium text-gray-500 uppercase tracking-wider">
                Member since
              </span>
              <span className="block mt-1 text-sm text-gray-900">
                {new Date(user.created_at).toLocaleDateString("en-US", {
                  year: "numeric",
                  month: "long",
                  day: "numeric",
                })}
              </span>
            </div>
          </div>
        </div>

        {/* Actions */}
        <div className="space-y-3">
          <Link
            href="/change-password"
            className="block w-full text-center px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            Change password
          </Link>

          {/* Logout Button */}
          <form action={logoutAction}>
            <button
              type="submit"
              disabled={logoutPending}
              className="w-full flex items-center justify-center px-4 py-2.5 border border-red-200 rounded-lg shadow-sm text-sm font-semibold text-red-600 bg-white hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {logoutPending ? (
                <>
                  <svg
                    className="animate-spin -ml-1 mr-2 h-4 w-4 text-red-600"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      className="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      strokeWidth="4"
                    />
                    <path
                      className="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    />
                  </svg>
                  Logging out...
                </>
              ) : (
                "Logout"
              )}
            </button>
          </form>

          {/* Logout All Devices */}
          <form action={logoutAllAction}>
            <button
              type="submit"
              disabled={logoutAllPending}
              className="w-full flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-lg shadow-sm text-sm font-medium text-gray-500 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {logoutAllPending ? (
                <>
                  <svg
                    className="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      className="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      strokeWidth="4"
                    />
                    <path
                      className="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    />
                  </svg>
                  Logging out all devices...
                </>
              ) : (
                "Logout from all devices"
              )}
            </button>
          </form>
        </div>

        {/* Back to Home */}
        <div className="text-center">
          <Link
            href="/"
            className="text-sm font-medium text-[var(--color-primary)] hover:text-[var(--color-primary)] transition-colors"
          >
            Back to home
          </Link>
        </div>
      </div>
    </div>
  );
}

export default function ProfilePage() {
  return (
    <AuthGuard>
      <ProfileContent />
    </AuthGuard>
  );
}