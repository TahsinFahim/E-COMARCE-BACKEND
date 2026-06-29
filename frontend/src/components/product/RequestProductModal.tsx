"use client";

import { useState, useRef, type FormEvent } from "react";
import { X, Upload, Package } from "lucide-react";
import { Button } from "@/components/ui/button";
import { submitProductRequest } from "@/services/product-request.service";

interface RequestProductModalProps {
  isOpen: boolean;
  onClose: () => void;
  prefillProductName?: string;
}

export default function RequestProductModal({
  isOpen,
  onClose,
  prefillProductName,
}: RequestProductModalProps) {
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [imageName, setImageName] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  if (!isOpen) return null;

  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setLoading(true);
    setError(null);
    setSuccess(null);

    const form = e.currentTarget;
    const formData = new FormData(form);

    try {
      const result = await submitProductRequest({
        customer_name: (formData.get("customer_name") as string) || "",
        customer_email: (formData.get("customer_email") as string) || "",
        customer_phone: (formData.get("customer_phone") as string) || undefined,
        product_name: (formData.get("product_name") as string) || "",
        product_description: (formData.get("product_description") as string) || undefined,
        product_image: (formData.get("product_image") as File) || undefined,
        quantity: formData.get("quantity")
          ? parseInt(formData.get("quantity") as string)
          : undefined,
        expected_price: formData.get("expected_price")
          ? parseFloat(formData.get("expected_price") as string)
          : undefined,
        notes: (formData.get("notes") as string) || undefined,
      });

      setSuccess(result.message || "Your request has been submitted!");
      form.reset();
      setImageName(null);
    } catch (err: any) {
      setError(err.message || "Failed to submit. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4" onClick={onClose}>
      <div className="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl" onClick={(e) => e.stopPropagation()}>
        {/* Header */}
        <div className="flex items-center justify-between border-b px-6 py-4">
          <div className="flex items-center gap-3">
            <div className="flex h-10 w-10 items-center justify-center rounded-full bg-[#F0FDF4]">
              <Package className="h-5 w-5 text-[var(--color-primary)]" />
            </div>
            <div>
              <h2 className="text-lg font-bold text-gray-900">Request a Product</h2>
              <p className="text-xs text-gray-500">Let us know what you're looking for</p>
            </div>
          </div>
          <button
            onClick={onClose}
            className="flex h-8 w-8 items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
          >
            <X className="h-5 w-5" />
          </button>
        </div>

        {/* Body */}
        <form onSubmit={handleSubmit} className="space-y-4 p-6 max-h-[70vh] overflow-y-auto">
          {success && (
            <div className="rounded-lg bg-green-50 border border-green-200 p-4">
              <p className="text-sm font-medium text-green-800">{success}</p>
            </div>
          )}

          {error && (
            <div className="rounded-lg bg-red-50 border border-red-200 p-4">
              <p className="text-sm font-medium text-red-800">{error}</p>
            </div>
          )}

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Your Name <span className="text-red-500">*</span>
              </label>
              <input
                type="text"
                name="customer_name"
                required
                className="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                placeholder="Enter your name"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Email <span className="text-red-500">*</span>
              </label>
              <input
                type="email"
                name="customer_email"
                required
                className="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                placeholder="your@email.com"
              />
            </div>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Phone (optional)
              </label>
              <input
                type="tel"
                name="customer_phone"
                className="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                placeholder="01XXXXXXXXX"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Quantity
              </label>
              <input
                type="number"
                name="quantity"
                min="1"
                defaultValue="1"
                className="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Product Name <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              name="product_name"
              required
              defaultValue={prefillProductName || ""}
              className="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
              placeholder="What product are you looking for?"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Product Description (optional)
            </label>
            <textarea
              name="product_description"
              rows={3}
              className="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
              placeholder="Describe the product you want (brand, model, size, color, etc.)"
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Expected Price (optional)
            </label>
            <div className="relative">
              <span className="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">৳</span>
              <input
                type="number"
                name="expected_price"
                min="0"
                step="0.01"
                className="w-full rounded-lg border border-gray-300 pl-8 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
                placeholder="0.00"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Image (optional)
            </label>
            <div
              onClick={() => fileInputRef.current?.click()}
              className="flex cursor-pointer items-center gap-3 rounded-lg border-2 border-dashed border-gray-300 p-4 hover:border-[var(--color-primary)] transition-colors"
            >
              <Upload className="h-5 w-5 text-gray-400" />
              <span className="text-sm text-gray-500">
                {imageName || "Click to upload an image"}
              </span>
            </div>
            <input
              ref={fileInputRef}
              type="file"
              name="product_image"
              accept="image/*"
              className="hidden"
              onChange={(e) => {
                const file = e.target.files?.[0];
                setImageName(file ? file.name : null);
              }}
            />
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Additional Notes (optional)
            </label>
            <textarea
              name="notes"
              rows={2}
              className="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-[var(--color-primary)] focus:border-transparent"
              placeholder="Any other details..."
            />
          </div>

          <div className="flex gap-3 pt-2">
            <Button
              type="button"
              variant="outline"
              onClick={onClose}
              className="flex-1 h-11 rounded-xl"
            >
              Cancel
            </Button>
            <Button
              type="submit"
              disabled={loading}
              className="flex-1 h-11 rounded-xl bg-[var(--color-primary)] hover:bg-[var(--color-primary)] text-white font-semibold"
            >
              {loading ? "Submitting..." : "Submit Request"}
            </Button>
          </div>
        </form>
      </div>
    </div>
  );
}