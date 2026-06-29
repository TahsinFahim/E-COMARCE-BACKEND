import { categoryService, type Category } from "@/services/category.service";
import MainHeader from "./MainHeader";

export default async function MainHeaderServer() {
  let categories: Category[] = [];

  try {
    const categoryRes = await categoryService.getAll();
    if (categoryRes.success && categoryRes.data.length > 0) {
      categories = categoryRes.data.filter((c) => c.status === "active");
    }
  } catch {
    // API unavailable
  }

  return <MainHeader serverCategories={categories} />;
}