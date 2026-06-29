import { api } from "@/lib/api";

export interface Category {
  id: number;
  name: string;
  slug: string;
  image?: string;
  status: string;
}

export interface CategoryResponse {
  success: boolean;
  message: string;
  data: Category[];
}

export const categoryService = {
  async getAll(): Promise<CategoryResponse> {
    return api<CategoryResponse>("/categories", {
      // revalidate: 60,
    });
  },
};