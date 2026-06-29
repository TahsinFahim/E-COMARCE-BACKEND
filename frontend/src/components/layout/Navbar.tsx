import TopHeaderBar from "./TopHeaderBar";
import MainHeader from "./MainHeader";
import { type Category } from "@/services/category.service";
import { type AnnouncementBar } from "@/services/announcement-bar.service";
import { type Settings } from "./NavbarServer";

interface NavbarProps {
  children?: React.ReactNode;
  serverCategories: Category[];
  serverAnnouncementBars?: AnnouncementBar[];
  serverSettings?: Settings;
}

export default function Navbar({ children, serverCategories, serverAnnouncementBars, serverSettings }: NavbarProps) {
  return (
    <>
      <TopHeaderBar serverAnnouncementBars={serverAnnouncementBars} />
      <MainHeader serverCategories={serverCategories} serverSettings={serverSettings} />
      {children}
    </>
  );
}

