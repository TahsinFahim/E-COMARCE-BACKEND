import { categoryService, type Category } from "@/services/category.service";
import { announcementBarService, type AnnouncementBar } from "@/services/announcement-bar.service";
import { settingsService } from "@/services/settings.service";
import Navbar from "./Navbar";
import NavigationBarServer from "./NavigationBarServer";

export interface Settings {
  site_name?: string;
  site_logo?: string;
  site_description?: string;
  primary_color?: string;
  facebook_url?: string;
  twitter_url?: string;
  instagram_url?: string;
  youtube_url?: string;
  whatsapp_number?: string;
  phone?: string;
  email?: string;
  address?: string;
  meta_title?: string;
  meta_description?: string;
}

export default async function NavbarServer() {
  let categories: Category[] = [];
  let announcementBars: AnnouncementBar[] = [];
  let settings: Settings = {};

  try {
    const categoryRes = await categoryService.getAll();
    if (categoryRes.success && categoryRes.data.length > 0) {
      categories = categoryRes.data.filter((c) => c.status === "active");
    }
  } catch {
    // API unavailable – render empty/default state
  }

  try {
    const announcementRes = await announcementBarService.getAll();
    if (announcementRes.success && announcementRes.data.length > 0) {
      announcementBars = announcementRes.data;
    }
  } catch {
    // API unavailable – render empty/default state
  }

  try {
    const settingsRes = await settingsService.getAll();
    if (settingsRes.success && settingsRes.data) {
      settings = settingsRes.data as unknown as Settings;
    }
  } catch {
    // API unavailable – render empty/default state
  }

  return (
    <Navbar serverCategories={categories} serverAnnouncementBars={announcementBars} serverSettings={settings}>
      <NavigationBarServer serverCategories={categories} />
    </Navbar>
  );
}
