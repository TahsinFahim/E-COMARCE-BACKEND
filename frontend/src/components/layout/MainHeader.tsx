"use client";

import { useState, useCallback, useRef, useEffect } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import {
  Search, Heart, ShoppingCart, User, ChevronDown, Menu, X,
  Truck, Grid3X3, LogOut, Shield, PackagePlus,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
  Sheet, SheetContent, SheetTrigger,
} from "@/components/ui/sheet";
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem,
  DropdownMenuTrigger, DropdownMenuSeparator,
} from "@/components/ui/dropdown-menu";
import SearchOverlay from "./SearchOverlay";
import RequestProductModal from "@/components/product/RequestProductModal";
import { type Category } from "@/services/category.service";
import { type Settings } from "./NavbarServer";
import { useAppDispatch, useAppSelector } from "@/lib/hooks";
import { clearUser } from "@/lib/features/auth/authSlice";
import { selectCartCount, toggleCart } from "@/lib/features/cart/cartSlice";
import {
  fetchWishlistItems,
  selectWishlistCount,
} from "@/lib/features/wishlist/wishlistSlice";
import { logoutUserAction } from "@/app/actions/auth";
import { removeClientToken } from "@/services/auth.service";

interface MainHeaderProps {
  serverCategories: Category[];
  serverSettings?: Settings;
}

