import { createSlice, type PayloadAction } from "@reduxjs/toolkit";

export interface CartItem {
  id: number;
  name: string;
  slug: string;
  image: string | null;
  price: number;
  variant_id?: number;
  variant_name?: string;
  variant_option_id?: number;
  quantity: number;
  stock: number;
}

interface CartState {
  items: CartItem[];
  isOpen: boolean;
  animatingItem: { id: number; name: string; image: string | null } | null;
}

// Load cart from localStorage
function loadCart(): CartItem[] {
  if (typeof window === "undefined") return [];
  try {
    const saved = localStorage.getItem("cart_items");
    return saved ? JSON.parse(saved) : [];
  } catch {
    return [];
  }
}

// Save cart to localStorage
function saveCart(items: CartItem[]) {
  if (typeof window === "undefined") return;
  try {
    localStorage.setItem("cart_items", JSON.stringify(items));
  } catch {
    // ignore
  }
}

const initialState: CartState = {
  items: loadCart(),
  isOpen: false,
  animatingItem: null,
};

const cartSlice = createSlice({
  name: "cart",
  initialState,
  reducers: {
    addToCart(state, action: PayloadAction<Omit<CartItem, "quantity">>) {
      const existing = state.items.find(
        (item) =>
          item.id === action.payload.id &&
          item.variant_id === action.payload.variant_id &&
          item.variant_option_id === action.payload.variant_option_id
      );
      if (existing) {
        existing.quantity = Math.min(existing.quantity + 1, 99);
      } else {
        state.items.push({ ...action.payload, quantity: 1 });
      }
      saveCart(state.items);
      // Set animating item for fly animation
      state.animatingItem = {
        id: action.payload.id,
        name: action.payload.name,
        image: action.payload.image,
      };
    },
    addToCartWithQuantity(
      state,
      action: PayloadAction<CartItem & { animation?: boolean }>
    ) {
      const existing = state.items.find(
        (item) =>
          item.id === action.payload.id &&
          item.variant_id === action.payload.variant_id &&
          item.variant_option_id === action.payload.variant_option_id
      );
      if (existing) {
        existing.quantity = Math.min(existing.quantity + action.payload.quantity, 99);
      } else {
        state.items.push({ ...action.payload, quantity: action.payload.quantity });
      }
      saveCart(state.items);
      if (action.payload.animation !== false) {
        state.animatingItem = {
          id: action.payload.id,
          name: action.payload.name,
          image: action.payload.image,
        };
      }
    },
    removeFromCart(state, action: PayloadAction<{ id: number; variant_id?: number; variant_option_id?: number }>) {
      state.items = state.items.filter(
        (item) =>
          !(item.id === action.payload.id && 
            item.variant_id === action.payload.variant_id &&
            item.variant_option_id === action.payload.variant_option_id)
      );
      saveCart(state.items);
    },
    updateQuantity(
      state,
      action: PayloadAction<{ id: number; variant_id?: number; variant_option_id?: number; quantity: number }>
    ) {
      const item = state.items.find(
        (item) =>
          item.id === action.payload.id &&
          item.variant_id === action.payload.variant_id &&
          item.variant_option_id === action.payload.variant_option_id
      );
      if (item) {
        item.quantity = Math.max(1, Math.min(99, action.payload.quantity));
      }
      saveCart(state.items);
    },
    clearCart(state) {
      state.items = [];
      saveCart(state.items);
    },
    setCartItems(state, action: PayloadAction<CartItem[]>) {
      state.items = action.payload;
      saveCart(state.items);
    },
    toggleCart(state) {
      state.isOpen = !state.isOpen;
    },
    setCartOpen(state, action: PayloadAction<boolean>) {
      state.isOpen = action.payload;
    },
    clearAnimation(state) {
      state.animatingItem = null;
    },
  },
});

export const {
  addToCart,
  addToCartWithQuantity,
  removeFromCart,
  updateQuantity,
  clearCart,
  setCartItems,
  toggleCart,
  setCartOpen,
  clearAnimation,
} = cartSlice.actions;

export const selectCartItems = (state: { cart: CartState }) => state.cart.items;
export const selectCartCount = (state: { cart: CartState }) =>
  state.cart.items.reduce((sum, item) => sum + item.quantity, 0);
export const selectCartTotal = (state: { cart: CartState }) =>
  state.cart.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
export const selectCartOpen = (state: { cart: CartState }) => state.cart.isOpen;
export const selectAnimatingItem = (state: { cart: CartState }) => state.cart.animatingItem;

export default cartSlice.reducer;