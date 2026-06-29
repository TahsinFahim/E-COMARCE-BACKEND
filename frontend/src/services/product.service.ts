import { api } from "@/lib/api";

export interface Product {
  id: number;
  name: string;
  slug: string;
  price: number;
  sale_price?: number;
  thumbnail?: string;
  category?: string;
  stock_status?: string;
}

export interface ProductSearchResponse {
  success: boolean;
  message: string;
  data: Product[];
}

export const productService = {
  async search(
    query: string,
    categoryId?: number
  ): Promise<ProductSearchResponse> {
    const params = new URLSearchParams();
    params.set("q", query);
    if (categoryId !== undefined && categoryId > 0) {
      params.set("category_id", String(categoryId));
    }

    return api<ProductSearchResponse>(`/products/search?${params.toString()}`, {
      revalidate: 0, // never cache search results
    });
  },
};
