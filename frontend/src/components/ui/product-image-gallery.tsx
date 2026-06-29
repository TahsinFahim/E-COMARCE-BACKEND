"use client";

import { useState, useRef, useCallback, useEffect } from "react";
import Image from "next/image";
import { Package } from "lucide-react";
import { cn } from "@/lib/utils";
import {
  Carousel,
  CarouselContent,
  CarouselItem,
} from "@/components/ui/carousel";

const isDev = process.env.NODE_ENV === "development";

// ─── Types ───────────────────────────────────────────────────────────────────
export interface GalleryImage {
  id: number | string;
  url: string;
  alt_text?: string | null;
  is_main?: boolean;
  sort_order?: number;
}

interface ProductImageGalleryProps {
  images: GalleryImage[];
  productName: string;
  /** Optional className override for the main container */
  className?: string;
}

// ─── Image Zoom Hook ─────────────────────────────────────────────────────────
interface ZoomState {
  /** Whether the zoom lens is visible */
  show: boolean;
  /** Cursor position (percentage of container) */
  x: number;
  y: number;
  /** Natural size of the image for background sizing */
  naturalWidth: number;
  naturalHeight: number;
}

function useImageZoom(zoomFactor = 2.5) {
  const containerRef = useRef<HTMLDivElement>(null);
  const imageRef = useRef<HTMLImageElement>(null);
  const [zoom, setZoom] = useState<ZoomState>({
    show: false,
    x: 50,
    y: 50,
    naturalWidth: 0,
    naturalHeight: 0,
  });

  const handleMouseEnter = useCallback(() => {
    if (imageRef.current) {
      setZoom((prev) => ({
        ...prev,
        show: true,
        naturalWidth: imageRef.current?.naturalWidth || 0,
        naturalHeight: imageRef.current?.naturalHeight || 0,
      }));
    }
  }, []);

  const handleMouseMove = useCallback(
    (e: React.MouseEvent<HTMLDivElement>) => {
      const rect = containerRef.current?.getBoundingClientRect();
      if (!rect) return;
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      setZoom((prev) => ({ ...prev, x: Math.min(100, Math.max(0, x)), y: Math.min(100, Math.max(0, y)) }));
    },
    [],
  );

  const handleMouseLeave = useCallback(() => {
    setZoom((prev) => ({ ...prev, show: false }));
  }, []);

  return {
    containerRef,
    imageRef,
    zoom,
    zoomFactor,
    handlers: {
      onMouseEnter: handleMouseEnter,
      onMouseMove: handleMouseMove,
      onMouseLeave: handleMouseLeave,
    },
  };
}

// ─── Sub-component: Main Image Area with Lens Zoom ──────────────────────────
interface MainImageViewerProps {
  src: string;
  alt: string;
  priority?: boolean;
}

function MainImageViewer({ src, alt, priority = true }: MainImageViewerProps) {
  const { containerRef, imageRef, zoom, zoomFactor, handlers } = useImageZoom(2.5);

  return (
    <div
      ref={containerRef}
      className="relative aspect-square bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 cursor-crosshair select-none"
      {...handlers}
    >
      <Image
        ref={imageRef}
        src={src}
        alt={alt}
        fill
        sizes="(max-width: 1024px) 100vw, 600px"
        className="object-contain p-4 pointer-events-none"
        priority={priority}
        unoptimized={isDev}
        draggable={false}
      />

      {/* Zoom lens overlay */}
      {zoom.show && (
        <div
          className="absolute inset-0 pointer-events-none"
          style={{
            backgroundImage: `url(${src})`,
            backgroundSize: `${zoomFactor * 100}% ${zoomFactor * 100}%`,
            backgroundPosition: `${zoom.x}% ${zoom.y}%`,
            backgroundRepeat: "no-repeat",
            imageRendering: "auto",
          }}
        />
      )}

      {/* Focus ring */}
      {zoom.show && (
        <div
          className="absolute pointer-events-none border-2 border-white/50 bg-white/10 rounded-full shadow-lg"
          style={{
            width: "120px",
            height: "120px",
            left: `calc(${zoom.x}% - 60px)`,
            top: `calc(${zoom.y}% - 60px)`,
            transform: "translate(0, 0)",
            backdropFilter: "blur(1px)",
          }}
        />
      )}
    </div>
  );
}

// ─── Main Component ─────────────────────────────────────────────────────────
export default function ProductImageGallery({
  images,
  productName,
  className,
}: ProductImageGalleryProps) {
  const [activeIndex, setActiveIndex] = useState(0);

  // Sort images by sort_order
  const sorted = [...images].sort(
    (a, b) => (a.sort_order ?? 99) - (b.sort_order ?? 99),
  );

  // If no images, show placeholder
  if (!sorted.length) {
    return (
      <div
        className={cn("space-y-4", className)}
      >
        <div className="relative aspect-square bg-gray-50 rounded-2xl overflow-hidden border border-gray-100">
          <div className="flex h-full items-center justify-center text-gray-300">
            <Package className="h-20 w-20" />
          </div>
        </div>
      </div>
    );
  }

  const current = sorted[activeIndex];

  return (
    <div className={cn("space-y-4", className)}>
      {/* Main Image */}
      <MainImageViewer
        src={current.url}
        alt={current.alt_text || `${productName} ${activeIndex + 1}`}
        priority={activeIndex === 0}
      />

      {/* Thumbnail Carousel */}
      {sorted.length > 1 && (
        <Carousel
          opts={{
            align: "start",
            dragFree: true,
            containScroll: "trimSnaps",
            slidesToScroll: "auto",
          }}
          className="relative"
        >
          <CarouselContent className="-ml-2">
            {sorted.map((img, idx) => (
              <CarouselItem key={img.id} className="min-w-[88px] pl-2">
                <button
                  onClick={() => setActiveIndex(idx)}
                  className={cn(
                    "relative aspect-square w-full overflow-hidden rounded-3xl border-2 transition-all focus-visible:ring-2 focus-visible:ring-[var(--color-primary)]",
                    idx === activeIndex
                      ? "border-[var(--color-primary)] shadow-sm"
                      : "border-slate-200 bg-white hover:border-slate-300",
                  )}
                  aria-label={`View ${img.alt_text || `${productName} image ${idx + 1}`}`}
                  aria-current={idx === activeIndex ? "true" : undefined}
                >
                  <Image
                    src={img.url}
                    alt={img.alt_text || `${productName} ${idx + 1}`}
                    fill
                    sizes="88px"
                    className="object-cover"
                    unoptimized={isDev}
                  />
                </button>
              </CarouselItem>
            ))}
          </CarouselContent>
        </Carousel>
      )}
    </div>
  );
}