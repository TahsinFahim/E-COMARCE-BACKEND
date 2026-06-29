import { subnavbarService } from "@/services/subnavbar.service";
import SubnavbarProductsPage from "@/components/subnavbar/SubnavbarProductsPage";
import type { Metadata } from "next";
import { notFound } from "next/navigation";

export const dynamic = "force-dynamic";

interface Props {
  params: Promise<{ slug: string }>;
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;

  try {
    const data = await subnavbarService.getProducts(slug, { per_page: 1 });
    return {
      title: `${data.subnavbar.name} | Shopio`,
      description: `Browse ${data.subnavbar.name} products at Shopio. Best prices, fast delivery.`,
    };
  } catch {
    return {
      title: "Category | Shopio",
      description: "Browse products by category",
    };
  }
}

export default async function SubnavbarPage({ params }: Props) {
  const { slug } = await params;

  let initialData;
  try {
    initialData = await subnavbarService.getProducts(slug);
  } catch {
    notFound();
  }

  return <SubnavbarProductsPage slug={slug} initialData={initialData} />;
}