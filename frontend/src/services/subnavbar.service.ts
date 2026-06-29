import { api } from "@/lib/api";

// ===== Response Types =====

export interface SubnavbarProductsResponse {
  success: boolean;
  message: string;
  data: SubnavbarProductsData;
}

export interface SubnavbarProductsData {
  subnavbar: SubnavbarInfo;
  products: SubnavbarProduct[];
  meta?: PaginationInfo;
}

export interface SubnavbarInfo {
  id: number;
  navbar_item_id: number;
  name: string;
  slug: string;
  description: string | null;
  image: string | null;
}

export interface SubnavbarProduct {
  id: number;
  name: string;
  slug: string;
  short_description: string | null;
  main_image: string | null;
  price: number | null;
  product_type: string;
  stock_status?: string;
}

export interface PaginationInfo {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

// Sort options
export type SubnavbarSortOption = "latest" | "price_asc" | "price_desc" | "name";

export const SORT_OPTIONS: { label: string; value: SubnavbarSortOption }[] = [
  { label: "Latest", value: "latest" },
  { label: "Price: Low to High", value: "price_asc" },
  { label: "Price: High to Low", value: "price_desc" },
  { label: "Name: A-Z", value: "name" },
];

// ===== Service =====

export const subnavbarService = {
  async getProducts(
    slug: string,
    params?: {
      page?: number;
      per_page?: number;
      sort?: SubnavbarSortOption;
    }
  ): Promise<SubnavbarProductsData> {
    const query = new URLSearchParams();

    if (params?.page) query.set("page", String(params.page));
    if (params?.per_page) {
      const clamped = Math.min(Math.max(1, params.per_page), 40);
      query.set("per_page", String(clamped));
    }
    if (params?.sort) query.set("sort", params.sort);

    const qs = query.toString();
    const endpoint = `/subnavbar/${slug}/products${qs ? `?${qs}` : ""}`;

    const res = await api<SubnavbarProductsResponse>(endpoint, {
      revalidate: 120,
      tags: [`subnavbar-products-${slug}`],
    });

    return res.data;
  },
};