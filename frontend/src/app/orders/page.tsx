"use client";

import { useState } from "react";
import Link from "next/link";
import { Package, ArrowLeft, Search, ChevronRight } from "lucide-react";
import AuthGuard from "@/components/auth/AuthGuard";

function OrdersContent() {
  const [search, setSearch] = useState("");

  // Sample orders - replace with actual API data
  const orders = [
    { id: "ORD-ABC123", date: "2026-06-20", status: "Delivered", total: 149.99, items: 3 },
    { id: "ORD-DEF456", date: "2026-06-18", status: "Processing", total: 89.99, items: 1 },
    { id: "ORD-GHI789", date: "2026-06-15", status: "Shipped", total: 299.99, items: 5 },
  ];

  const statusColors: Record<string, string> = {
    Delivered: "bg-green-100 text-green-700",
    Processing: "bg-blue-100 text-blue-700",
    Shipped: "bg-purple-100 text-purple-700",
    Cancelled: "bg-red-100 text-red-700",
    Pending: "bg-yellow-100 text-yellow-700",
  };

  const filteredOrders = orders.filter(o =>
    o.id.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-[800px] mx-auto px-4">
        <Link href="/" className="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-6">
          <ArrowLeft className="h-4 w-4" /> Back to Home
        </Link>

        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">My Orders</h1>
            <p className="text-sm text-gray-500">View and track your orders</p>
          </div>
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
            <input type="text" value={search} onChange={(e) => setSearch(e.target.value)}
              placeholder="Search orders..." 
              className="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] w-48" />
          </div>
        </div>

        {filteredOrders.length === 0 ? (
          <div className="bg-white rounded-xl border border-gray-100 p-12 text-center">
            <Package className="h-16 w-16 text-gray-200 mx-auto mb-4" />
            <h3 className="text-lg font-semibold text-gray-900 mb-1">No orders found</h3>
            <p className="text-sm text-gray-500 mb-6">Start shopping to see your orders here</p>
            <Link href="/" className="inline-flex items-center gap-2 px-6 py-3 bg-[var(--color-primary)] text-white rounded-xl font-semibold text-sm hover:bg-[var(--color-primary)]">
              Start Shopping
            </Link>
          </div>
        ) : (
          <div className="space-y-4">
            {filteredOrders.map((order) => (
              <div key={order.id} className="bg-white rounded-xl border border-gray-100 p-5 hover:shadow-sm transition-shadow">
                <div className="flex items-center justify-between">
                  <div className="flex items-center gap-4">
                    <div className="w-12 h-12 rounded-lg bg-[#F0FDF4] flex items-center justify-center">
                      <Package className="h-6 w-6 text-[var(--color-primary)]" />
                    </div>
                    <div>
                      <p className="text-sm font-semibold text-gray-900">{order.id}</p>
                      <p className="text-xs text-gray-500">{order.date} • {order.items} items</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-bold text-gray-900">৳{order.total.toFixed(2)}</p>
                    <span className={`inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold ${statusColors[order.status] || "bg-gray-100 text-gray-700"}`}>
                      {order.status}
                    </span>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

export default function OrdersPage() {
  return (
    <AuthGuard>
      <OrdersContent />
    </AuthGuard>
  );
}