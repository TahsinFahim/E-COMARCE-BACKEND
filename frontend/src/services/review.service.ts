import { api } from "@/lib/api";

export interface Review {
  id: number;
  user_name: string;
  rating: number;
  title: string;
  body: string;
  is_verified_purchase: boolean;
  created_at: string;
}

export interface ReviewResponse {
  success: boolean;
  data: Review[];
  average_rating: number;
  total_reviews: number;
  message?: string;
}

export const reviewService = {
  /**
   * Get reviews for a product
   */
  async getProductReviews(productId: number): Promise<ReviewResponse> {
    return api<ReviewResponse>(`/products/${productId}/reviews`, {
      revalidate: 60, // Cache for 1 minute
    });
  },

  /**
   * Submit a new review
   */
  async submitReview(productId: number, reviewData: {
    rating: number;
    title: string;
    body: string;
  }): Promise<ReviewResponse> {
    return api<ReviewResponse>(`/products/${productId}/reviews`, {
      method: 'POST',
      body: JSON.stringify({
        product_id: productId,
        ...reviewData,
      }),
    });
  },
};