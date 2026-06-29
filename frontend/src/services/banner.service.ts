import { api } from "@/lib/api";

export interface Banner {
  id: number;
  smtag?: string;
  title?: string;
  subtitle?: string;
  primary_btn?: string;
  secondary_btn?: string;
  banner_image?: string;
}

export interface BannerResponse {
  success: boolean;
  data: Banner[];
}

export const bannerService = {
  async getAll(): Promise<BannerResponse> {
    return api<BannerResponse>("/banners", {
      revalidate: 60,
    });
  },
};