import ProductCard from "@/components/ui/ProductCard";
import { type HomeProduct } from "@/services/home.service";
import QuickAddModal from "@/components/cart/QuickAddModal";

interface ProductCardProps {
  product: HomeProduct;
}

export default function HomeProductCard({ product }: ProductCardProps) {
  return <ProductCard product={product} />;
}