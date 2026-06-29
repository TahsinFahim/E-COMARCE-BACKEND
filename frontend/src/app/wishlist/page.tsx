"use client";

import Link from "next/link";
import { Heart, ArrowLeft } from "lucide-react";
import { useEffect } from "react";
import { useAppDispatch, useAppSelector } from "@/lib/hooks";
import {
  fetchWishlistItems,
  removeWishlistItem,
  selectWishlistItems,
} from "@/lib/features/wishlist/wishlistSlice";

export default function WishlistPage() {
  const dispatch = useAppDispatch();
  const items = useAppSelector(selectWishlistItems);

  useEffect(() => {
    dispatch(fetchWishlistItems());
  }, [dispatch]);

  if (items.length === 0) {
    return (
      <div className="min-h-screen bg-gray-50 py-16 px-4">
        <div className="mx-auto max-w-3xl rounded-3xl border border-gray-200 bg-white p-10 text-center shadow-sm">
          <Heart className="mx-auto h-12 w-12 text-[var(--color-primary)]" />
          <h1 className="mt-6 text-3xl font-bold text-gray-900">Your wishlist is empty</h1>
          <p className="mt-3 text-sm text-gray-500">
            Save products to your wishlist and view them here anytime.
          </p>
          <Link
            href="/"
            className="mt-8 inline-flex items-center justify-center rounded-full bg-[var(--color-primary)] px-6 py-3 text-sm font-semibold text-white hover:bg-[var(--color-primary)] transition-colors"
          >
            Continue shopping
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-10 px-4">
      <div className="mx-auto max-w-5xl">
        <div className="mb-8 flex flex-col gap-3 rounded-3xl border border-gray-200 bg-white p-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 className="text-3xl font-bold text-gray-900">My Wishlist</h1>
            <p className="mt-2 text-sm text-gray-500">
              Easily manage products you've saved for later.
            </p>
          </div>
          <Link
            href="/"
            className="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
          >
            <ArrowLeft className="h-4 w-4" /> Continue shopping
          </Link>
        </div>

        <div className="space-y-4">
          {items.map((item) => (
            <div key={item.id} className="flex flex-col gap-4 rounded-3xl border border-gray-200 bg-white p-6 sm:flex-row sm:items-center sm:justify-between">
              <div className="flex items-center gap-4">
                <div className="relative h-24 w-24 overflow-hidden rounded-3xl bg-gray-100">
                  {item.image ? (
                    <img
                      src={item.image}
                      alt={item.name}
                      className="h-full w-full object-cover"
                    />
                  ) : (
                    <div className="flex h-full items-center justify-center text-gray-400">
                      <Heart className="h-8 w-8" />
                    </div>
                  )}
                </div>
                <div>
                  <h2 className="text-lg font-semibold text-gray-900">{item.name}</h2>
                  <p className="mt-2 text-sm text-gray-500">৳{item.price.toLocaleString("en-BD")}</p>
                </div>
              </div>

              <div className="flex flex-col gap-3 sm:items-end">
                <button
                  onClick={() => dispatch(removeWishlistItem(item.id))}
                  className="rounded-full bg-red-50 px-5 py-2 text-sm font-semibold text-red-600 hover:bg-red-100 transition-colors"
                >
                  Remove
                </button>
                <Link
                  href={`/product/${item.slug}`}
                  className="rounded-full bg-[var(--color-primary)] px-5 py-2 text-sm font-semibold text-white hover:bg-[var(--color-primary)] transition-colors"
                >
                  View Product
                </Link>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
