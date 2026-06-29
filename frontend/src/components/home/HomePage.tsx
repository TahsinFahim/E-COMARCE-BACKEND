"use client";

import CategorySection from "./CategorySection";
import CtaSection from "./CtaSection";
import { type HomeSection } from "@/services/home.service";

interface HomePageProps {
  sections: HomeSection[];
}

export default function HomePage({ sections }: HomePageProps) {
  
  if (sections.length === 0) {
    return (
      <div className="py-16 text-center text-gray-400">
        <p className="text-lg">No content available at the moment.</p>
        <p className="text-sm mt-2">Please check back later.</p>
      </div>
    );
  }

  return (
    <div className="space-y-12 py-8">
      {sections.map((section, index) => {
        if (section.type === "category_section") {
          return (
            <CategorySection
              key={`cat-${section.category.id}-${index}`}
              category={section.category}
              products={section.products}
            />
          );
        }
        if (section.type === "cta_section") {
          return (
            <CtaSection
              key={`cta-${section.id}-${index}`}
              cta={section}
            />
          );
        }
        return null;
      })}
    </div>
  );
}