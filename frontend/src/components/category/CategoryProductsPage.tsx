"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import Image from "next/image";
import {
  SlidersHorizontal,
  Grid3X3,
  List,
  ChevronLeft,
  ShoppingCart,
  ArrowUpDown,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import ProductCard from "@/components/ui/ProductCard";
import {
  type CategoryProductsData,
  type CategoryProduct,
  SORT_OPTIONS,
} from "@/services/category-products.service";
import { useAppDispatch, useAppSelector } from "@/lib/hooks";
import {
  fetchWishlistItems,
} from "@/lib/features/wishlist/wishlistSlice";

const isDev = process.env.NODE_ENV === "development";

interface CategoryProductsPageProps {
  slug: string;
  initialData: CategoryProductsData;
  categoryName: string;
  categoryDescription: string | null;
  categoryImage: string | null;
}

export default function CategoryProductsPage({
  slug,
  initialData,
  categoryName,
  categoryDescription,
  categoryImage,
}: CategoryProductsPageProps) {
  const dispatch = useAppDispatch();
  const [viewMode, setViewMode] = useState<"grid" | "list">("grid");
  const [sortBy, setSortBy] = useState("latest");
  const [showMobileFilter, setShowMobileFilter] = useState(false);

  const { products = [], category } = initialData;

  useEffect(() => {
    dispatch(fetchWishlistItems());
  }, [dispatch]);

  const displayName = category?.name || categoryName;
  const displayDescription = category?.description || categoryDescription;
  const displayImage = category?.image || categoryImage;

  const breadcrumbSchema = {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    itemListElement: [
      { "@type": "ListItem", position: 1, name: "Home", item: "https://shopio.com" },
      { "@type": "ListItem", position: 2, name: "Categories", item: "https://shopio.com/categories" },
      { "@type": "ListItem", position: 3, name: displayName, item: `https://shopio.com/category/${slug}` },
    ],
  };

  return (
    <>
      {/* Breadcrumb Schema */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />

      <div className="min-h-screen bg-gray-50">
        {/* Category Hero Header */}
        <div className="bg-white border-b border-gray-200">
          <div className="mx-auto max-w-[1200px] px-4">
            {/* Breadcrumb */}
            <nav className="flex items-center gap-2 py-3 text-xs text-gray-500" aria-label="Breadcrumb">
              <Link href="/" className="hover:text-[var(--color-primary)] transition-colors">Home</Link>
              <ChevronLeft className="h-3 w-3 rotate-180" aria-hidden="true" />
              <Link href="/categories" className="hover:text-[var(--color-primary)] transition-colors">Categories</Link>
              <ChevronLeft className="h-3 w-3 rotate-180" aria-hidden="true" />
              <span className="text-gray-900 font-medium">{displayName}</span>
            </nav>

            {/* Category Hero */}
            <div className="flex flex-col md:flex-row items-start md:items-center gap-6 py-6 md:py-8">
              {displayImage && (
                <div className="relative w-20 h-20 md:w-24 md:h-24 shrink-0 rounded-2xl overflow-hidden shadow-md">
                  <Image
                    src={displayImage}
                    alt={displayName}
                    fill
                    sizes="(max-width: 768px) 80px, 96px"
                    className="object-cover"
                    unoptimized={isDev}
                  />
                </div>
              )}
              <div className="flex-1">
                <h1 className="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900">
                  {displayName}
                </h1>
                {displayDescription && (
                  <p className="mt-2 text-sm md:text-base text-gray-500 max-w-2xl leading-relaxed">
                    {displayDescription}
                  </p>
                )}
                <p className="mt-1 text-sm text-gray-400">
                  {products.length} product{products.length !== 1 ? "s" : ""} found
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Main Content Area */}
        <div className="mx-auto max-w-[1200px] px-4 py-6">
          {/* Toolbar */}
          <div className="flex flex-wrap items-center justify-between gap-4 mb-6 bg-white rounded-xl border border-gray-200 p-3 md:p-4">
            {/* Left: View toggle + filter */}
            <div className="flex items-center gap-3">
              <Button
                variant="outline"
                size="sm"
                className="md:hidden flex items-center gap-2 text-gray-700"
                onClick={() => setShowMobileFilter(!showMobileFilter)}
              >
                <SlidersHorizontal className="h-4 w-4" />
                Filters
              </Button>

              <div className="hidden md:flex items-center border border-gray-200 rounded-lg overflow-hidden">
                <button
                  onClick={() => setViewMode("grid")}
                  className={`p-2 transition-colors ${viewMode === "grid" ? "bg-[var(--color-primary)] text-white" : "bg-white text-gray-500 hover:bg-gray-100"}`}
                  aria-label="Grid view"
                >
                  <Grid3X3 className="h-4 w-4" />
                </button>
                <button
                  onClick={() => setViewMode("list")}
                  className={`p-2 transition-colors ${viewMode === "list" ? "bg-[var(--color-primary)] text-white" : "bg-white text-gray-500 hover:bg-gray-100"}`}
                  aria-label="List view"
                >
                  <List className="h-4 w-4" />
                </button>
              </div>
            </div>

            {/* Right: Sort */}
            <div className="flex items-center gap-2">
              <ArrowUpDown className="h-4 w-4 text-gray-400" aria-hidden="true" />
              <span className="text-sm text-gray-500 hidden sm:inline">Sort by:</span>
              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
                className="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                aria-label="Sort products"
              >
                {SORT_OPTIONS.map((opt) => (
                  <option key={opt.value} value={opt.value}>
                    {opt.label}
                  </option>
                ))}
              </select>
            </div>
          </div>

          {/* Products Grid / List */}
          {products.length > 0 ? (
            <div
              className={
                viewMode === "grid"
                  ? "grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4"
                  : "flex flex-col gap-3"
              }
            >
              {products.map((product) => (
                <ProductCard key={product.id} product={product} viewMode={viewMode} />
              ))}
            </div>
          ) : (
            <div className="text-center py-20 bg-white rounded-xl border border-gray-200">
              <ShoppingCart className="mx-auto h-12 w-12 text-gray-300 mb-4" />
              <h3 className="text-lg font-semibold text-gray-900 mb-2">
                No products found
              </h3>
              <p className="text-sm text-gray-500 mb-6">
                This category has no products yet. Check back soon!
              </p>
              <Link
                href="/shop"
                className="inline-flex h-10 items-center rounded-full bg-[var(--color-primary)] px-6 text-sm font-semibold text-white hover:bg-[var(--color-primary)] transition-colors"
              >
                Browse All Products
              </Link>
            </div>
          )}

          {/* Pagination Placeholder */}
          {products.length > 0 && (
            <div className="flex justify-center mt-10">
              <nav className="flex items-center gap-2" aria-label="Pagination">
                <Button variant="outline" size="sm" disabled className="text-gray-400">
                  <ChevronLeft className="h-4 w-4" aria-hidden="true" />
                  <span className="sr-only">Previous</span>
                </Button>
                <Button
                  variant="default"
                  size="sm"
                  className="bg-[var(--color-primary)] hover:bg-[var(--color-primary)] text-white min-w-[36px]"
                >
                  1
                </Button>
                <Button variant="outline" size="sm" disabled className="text-gray-400">
                  <ChevronLeft className="h-4 w-4 rotate-180" aria-hidden="true" />
                  <span className="sr-only">Next</span>
                </Button>
              </nav>
            </div>
          )}
        </div>
      </div>
    </>
  );
}