export default function MainHeader({ serverCategories, serverSettings }: MainHeaderProps) {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [mobileSearchOpen, setMobileSearchOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [mobileSearchQuery, setMobileSearchQuery] = useState("");
  const [selectedCategoryId, setSelectedCategoryId] = useState<number | undefined>(undefined);
  const [selectedCategoryName, setSelectedCategoryName] = useState("All Categories");
  const [mobileCategoryId, setMobileCategoryId] = useState<number | undefined>(undefined);
  const [mobileCategoryName, setMobileCategoryName] = useState("All Categories");
  const [requestModalOpen, setRequestModalOpen] = useState(false);
  const desktopInputRef = useRef<HTMLInputElement>(null);
  const mobileSearchRef = useRef<HTMLInputElement>(null);

  const dispatch = useAppDispatch();
  const { user, isAuthenticated } = useAppSelector((state) => state.auth);
  const cartCount = useAppSelector(selectCartCount);
  const wishlistCount = useAppSelector(selectWishlistCount);
  const router = useRouter();

  // Double-check authentication from localStorage (survives page refreshes)
  const isLoggedIn = isAuthenticated || (typeof window !== "undefined" && !!localStorage.getItem("auth_token"));

  const categories = serverCategories;

  useEffect(() => {
    if (user) {
      dispatch(fetchWishlistItems());
    }
  }, [dispatch, user]);

  const defaultCategories: Category[] = [
    "Electronics", "Clothing", "Home & Kitchen", "Sports", "Books", "Toys"
  ].map((name, idx) => ({
    id: idx + 1, name,
    slug: name.toLowerCase().replace(/ & /g, "-").replace(/ /g, "-"),
    status: "active" as const,
  }));

  const displayCategories = categories.length > 0 ? categories : defaultCategories;

  const handleSelectCategory = useCallback((cat: Category | null) => {
    cat ? (setSelectedCategoryId(cat.id), setSelectedCategoryName(cat.name))
      : (setSelectedCategoryId(undefined), setSelectedCategoryName("All Categories"));
  }, []);

  const handleCloseSearch = useCallback(() => {
    setSearchOpen(false);
    setSearchQuery("");
  }, []);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === "k") {
        e.preventDefault();
        desktopInputRef.current?.focus();
      }
    };
    document.addEventListener("keydown", handleKeyDown);
    return () => document.removeEventListener("keydown", handleKeyDown);
  }, []);

  useEffect(() => {
    if (mobileSearchOpen && mobileSearchRef.current) mobileSearchRef.current.focus();
  }, [mobileSearchOpen]);

  const handleMobileSelectCategory = useCallback((cat: Category | null) => {
    cat ? (setMobileCategoryId(cat.id), setMobileCategoryName(cat.name))
      : (setMobileCategoryId(undefined), setMobileCategoryName("All Categories"));
  }, []);

  return (
    <div className="border-b border-gray-100">
      <div className="mx-auto flex h-20 max-w-[1200px] items-center justify-between px-4">
        <Link href="/" className="flex items-center gap-0.5 shrink-0">
          {serverSettings?.site_logo ? (
            <img src={serverSettings.site_logo} alt={serverSettings.site_name || "Shopio"} className="h-10 w-auto object-contain" />
          ) : (
            <>
              <span className="text-[30px] font-bold tracking-tight text-[#111827]">
                {serverSettings?.site_name || "Shopio"}
              </span>
              <span className="text-[38px] font-bold text-[var(--color-primary)]">.</span>
            </>
          )}
        </Link>

        {/* Search Desktop */}
        <div className="relative hidden flex-1 items-center justify-center gap-0 px-6 lg:flex">
          {/* Category Dropdown */}
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <button className="flex h-10 w-[180px] items-center gap-2 rounded-l-[10px] border border-[#E5E7EB] bg-[#F8FAFC] px-4 text-sm font-medium hover:bg-gray-100 transition-colors">
                <Grid3X3 className="h-4 w-4" />
                <span className="truncate">{selectedCategoryName}</span>
                <ChevronDown className="ml-auto h-4 w-4 shrink-0 text-gray-400" />
              </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start" className="w-[220px] border border-gray-100 p-2 shadow-xl max-h-[400px] overflow-y-auto">
              <DropdownMenuItem asChild>
                <button onClick={() => handleSelectCategory(null)}
                  className="flex w-full cursor-pointer items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium hover:bg-[#F0FDF4] hover:text-[var(--color-primary)]">
                  <Grid3X3 className="h-3.5 w-3.5" /> <span>All Categories</span>
                </button>
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              {displayCategories.map((cat) => (
                <DropdownMenuItem key={cat.slug} asChild>
                  <button onClick={() => handleSelectCategory(cat)}
                    className="flex w-full cursor-pointer items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium hover:bg-[#F0FDF4] hover:text-[var(--color-primary)]">
                    <span className="flex h-7 w-7 items-center justify-center rounded-md bg-gray-100 overflow-hidden">
                      {cat.image ? <img src={cat.image} alt={cat.name} className="h-full w-full object-cover" /> : <span className="text-xs">{cat.name.charAt(0)}</span>}
                    </span>
                    <span>{cat.name}</span>
                  </button>
                </DropdownMenuItem>
              ))}
            </DropdownMenuContent>
          </DropdownMenu>

          <div className="relative flex h-10 flex-1 max-w-[550px]">
            <input ref={desktopInputRef} type="text" value={searchQuery}
              onChange={(e) => { setSearchQuery(e.target.value); if (!searchOpen) setSearchOpen(true); }}
              onFocus={() => setSearchOpen(true)}
              placeholder="Search for products..."
              className="h-full w-full rounded-r-[10px] border border-[#E5E7EB] border-l-0 bg-white pl-4 pr-[60px] text-sm text-gray-900 placeholder:text-gray-400 focus:border-gray-300 focus:outline-none"
            />
            <div className="absolute right-0 top-0 flex h-full items-center gap-1.5 pr-2">
              <kbd className="hidden rounded-md border border-gray-200 bg-gray-50 px-1.5 py-0.5 text-[11px] text-gray-400 sm:inline-block">⌘K</kbd>
              <div className="flex h-8 w-8 items-center justify-center rounded-md bg-[var(--color-primary)] text-white">
                <Search className="h-4 w-4" />
              </div>
            </div>
            <SearchOverlay query={searchQuery} onQueryChange={setSearchQuery}
              onClose={handleCloseSearch} isOpen={searchOpen}
              categoryId={selectedCategoryId} inputRef={desktopInputRef} />
          </div>
        </div>

        {/* Right Actions */}
        <div className="hidden items-center gap-8 lg:flex">
          {/* Request a Product */}
          <button onClick={() => setRequestModalOpen(true)} className="relative flex flex-col items-center gap-1 text-[#111827] hover:text-[var(--color-primary)] transition-colors">
            <PackagePlus className="h-5 w-5" />
            <span className="text-xs font-medium">Request</span>
          </button>

          <Link href="/wishlist" className="relative flex flex-col items-center gap-1 text-[#111827] hover:text-[var(--color-primary)] transition-colors">
            <Heart className="h-5 w-5" />
            <span className="text-xs font-medium">Wishlist</span>
            {wishlistCount > 0 && (
              <Badge className="absolute -top-1.5 -right-2.5 flex h-5 w-5 items-center justify-center rounded-full bg-[var(--color-primary)] p-0 text-[10px] font-bold text-white">
                {wishlistCount > 99 ? "99+" : wishlistCount}
              </Badge>
            )}
          </Link>

          <button onClick={() => dispatch(toggleCart())} className="relative flex flex-col items-center gap-1 text-[#111827] hover:text-[var(--color-primary)] transition-colors">
            <ShoppingCart className="h-5 w-5" />
            <span className="text-xs font-medium">Cart</span>
            {cartCount > 0 && (
              <Badge className="absolute -top-1.5 -right-2.5 flex h-5 w-5 items-center justify-center rounded-full bg-[var(--color-primary)] p-0 text-[10px] font-bold text-white">
                {cartCount > 99 ? "99+" : cartCount}
              </Badge>
            )}
          </button>

          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <button className="flex flex-col items-center gap-1 text-[#111827] hover:text-[var(--color-primary)] transition-colors">
                <User className="h-5 w-5" />
                <span className="text-xs font-medium">Account</span>
              </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-[200px] border border-gray-100 p-2 shadow-xl">
              {isLoggedIn ? (
                <>
                  <div className="px-3 py-2 border-b border-gray-100 mb-1">
                    <p className="text-sm font-semibold text-gray-900 truncate">{user?.first_name ?? "User"} {user?.last_name ?? ""}</p>
                    <p className="text-xs text-gray-500 truncate">{user?.email ?? "Logged in"}</p>
                  </div>
                  <DropdownMenuItem asChild>
                    <Link href="/profile" className="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium hover:bg-[#F0FDF4] hover:text-[var(--color-primary)]">
                      <User className="h-4 w-4" /> <span>My Profile</span>
                    </Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem asChild>
                    <Link href="/orders" className="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium hover:bg-[#F0FDF4] hover:text-[var(--color-primary)]">
                      <Truck className="h-4 w-4" /> <span>My Orders</span>
                    </Link>
                  </DropdownMenuItem>
                  <DropdownMenuSeparator />
                  <DropdownMenuItem asChild>
                    <button onClick={async () => {
                      await logoutUserAction();
                      removeClientToken();
                      dispatch(clearUser());
                      router.push("/");
                    }} className="flex w-full cursor-pointer items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50">
                      <LogOut className="h-4 w-4" /> <span>Sign Out</span>
                    </button>
                  </DropdownMenuItem>
                </>
              ) : (
                <>
                  <DropdownMenuItem asChild>
                    <Link href="/login" className="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium text-[var(--color-primary)] hover:bg-[#F0FDF4]">
                      <LogOut className="h-4 w-4" /> <span>Sign In</span>
                    </Link>
                  </DropdownMenuItem>
                  <DropdownMenuItem asChild>
                    <Link href="/register" className="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium hover:bg-[#F0FDF4] hover:text-[var(--color-primary)]">
                      <Shield className="h-4 w-4" /> <span>Create Account</span>
                    </Link>
                  </DropdownMenuItem>
                </>
              )}
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        {/* Mobile */}
        <div className="flex items-center gap-3 lg:hidden">
          <button onClick={() => setMobileSearchOpen((prev) => !prev)} className="text-[#111827]">
            <Search className="h-5 w-5" />
          </button>
          <button onClick={() => dispatch(toggleCart())} className="relative text-[#111827]">
            <ShoppingCart className="h-5 w-5" />
            {cartCount > 0 && (
              <Badge className="absolute -top-2 -right-2.5 flex h-4 w-4 items-center justify-center rounded-full bg-[var(--color-primary)] p-0 text-[9px] font-bold text-white">
                {cartCount > 99 ? "99+" : cartCount}
              </Badge>
            )}
          </button>
          <Sheet open={mobileMenuOpen} onOpenChange={setMobileMenuOpen}>
            <SheetTrigger asChild>
              <Button variant="ghost" size="icon" className="text-[#111827]">
                <Menu className="h-6 w-6" />
              </Button>
            </SheetTrigger>
            <SheetContent side="right" className="w-[300px] p-0">
              <div className="flex items-center justify-between border-b px-6 py-4">
                <span className="text-xl font-bold">Menu</span>
                <button onClick={() => setMobileMenuOpen(false)} className="text-gray-400"><X className="h-5 w-5" /></button>
              </div>
              <div className="flex flex-col gap-2 p-6">
                <div className="mb-2">
                  <div className="flex items-center gap-2 px-3 py-2 text-sm font-semibold">
                    <Grid3X3 className="h-4 w-4 text-[var(--color-primary)]" /> <span>Categories</span>
                  </div>
                  <div className="ml-2 flex flex-col gap-1 border-l-2 border-[#E5E7EB] pl-3">
                    {displayCategories.map((cat) => (
                      <Link key={cat.slug} href={`/category/${cat.slug}`}
                        onClick={() => setMobileMenuOpen(false)}
                        className="rounded-lg px-3 py-2 text-sm font-medium hover:bg-[#F0FDF4] hover:text-[var(--color-primary)]">{cat.name}</Link>
                    ))}
                  </div>
                </div>
                <hr className="my-2 border-gray-100" />
                <div className="flex flex-col gap-2">
                  <Link href="/wishlist" onClick={() => setMobileMenuOpen(false)} className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-[#F8FAFC]">
                    <Heart className="h-5 w-5" /> Wishlist
                  </Link>
                  <Link href="/cart" onClick={() => setMobileMenuOpen(false)}
                    className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-[#F8FAFC]">
                    <ShoppingCart className="h-5 w-5" /> Cart
                  </Link>
                  <button onClick={() => { setMobileMenuOpen(false); setRequestModalOpen(true); }} className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-[#F8FAFC]">
                    <PackagePlus className="h-5 w-5" /> Request a Product
                  </button>
                  <button className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium hover:bg-[#F8FAFC]">
                    <User className="h-5 w-5" /> Account
                  </button>
                </div>
              </div>
            </SheetContent>
          </Sheet>
        </div>
      </div>

      {mobileSearchOpen && (
        <div className="relative border-t border-gray-100 bg-white px-4 pb-3 pt-2 lg:hidden">
          <div className="flex gap-2">
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <button className="flex h-11 shrink-0 items-center gap-1.5 rounded-lg border border-[#E5E7EB] bg-[#F8FAFC] px-3 text-xs font-medium hover:bg-gray-100">
                  <Grid3X3 className="h-3.5 w-3.5" />
                  <span className="max-w-[60px] truncate">{mobileCategoryName === "All Categories" ? "All" : mobileCategoryName}</span>
                  <ChevronDown className="h-3 w-3 shrink-0 text-gray-400" />
                </button>
              </DropdownMenuTrigger>
            <DropdownMenuContent align="start" className="w-[200px] border border-gray-100 p-2 shadow-xl max-h-[300px] overflow-y-auto">
                <DropdownMenuItem asChild>
                  <button onClick={() => handleMobileSelectCategory(null)}
                    className="flex w-full cursor-pointer items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium hover:bg-[#F0FDF4] hover:text-[var(--color-primary)]">
                    <Grid3X3 className="h-3.5 w-3.5" /> <span>All Categories</span>
                  </button>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                {displayCategories.map((cat) => (
                  <DropdownMenuItem key={cat.slug} asChild>
                    <button onClick={() => handleMobileSelectCategory(cat)}
                      className="flex w-full cursor-pointer items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium hover:bg-[#F0FDF4] hover:text-[var(--color-primary)]">
                      <span className="flex h-7 w-7 items-center justify-center rounded-md bg-gray-100 overflow-hidden">
                        {cat.image ? <img src={cat.image} alt={cat.name} className="h-full w-full object-cover" /> : <span className="text-xs">{cat.name.charAt(0)}</span>}
                      </span>
                      <span>{cat.name}</span>
                    </button>
                  </DropdownMenuItem>
                ))}
              </DropdownMenuContent>
            </DropdownMenu>
            <div className="relative flex flex-1">
              <input ref={mobileSearchRef} type="text" value={mobileSearchQuery}
                onChange={(e) => setMobileSearchQuery(e.target.value)}
                placeholder="Search..."
                className="h-11 w-full rounded-lg border border-[#E5E7EB] bg-white pl-3 pr-10 text-sm focus:border-gray-300 focus:outline-none" />
            </div>
          </div>
          <SearchOverlay query={mobileSearchQuery} onQueryChange={setMobileSearchQuery}
            onClose={() => { setMobileSearchQuery(""); setMobileSearchOpen(false); }}
            isOpen={mobileSearchOpen && mobileSearchQuery.length >= 2}
            categoryId={mobileCategoryId} inputRef={mobileSearchRef} />
        </div>
      )}

      {/* Request a Product Modal */}
      <RequestProductModal isOpen={requestModalOpen} onClose={() => setRequestModalOpen(false)} />
    </div>
  );
}
