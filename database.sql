-- Professional ecommerce, POS, and delivery database schema.
-- Target: MySQL 8.0+ / InnoDB / utf8mb4
-- Notes:
--   - Uses BIGINT ids for high volume growth.
--   - Adds indexes for common 1M+ user traffic paths.
--   - Stores monetary totals in DECIMAL(19,4) for financial accuracy.
--   - Keeps operational settings flexible through scoped key/value tables.

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS ecommerce_backend
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ecommerce_backend;

-- =========================================================
-- Identity and access
-- =========================================================

CREATE TABLE users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  public_id CHAR(36) NOT NULL,
  first_name VARCHAR(100) NULL,
  last_name VARCHAR(100) NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(32) NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','inactive','blocked','deleted') NOT NULL DEFAULT 'active',
  email_verified_at DATETIME NULL,
  phone_verified_at DATETIME NULL,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_public_id (public_id),
  UNIQUE KEY uq_users_email (email),
  UNIQUE KEY uq_users_phone (phone),
  KEY idx_users_status_deleted_created (status, deleted_at, created_at),
  KEY idx_users_last_login (last_login_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  description VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE permissions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  description VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_permissions_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_permissions (
  role_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_roles (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (user_id, role_id),
  CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_sessions (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  ip_address VARBINARY(16) NULL,
  user_agent VARCHAR(500) NULL,
  expires_at DATETIME NOT NULL,
  revoked_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_user_sessions_token_hash (token_hash),
  KEY idx_user_sessions_user_active (user_id, revoked_at, expires_at),
  KEY idx_user_sessions_expires (expires_at),
  CONSTRAINT fk_user_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Stores, staff, addresses, and settings
-- =========================================================

CREATE TABLE stores (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL,
  email VARCHAR(255) NULL,
  phone VARCHAR(32) NULL,
  status ENUM('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  currency_code CHAR(3) NOT NULL DEFAULT 'USD',
  timezone VARCHAR(64) NOT NULL DEFAULT 'UTC',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_stores_slug (slug),
  KEY idx_stores_status_deleted (status, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE store_staff (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  store_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  staff_code VARCHAR(40) NULL,
  status ENUM('active','inactive','terminated') NOT NULL DEFAULT 'active',
  hired_at DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_store_staff_user (store_id, user_id),
  UNIQUE KEY uq_store_staff_code (store_id, staff_code),
  KEY idx_store_staff_status (store_id, status),
  CONSTRAINT fk_store_staff_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
  CONSTRAINT fk_store_staff_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE countries (
  id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  iso2 CHAR(2) NOT NULL,
  name VARCHAR(100) NOT NULL,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_countries_iso2 (iso2)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE addresses (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,
  store_id BIGINT UNSIGNED NULL,
  label VARCHAR(80) NULL,
  contact_name VARCHAR(160) NULL,
  contact_phone VARCHAR(32) NULL,
  address_line1 VARCHAR(255) NOT NULL,
  address_line2 VARCHAR(255) NULL,
  city VARCHAR(120) NOT NULL,
  state VARCHAR(120) NULL,
  postal_code VARCHAR(32) NULL,
  country_id SMALLINT UNSIGNED NOT NULL,
  latitude DECIMAL(10,7) NULL,
  longitude DECIMAL(10,7) NULL,
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_addresses_user_default (user_id, is_default),
  KEY idx_addresses_store (store_id),
  KEY idx_addresses_location (country_id, city, postal_code),
  KEY idx_addresses_geo (latitude, longitude),
  CONSTRAINT fk_addresses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_addresses_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
  CONSTRAINT fk_addresses_country FOREIGN KEY (country_id) REFERENCES countries(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE app_settings (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  scope_type ENUM('global','store','user') NOT NULL DEFAULT 'global',
  scope_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
  setting_key VARCHAR(120) NOT NULL,
  setting_value JSON NOT NULL,
  is_public TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_app_settings_scope_key (scope_type, scope_id, setting_key),
  KEY idx_app_settings_public (is_public, setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Catalog
-- =========================================================

CREATE TABLE brands (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL,
  logo_url VARCHAR(500) NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_brands_slug (slug),
  KEY idx_brands_status_deleted (status, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  parent_id BIGINT UNSIGNED NULL,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL,
  description TEXT NULL,
  image_url VARCHAR(500) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug),
  KEY idx_categories_parent_sort (parent_id, sort_order),
  KEY idx_categories_status_deleted_sort (status, deleted_at, sort_order),
  CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  brand_id BIGINT UNSIGNED NULL,
  name VARCHAR(220) NOT NULL,
  slug VARCHAR(240) NOT NULL,
  short_description VARCHAR(500) NULL,
  description LONGTEXT NULL,
  product_type ENUM('physical','digital','service','bundle') NOT NULL DEFAULT 'physical',
  status ENUM('draft','active','archived') NOT NULL DEFAULT 'draft',
  visibility ENUM('public','hidden','private') NOT NULL DEFAULT 'public',
  seo_title VARCHAR(255) NULL,
  seo_description VARCHAR(500) NULL,
  published_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_products_slug (slug),
  KEY idx_products_brand_status (brand_id, status),
  KEY idx_products_status_deleted_published (status, deleted_at, visibility, published_at),
  FULLTEXT KEY ft_products_search (name, short_description, description),
  CONSTRAINT fk_products_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_categories (
  product_id BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (product_id, category_id),
  KEY idx_product_categories_category (category_id, product_id),
  CONSTRAINT fk_product_categories_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_product_categories_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_variants (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id BIGINT UNSIGNED NOT NULL,
  sku VARCHAR(100) NOT NULL,
  barcode VARCHAR(100) NULL,
  name VARCHAR(220) NOT NULL,
  attributes JSON NULL,
  cost_price DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  sale_price DECIMAL(19,4) NOT NULL,
  compare_at_price DECIMAL(19,4) NULL,
  weight_grams INT UNSIGNED NULL,
  length_mm INT UNSIGNED NULL,
  width_mm INT UNSIGNED NULL,
  height_mm INT UNSIGNED NULL,
  track_inventory TINYINT(1) NOT NULL DEFAULT 1,
  allow_backorder TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_product_variants_sku (sku),
  UNIQUE KEY uq_product_variants_barcode (barcode),
  KEY idx_product_variants_product_status_deleted (product_id, status, deleted_at),
  KEY idx_product_variants_price (sale_price),
  CONSTRAINT fk_product_variants_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id BIGINT UNSIGNED NOT NULL,
  variant_id BIGINT UNSIGNED NULL,
  image_url VARCHAR(500) NOT NULL,
  alt_text VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_product_images_product_sort (product_id, sort_order),
  KEY idx_product_images_variant (variant_id),
  CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_product_images_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inventory_locations (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  store_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  location_type ENUM('warehouse','retail','delivery_hub') NOT NULL DEFAULT 'warehouse',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_inventory_locations_store_status_deleted (store_id, status, deleted_at),
  CONSTRAINT fk_inventory_locations_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inventory_stock (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  location_id BIGINT UNSIGNED NOT NULL,
  variant_id BIGINT UNSIGNED NOT NULL,
  quantity_on_hand INT NOT NULL DEFAULT 0,
  quantity_reserved INT NOT NULL DEFAULT 0,
  reorder_point INT NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_inventory_stock_location_variant (location_id, variant_id),
  KEY idx_inventory_stock_variant (variant_id),
  KEY idx_inventory_stock_available (location_id, quantity_on_hand, quantity_reserved),
  CONSTRAINT fk_inventory_stock_location FOREIGN KEY (location_id) REFERENCES inventory_locations(id) ON DELETE CASCADE,
  CONSTRAINT fk_inventory_stock_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE inventory_movements (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  location_id BIGINT UNSIGNED NOT NULL,
  variant_id BIGINT UNSIGNED NOT NULL,
  movement_type ENUM('purchase','sale','return','adjustment','transfer_in','transfer_out','reservation','release') NOT NULL,
  quantity INT NOT NULL,
  reference_type VARCHAR(60) NULL,
  reference_id BIGINT UNSIGNED NULL,
  note VARCHAR(500) NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_inventory_movements_variant_created (variant_id, created_at),
  KEY idx_inventory_movements_location_created (location_id, created_at),
  KEY idx_inventory_movements_reference (reference_type, reference_id),
  CONSTRAINT fk_inventory_movements_location FOREIGN KEY (location_id) REFERENCES inventory_locations(id),
  CONSTRAINT fk_inventory_movements_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id),
  CONSTRAINT fk_inventory_movements_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Promotions, carts, wishlist
-- =========================================================

CREATE TABLE coupons (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  code VARCHAR(80) NOT NULL,
  discount_type ENUM('percentage','fixed_amount','free_shipping') NOT NULL,
  discount_value DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  minimum_order_amount DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  usage_limit INT UNSIGNED NULL,
  usage_limit_per_user INT UNSIGNED NULL,
  used_count INT UNSIGNED NOT NULL DEFAULT 0,
  starts_at DATETIME NULL,
  ends_at DATETIME NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_coupons_code (code),
  KEY idx_coupons_status_deleted_dates (status, deleted_at, starts_at, ends_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE carts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NULL,
  session_id VARCHAR(120) NULL,
  store_id BIGINT UNSIGNED NULL,
  status ENUM('active','converted','abandoned') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  expires_at DATETIME NULL,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_carts_user_status_deleted_updated (user_id, status, deleted_at, updated_at),
  KEY idx_carts_session_status (session_id, status),
  KEY idx_carts_expires (expires_at),
  CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_carts_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  cart_id BIGINT UNSIGNED NOT NULL,
  variant_id BIGINT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(19,4) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cart_items_cart_variant (cart_id, variant_id),
  KEY idx_cart_items_variant (variant_id),
  CONSTRAINT fk_cart_items_cart FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE wishlists (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_wishlists_user_product (user_id, product_id),
  KEY idx_wishlists_product (product_id),
  CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_wishlists_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Orders, payment, shipping, delivery
-- =========================================================

CREATE TABLE orders (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_number VARCHAR(40) NOT NULL,
  user_id BIGINT UNSIGNED NULL,
  store_id BIGINT UNSIGNED NULL,
  source ENUM('web','mobile','pos','admin','marketplace') NOT NULL DEFAULT 'web',
  status ENUM('pending','confirmed','processing','ready','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  payment_status ENUM('unpaid','authorized','paid','partially_refunded','refunded','failed') NOT NULL DEFAULT 'unpaid',
  fulfillment_status ENUM('unfulfilled','partial','fulfilled','returned') NOT NULL DEFAULT 'unfulfilled',
  currency_code CHAR(3) NOT NULL DEFAULT 'USD',
  subtotal DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  discount_total DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  tax_total DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  shipping_total DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  grand_total DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  coupon_id BIGINT UNSIGNED NULL,
  billing_address_id BIGINT UNSIGNED NULL,
  shipping_address_id BIGINT UNSIGNED NULL,
  customer_note VARCHAR(1000) NULL,
  placed_at DATETIME NULL,
  cancelled_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_orders_order_number (order_number),
  KEY idx_orders_user_created (user_id, created_at),
  KEY idx_orders_store_status_deleted_created (store_id, status, deleted_at, created_at),
  KEY idx_orders_status_payment_deleted_created (status, payment_status, deleted_at, created_at),
  KEY idx_orders_source_created (source, created_at),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_orders_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL,
  CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE SET NULL,
  CONSTRAINT fk_orders_billing_address FOREIGN KEY (billing_address_id) REFERENCES addresses(id) ON DELETE SET NULL,
  CONSTRAINT fk_orders_shipping_address FOREIGN KEY (shipping_address_id) REFERENCES addresses(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id BIGINT UNSIGNED NULL,
  variant_id BIGINT UNSIGNED NULL,
  sku VARCHAR(100) NOT NULL,
  product_name VARCHAR(220) NOT NULL,
  variant_name VARCHAR(220) NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_price DECIMAL(19,4) NOT NULL,
  discount_total DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  tax_total DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  line_total DECIMAL(19,4) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_order_items_order (order_id),
  KEY idx_order_items_variant_created (variant_id, created_at),
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
  CONSTRAINT fk_order_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  provider VARCHAR(80) NOT NULL,
  provider_payment_id VARCHAR(180) NULL,
  method ENUM('card','cash','bank_transfer','wallet','cod','gift_card','other') NOT NULL,
  status ENUM('pending','authorized','captured','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  amount DECIMAL(19,4) NOT NULL,
  currency_code CHAR(3) NOT NULL DEFAULT 'USD',
  paid_at DATETIME NULL,
  raw_response JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_payments_order_status (order_id, status),
  KEY idx_payments_provider_payment (provider, provider_payment_id),
  KEY idx_payments_status_created (status, created_at),
  CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE refunds (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  payment_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(19,4) NOT NULL,
  reason VARCHAR(500) NULL,
  status ENUM('pending','processed','failed') NOT NULL DEFAULT 'pending',
  processed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_refunds_order (order_id, created_at),
  KEY idx_refunds_payment (payment_id),
  CONSTRAINT fk_refunds_payment FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
  CONSTRAINT fk_refunds_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE delivery_zones (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  store_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(160) NOT NULL,
  type ENUM('postal_code','radius','city','custom') NOT NULL DEFAULT 'postal_code',
  rules JSON NOT NULL,
  minimum_order_amount DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  base_fee DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_delivery_zones_store_active_deleted (store_id, is_active, deleted_at),
  CONSTRAINT fk_delivery_zones_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE delivery_drivers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  store_id BIGINT UNSIGNED NULL,
  vehicle_type ENUM('bike','motorcycle','car','van','truck','other') NOT NULL DEFAULT 'car',
  license_number VARCHAR(120) NULL,
  status ENUM('available','busy','offline','suspended') NOT NULL DEFAULT 'offline',
  current_latitude DECIMAL(10,7) NULL,
  current_longitude DECIMAL(10,7) NULL,
  last_seen_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_delivery_drivers_user (user_id),
  KEY idx_delivery_drivers_store_status_deleted (store_id, status, deleted_at),
  KEY idx_delivery_drivers_geo (current_latitude, current_longitude),
  CONSTRAINT fk_delivery_drivers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_delivery_drivers_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  store_id BIGINT UNSIGNED NULL,
  delivery_zone_id BIGINT UNSIGNED NULL,
  driver_id BIGINT UNSIGNED NULL,
  carrier VARCHAR(100) NULL,
  tracking_number VARCHAR(160) NULL,
  delivery_type ENUM('shipping','local_delivery','pickup') NOT NULL DEFAULT 'shipping',
  status ENUM('pending','packed','assigned','in_transit','delivered','failed','cancelled','returned') NOT NULL DEFAULT 'pending',
  estimated_delivery_at DATETIME NULL,
  shipped_at DATETIME NULL,
  delivered_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_shipments_order (order_id),
  KEY idx_shipments_store_status_deleted_created (store_id, status, deleted_at, created_at),
  KEY idx_shipments_driver_status (driver_id, status),
  KEY idx_shipments_tracking (carrier, tracking_number),
  CONSTRAINT fk_shipments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_shipments_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE SET NULL,
  CONSTRAINT fk_shipments_zone FOREIGN KEY (delivery_zone_id) REFERENCES delivery_zones(id) ON DELETE SET NULL,
  CONSTRAINT fk_shipments_driver FOREIGN KEY (driver_id) REFERENCES delivery_drivers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shipment_events (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  shipment_id BIGINT UNSIGNED NOT NULL,
  status VARCHAR(80) NOT NULL,
  message VARCHAR(500) NULL,
  latitude DECIMAL(10,7) NULL,
  longitude DECIMAL(10,7) NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_shipment_events_shipment_created (shipment_id, created_at),
  CONSTRAINT fk_shipment_events_shipment FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE,
  CONSTRAINT fk_shipment_events_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- POS
-- =========================================================

CREATE TABLE pos_registers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  store_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  device_identifier VARCHAR(160) NULL,
  status ENUM('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pos_registers_device (device_identifier),
  KEY idx_pos_registers_store_status_deleted (store_id, status, deleted_at),
  CONSTRAINT fk_pos_registers_store FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pos_shifts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  register_id BIGINT UNSIGNED NOT NULL,
  opened_by BIGINT UNSIGNED NOT NULL,
  closed_by BIGINT UNSIGNED NULL,
  opening_cash DECIMAL(19,4) NOT NULL DEFAULT 0.0000,
  closing_cash DECIMAL(19,4) NULL,
  expected_cash DECIMAL(19,4) NULL,
  status ENUM('open','closed') NOT NULL DEFAULT 'open',
  opened_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  closed_at DATETIME NULL,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_pos_shifts_register_status (register_id, status, opened_at),
  KEY idx_pos_shifts_opened_by (opened_by, opened_at),
  CONSTRAINT fk_pos_shifts_register FOREIGN KEY (register_id) REFERENCES pos_registers(id) ON DELETE CASCADE,
  CONSTRAINT fk_pos_shifts_opened_by FOREIGN KEY (opened_by) REFERENCES users(id),
  CONSTRAINT fk_pos_shifts_closed_by FOREIGN KEY (closed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pos_sales (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id BIGINT UNSIGNED NOT NULL,
  shift_id BIGINT UNSIGNED NOT NULL,
  cashier_id BIGINT UNSIGNED NOT NULL,
  receipt_number VARCHAR(60) NOT NULL,
  sale_type ENUM('sale','return','exchange') NOT NULL DEFAULT 'sale',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pos_sales_receipt (receipt_number),
  KEY idx_pos_sales_shift_created (shift_id, created_at),
  KEY idx_pos_sales_cashier_created (cashier_id, created_at),
  CONSTRAINT fk_pos_sales_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_pos_sales_shift FOREIGN KEY (shift_id) REFERENCES pos_shifts(id) ON DELETE CASCADE,
  CONSTRAINT fk_pos_sales_cashier FOREIGN KEY (cashier_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Reviews, notifications, audit, and operational logs
-- =========================================================

CREATE TABLE product_reviews (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NULL,
  rating TINYINT UNSIGNED NOT NULL,
  title VARCHAR(160) NULL,
  body TEXT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_product_reviews_user_order (product_id, user_id, order_id),
  KEY idx_product_reviews_product_status_deleted (product_id, status, deleted_at, rating),
  KEY idx_product_reviews_user_created (user_id, created_at),
  CONSTRAINT fk_product_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_product_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_product_reviews_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
  CONSTRAINT chk_product_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  type VARCHAR(80) NOT NULL,
  title VARCHAR(180) NOT NULL,
  body VARCHAR(1000) NULL,
  data JSON NULL,
  read_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_notifications_user_read_created (user_id, read_at, created_at),
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  actor_user_id BIGINT UNSIGNED NULL,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(120) NOT NULL,
  entity_id BIGINT UNSIGNED NULL,
  old_values JSON NULL,
  new_values JSON NULL,
  ip_address VARBINARY(16) NULL,
  user_agent VARCHAR(500) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_audit_logs_actor_created (actor_user_id, created_at),
  KEY idx_audit_logs_entity_created (entity_type, entity_id, created_at),
  KEY idx_audit_logs_action_created (action, created_at),
  CONSTRAINT fk_audit_logs_actor FOREIGN KEY (actor_user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE webhooks (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  event VARCHAR(120) NOT NULL,
  target_url VARCHAR(500) NOT NULL,
  secret_hash CHAR(64) NOT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_webhooks_event_status_deleted (event, status, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE webhook_deliveries (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  webhook_id BIGINT UNSIGNED NOT NULL,
  event VARCHAR(120) NOT NULL,
  payload JSON NOT NULL,
  response_status SMALLINT UNSIGNED NULL,
  response_body TEXT NULL,
  attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('pending','success','failed') NOT NULL DEFAULT 'pending',
  next_retry_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_webhook_deliveries_status_retry (status, next_retry_at),
  KEY idx_webhook_deliveries_webhook_created (webhook_id, created_at),
  CONSTRAINT fk_webhook_deliveries_webhook FOREIGN KEY (webhook_id) REFERENCES webhooks(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Seed essentials
-- =========================================================

INSERT INTO countries (iso2, name) VALUES
  ('US', 'United States'),
  ('CA', 'Canada'),
  ('GB', 'United Kingdom')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO roles (name, description) VALUES
  ('super_admin', 'Full system access'),
  ('store_admin', 'Store administration access'),
  ('manager', 'Store manager access'),
  ('cashier', 'POS cashier access'),
  ('driver', 'Delivery driver access'),
  ('customer', 'Customer account access')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO permissions (name, description) VALUES
  ('catalog.manage', 'Create and update catalog items'),
  ('orders.manage', 'Manage orders'),
  ('orders.refund', 'Process refunds'),
  ('pos.sell', 'Create POS sales'),
  ('delivery.manage', 'Manage deliveries'),
  ('settings.manage', 'Manage application settings'),
  ('reports.view', 'View business reports')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO app_settings (scope_type, scope_id, setting_key, setting_value, is_public) VALUES
  ('global', 0, 'storefront.enabled', JSON_OBJECT('enabled', true), 1),
  ('global', 0, 'checkout.tax_inclusive', JSON_OBJECT('enabled', false), 0),
  ('global', 0, 'delivery.default_provider', JSON_OBJECT('provider', 'local_delivery'), 0),
  ('global', 0, 'pos.require_shift_for_sale', JSON_OBJECT('enabled', true), 0)
ON DUPLICATE KEY UPDATE
  setting_value = VALUES(setting_value),
  is_public = VALUES(is_public);

SET FOREIGN_KEY_CHECKS = 1;
