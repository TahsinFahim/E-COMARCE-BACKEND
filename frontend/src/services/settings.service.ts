import { api } from "@/lib/api";

export interface SettingsResponse {
  success: boolean;
  data: Record<string, string | null>;
}

export const settingsService = {
  async getAll(): Promise<SettingsResponse> {
    return api<SettingsResponse>("/settings", {
      revalidate: 3600, // Cache for 1 hour since settings rarely change
    });
  },
};