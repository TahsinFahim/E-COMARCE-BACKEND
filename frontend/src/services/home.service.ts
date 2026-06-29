import { api } from "@/lib/api";

// ===== Response Types =====

export interface HomeApiResponse {
  success: boolean;
  message: string;
  data: HomeSection[];
}

export type HomeSection = CategorySection | CtaSection;

// ===== Category Section (type: "category_section") =====

export interface CategorySection {
  type: "category_section";
  category: {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    image: string | null;
  };
  products: HomeProduct[];
}

export interface HomeProduct {
  id: number;
  name: string;
  slug: string;
  short_description: string | null;
  main_image: string | null;
  price: number | null;
  product_type: string;
}

// ===== CTA Section (type: "cta_section") =====

export interface CtaSection {
  type: "cta_section";
  id: number;
  title: string;
  subtitle: string | null;
  description: string | null;
  image: string | null;
  button_text: string;
  button_link: string;
  background_color: string;
  text_color: string;
  button_color: string;
  button_text_color: string;
}

// ===== Service =====

export const homeService = {
  async getHomePageData(params?: {
    limit_categories?: number;
    limit_products?: number;
  }): Promise<HomeSection[]> {
    const query = new URLSearchParams();
    if (params?.limit_categories) query.set("limit_categories", String(params.limit_categories));
    if (params?.limit_products) query.set("limit_products", String(params.limit_products));
    const qs = query.toString();
    console.log("homeService.getHomePageData: query", qs);
    const endpoint = `/home/products-by-category${qs ? `?${qs}` : ""}`;
    const res = await api<HomeApiResponse>(
      endpoint,
      {
        revalidate: 3600, // ISR: revalidate every hour
        tags: ["home-page"],
      }
    );

    // Log a small sample of the response to help debug missing images
    try {
      console.log("homeService.getHomePageData: response sample", JSON.stringify(res.data?.slice?.(0,1), null, 2));
    } catch (e) {
      // ignore logging failures
    }

    return res.data;
  },
};