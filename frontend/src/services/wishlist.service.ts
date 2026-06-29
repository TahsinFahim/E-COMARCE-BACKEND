import { api } from "@/lib/api";

export interface WishlistProductItem {
  id: number;
  name: string;
  slug: string;
  image: string | null;
  price: number;
}

export interface WishlistApiResponse {
  status: string;
  message: string;
  wishlist: Array<{
    id: number;
    product_id: number;
    product: {
      id: number;
      name: string;
      slug: string;
      main_image: string | null;
      price: number | null;
    };
  }>;
}

export interface ToggleWishlistResponse {
  status: string;
  message: string;
  action: "added" | "removed";
}

export interface RemoveWishlistResponse {
  status: string;
  message: string;
}

export async function getWishlist(): Promise<WishlistApiResponse> {
  return api<WishlistApiResponse>("/wishlists", {
    revalidate: 0,
  });
}

export async function toggleWishlist(productId: number): Promise<ToggleWishlistResponse> {
  return api<ToggleWishlistResponse>("/wishlists/toggle", {
    method: "POST",
    body: JSON.stringify({ product_id: productId }),
  });
}

export async function removeWishlistItem(productId: number): Promise<RemoveWishlistResponse> {
  return api<RemoveWishlistResponse>(`/wishlists/${productId}`, {
    method: "DELETE",
  });
}
