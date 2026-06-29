"use client";

import Link from "next/link";
import Image from "next/image";
import { useRef } from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { type Category } from "@/services/category.service";

interface CategoryScrollProps {
  categories: Category[];
}

export default function CategoryScroll({
  categories,
}: CategoryScrollProps) {
  const scrollRef = useRef<HTMLDivElement>(null);

  const scroll = (direction: "left" | "right") => {
    if (!scrollRef.current) return;

    scrollRef.current.scrollBy({
      left: direction === "left" ? -400 : 400,
      behavior: "smooth",
    });
  };

  return (
    <section className="w-full py-6" aria-label="Shop by category">
      <div className="mx-auto max-w-[1200px] px-4">
        <div className="relative">
          {/* Left Button */}
          <button
            onClick={() => scroll("left")}
            className="absolute left-0 top-1/2 z-10 hidden h-10 w-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-gray-200 bg-white shadow-md transition-all hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] md:flex"
            aria-label="Scroll categories left"
          >
            <ChevronLeft className="h-5 w-5" />
          </button>

          {/* Categories */}
          <div
            ref={scrollRef}
            className="
              grid
              grid-flow-col
              auto-cols-[90px]
              gap-5
              overflow-x-auto
              scroll-smooth
              pb-2
              scrollbar-hide
            "
            style={{
              scrollbarWidth: "none",
              msOverflowStyle: "none",
            }}
          >
            {categories.map((category) => (
              <Link
                key={category.id}
                href={`/category/${category.slug}`}
                className="group flex w-[200px] shrink-0 flex-col items-center gap-2"
                aria-label={`Shop ${category.name}`}
              >
                <div className="relative h-[72px] w-[72px] overflow-hidden rounded-full bg-gray-100 shadow-sm ring-2 ring-transparent transition-all duration-200 group-hover:ring-[var(--color-primary)] group-hover:shadow-md">
                  {category.image ? (
                    <Image
                      src={category.image}
                      alt={category.name}
                      fill
                      sizes="72px"
                      className="object-cover transition-transform duration-300 group-hover:scale-110"
                      unoptimized
                    />
                  ) : (
                    <div className="flex h-full w-full items-center justify-center bg-gradient-to-br from-[var(--color-primary)]/10 to-[var(--color-primary)]/10 text-xl font-bold text-[var(--color-primary)]">
                      {category.name.charAt(0)}
                    </div>
                  )}
                </div>

                <span className="line-clamp-2 text-center text-xs font-medium leading-tight text-gray-700 transition-colors group-hover:text-[var(--color-primary)]">
                  {category.name}
                </span>
              </Link>
            ))}
          </div>

          {/* Right Button */}
          <button
            onClick={() => scroll("right")}
            className="absolute right-0 top-1/2 z-10 hidden h-10 w-10 translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-gray-200 bg-white shadow-md transition-all hover:border-[var(--color-primary)] hover:text-[var(--color-primary)] md:flex"
            aria-label="Scroll categories right"
          >
            <ChevronRight className="h-5 w-5" />
          </button>
        </div>
      </div>
    </section>
  );
}