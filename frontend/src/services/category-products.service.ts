import { api } from "@/lib/api";

// ===== Raw API Response =====

export interface CategoryProductsRawResponse {
  success: boolean;
  message: string;
  data: CategoryInfo;         // category info directly
  products: CategoryProduct[]; // at root level
  meta?: PaginationInfo;       // pagination at root level
}

// ===== Processed Data (what the component gets) =====

export interface CategoryProductsData {
  category: CategoryInfo;
  products: CategoryProduct[];
  meta?: PaginationInfo;
}

export interface PaginationInfo {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface CategoryInfo {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  image: string | null;
  parent_id: number | null;
  products_count?: number;
}

export interface CategoryProduct {
  id: number;
  name: string;
  slug: string;
  short_description: string | null;
  main_image: string | null;
  price: number | null;
  product_type: string;
  stock_status?: string;
  rating?: number;
  review_count?: number;
}

// Sort options matching the API
export type CategorySortOption = "latest" | "price_asc" | "price_desc" | "name";

export const SORT_OPTIONS: { label: string; value: CategorySortOption }[] = [
  { label: "Latest", value: "latest" },
  { label: "Price: Low to High", value: "price_asc" },
  { label: "Price: High to Low", value: "price_desc" },
  { label: "Name: A-Z", value: "name" },
];

// ===== Service =====

export const categoryProductsService = {
  async getBySlug(
    slug: string,
    params?: {
      page?: number;
      per_page?: number;
      sort?: CategorySortOption;
      min_price?: number;
      max_price?: number;
      q?: string;
      refresh?: boolean;
    }
  ): Promise<CategoryProductsData> {
    const query = new URLSearchParams();

    if (params?.page) query.set("page", String(params.page));
    if (params?.per_page) {
      const clamped = Math.min(Math.max(1, params.per_page), 40);
      query.set("per_page", String(clamped));
    }
    if (params?.sort) query.set("sort", params.sort);
    if (params?.min_price !== undefined) query.set("min_price", String(params.min_price));
    if (params?.max_price !== undefined) query.set("max_price", String(params.max_price));
    if (params?.q) query.set("q", params.q);
    if (params?.refresh) query.set("refresh", "1");

    const qs = query.toString();
    const endpoint = `/categories/${slug}/products${qs ? `?${qs}` : ""}`;

    const res = await api<CategoryProductsRawResponse>(endpoint, {
      revalidate: params?.refresh ? 0 : 120,
      tags: [`category-products-${slug}`],
    });

    // Map the actual API structure to what the component expects
    return {
      category: res.data,
      products: res.products || [],
      meta: res.meta,
    };
  },
};