import { api } from "@/lib/api";

// ===== Raw API Response =====

export interface ProductDetailResponse {
  success: boolean;
  message: string;
  data: ProductDetailData;
}

export interface ProductDetailData {
  id: number;
  name: string;
  slug: string;
  short_description: string | null;
  description: string | null;
  product_type: string;
  status: string;
  visibility: string;
  seo_title: string | null;
  seo_description: string | null;
  published_at: string | null;
  price_range: {
    min: number;
    max: number;
  } | null;
  brand: {
    id: number;
    name: string;
    slug: string;
    logo: string | null;
  } | null;
  categories: {
    id: number;
    name: string;
    slug: string;
  }[];
  main_image: string | null;
  gallery: ProductImage[];
  variants: ProductVariant[];
  attribute_options?: {
    colors: { value: string; hex?: string | null; available_count: number; available: boolean }[];
    sizes: { value: string; available_count: number; available: boolean }[];
  } | null;
  reviews: ProductReviews;
  related_products: RelatedProduct[];
}

export interface ProductImage {
  id: number;
  url: string;
  alt_text: string | null;
  is_main: boolean;
  sort_order: number;
}

export interface VariantOption {
  id: number;
  color_name: string;
  color_code: string | null;
  image_url: string | null;
  price_adjustment: number;
  stock: number;
}

export interface ProductVariant {
  id: number;
  name: string;
  sku: string;
  barcode: string | null;
  sale_price: number;
  compare_at_price: number | null;
  cost_price: number | null;
  stock: number;
  track_inventory: boolean;
  allow_backorder: boolean;
  attributes: Record<string, string> | null;
  image: string | null;
  options: VariantOption[];
}

export interface ProductReviews {
  average_rating: number;
  total_reviews: number;
  rating_distribution: Record<string, number>;
  items: ReviewItem[];
}

export interface ReviewItem {
  id: number;
  rating: number;
  title: string | null;
  body: string | null;
  user_name: string | null;
  is_verified_purchase: boolean;
  created_at: string;
}

export interface RelatedProduct {
  id: number;
  name: string;
  slug: string;
  short_description: string | null;
  main_image: string | null;
  price: string;
  product_type: string;
}

// ===== Service =====

export const productDetailService = {
  async getBySlug(slug: string): Promise<ProductDetailData> {
    const res = await api<ProductDetailResponse>(`/products/${slug}`, {
     
      tags: [`product-${slug}`],
    });
    return res.data;
  },
};