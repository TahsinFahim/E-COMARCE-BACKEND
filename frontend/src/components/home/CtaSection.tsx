import Link from "next/link";
import Image from "next/image";
import { type CtaSection as CtaSectionType } from "@/services/home.service";

const isDev = process.env.NODE_ENV === "development";

interface CtaSectionProps {
  cta: CtaSectionType;
}

export default function CtaSection({ cta }: CtaSectionProps) {
  return (
    <section className="w-full">
      <div className="mx-auto max-w-[1200px] px-4">
        <div
          className="relative rounded-2xl overflow-hidden p-8 md:p-12"
          style={{ backgroundColor: cta.background_color }}
        >
          <div className="relative z-10 max-w-2xl">
            {/* Image */}
            {cta.image && (
              <div className="relative w-20 h-20 mb-4 rounded-lg overflow-hidden">
                <Image
                  src={cta.image}
                  alt={cta.title}
                  fill
                  sizes="80px"
                  className="object-cover"
                  unoptimized={isDev}
                />
              </div>
            )}

            {/* Title */}
            <h2
              className="text-3xl md:text-4xl font-bold mb-2"
              style={{ color: cta.text_color }}
            >
              {cta.title}
            </h2>

            {/* Subtitle */}
            {cta.subtitle && (
              <p
                className="text-lg md:text-xl font-semibold mb-3"
                style={{ color: cta.text_color, opacity: 0.8 }}
              >
                {cta.subtitle}
              </p>
            )}

            {/* Description */}
            {cta.description && (
              <p
                className="text-sm md:text-base mb-6 leading-relaxed"
                style={{ color: cta.text_color, opacity: 0.7 }}
              >
                {cta.description}
              </p>
            )}

            {/* Button */}
            <Link
              href={cta.button_link}
              className="inline-flex items-center px-6 py-3 rounded-lg font-semibold text-sm transition-all hover:opacity-90 hover:scale-105"
              style={{
                backgroundColor: cta.button_color,
                color: cta.button_text_color,
              }}
              aria-label={cta.button_text}
            >
              {cta.button_text}
              <svg className="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}