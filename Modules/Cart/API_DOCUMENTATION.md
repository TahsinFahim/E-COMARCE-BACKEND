# Cart API Documentation

Base URL: `/api/v1/carts`
Authentication: `Bearer {token}` ( Sanctum )

---

## 1. Add Item to Cart

**Endpoint:** `POST /api/v1/carts/add-item`

**Description:** Add a product variant to the user's cart. If the variant already exists, quantity will be increased.

**Headers:**
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
    "variant_id": 15,
    "quantity": 2,
    "store_id": 3
}
```

**Field Descriptions:**
- `variant_id` (required): ID of the product variant
- `quantity` (optional): Number of items to add (default: 1, minimum: 1)
- `store_id` (optional): ID of the store (if not provided, uses existing cart's store)

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Item added to cart successfully.",
    "cart": {
        "id": 5,
        "user_id": 12,
        "store_id": 3,
        "status": "active",
        "expires_at": "2026-06-28 00:40:00",
        "created_at": "2026-06-21 00:40:00",
        "updated_at": "2026-06-21 00:40:00",
        "items": [
            {
                "id": 8,
                "cart_id": 5,
                "variant_id": 15,
                "quantity": 2,
                "unit_price": "1299.00",
                "line_total": 2598.00,
                "variant": {
                    "id": 15,
                    "product_id": 7,
                    "sku": "TSHIRT-RED-M",
                    "sale_price": "1299.00",
                    "product": {
                        "id": 7,
                        "name": "Premium Cotton T-Shirt",
                        "slug": "premium-cotton-tshirt",
                        "thumbnail": "https://example.com/images/tshirt.jpg"
                    }
                }
            }
        ],
        "total": 2598.00
    }
}
```

**Error Response (422):**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "variant_id": ["The selected variant id is invalid."]
    }
}
```

**Error Response (500):**
```json
{
    "status": "error",
    "message": "Error adding to cart: Product variant not found."
}
```

---

## 2. Update Cart Item Quantity

**Endpoint:** `PUT /api/v1/carts/items/{itemId}`

**Description:** Update the quantity of a specific cart item.

**Headers:**
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

**URL Parameters:**
- `itemId` (required): ID of the cart item to update

**Request Body:**
```json
{
    "quantity": 5
}
```

**Field Descriptions:**
- `quantity` (required): New quantity (minimum: 1)

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Cart item updated successfully.",
    "cart": {
        "id": 5,
        "user_id": 12,
        "store_id": 3,
        "status": "active",
        "items": [
            {
                "id": 8,
                "cart_id": 5,
                "variant_id": 15,
                "quantity": 5,
                "unit_price": "1299.00",
                "line_total": 6495.00,
                "variant": {
                    "id": 15,
                    "sku": "TSHIRT-RED-M",
                    "sale_price": "1299.00",
                    "product": {
                        "id": 7,
                        "name": "Premium Cotton T-Shirt",
                        "thumbnail": "https://example.com/images/tshirt.jpg"
                    }
                }
            }
        ],
        "total": 6495.00
    }
}
```

**Error Response (404):**
```json
{
    "status": "error",
    "message": "Cart item not found."
}
```

---

## 3. Remove Item from Cart

**Endpoint:** `DELETE /api/v1/carts/items/{itemId}`

**Description:** Remove a specific item from the cart.

**Headers:**
```
Authorization: Bearer {your_token}
Accept: application/json
```

**URL Parameters:**
- `itemId` (required): ID of the cart item to remove

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Item removed from cart successfully.",
    "cart": {
        "id": 5,
        "user_id": 12,
        "store_id": 3,
        "status": "active",
        "items": [],
        "total": 0
    }
}
```

**Error Response (404):**
```json
{
    "status": "error",
    "message": "Cart item not found."
}
```

---

## 4. Get My Cart

**Endpoint:** `GET /api/v1/carts/my-cart`

**Description:** Get the current authenticated user's active cart. Creates a new cart if one doesn't exist.

**Headers:**
```
Authorization: Bearer {your_token}
Accept: application/json
```

**Success Response (200):**
```json
{
    "status": "success",
    "cart": {
        "id": 5,
        "user_id": 12,
        "store_id": 3,
        "status": "active",
        "expires_at": "2026-06-28 00:40:00",
        "created_at": "2026-06-21 00:40:00",
        "updated_at": "2026-06-21 00:40:00",
        "items": [
            {
                "id": 8,
                "cart_id": 5,
                "variant_id": 15,
                "quantity": 2,
                "unit_price": "1299.00",
                "line_total": 2598.00,
                "variant": {
                    "id": 15,
                    "sku": "TSHIRT-RED-M",
                    "sale_price": "1299.00",
                    "product": {
                        "id": 7,
                        "name": "Premium Cotton T-Shirt",
                        "slug": "premium-cotton-tshirt",
                        "thumbnail": "https://example.com/images/tshirt.jpg"
                    }
                }
            },
            {
                "id": 9,
                "cart_id": 5,
                "variant_id": 22,
                "quantity": 1,
                "unit_price": "2499.00",
                "line_total": 2499.00,
                "variant": {
                    "id": 22,
                    "sku": "JEANS-BLUE-32",
                    "sale_price": "2499.00",
                    "product": {
                        "id": 12,
                        "name": "Slim Fit Jeans",
                        "slug": "slim-fit-jeans",
                        "thumbnail": "https://example.com/images/jeans.jpg"
                    }
                }
            }
        ],
        "total": 5097.00
    }
}
```

**Empty Cart Response:**
```json
{
    "status": "success",
    "cart": {
        "id": 6,
        "user_id": 12,
        "store_id": null,
        "status": "active",
        "expires_at": "2026-06-28 00:45:00",
        "created_at": "2026-06-21 00:45:00",
        "updated_at": "2026-06-21 00:45:00",
        "items": [],
        "total": 0
    }
}
```

---

## 5. Get Cart by ID (Admin/Existing)

**Endpoint:** `GET /api/v1/carts/{id}`

**Description:** Get a specific cart by ID (for admin or reference).

**Headers:**
```
Authorization: Bearer {your_token}
Accept: application/json
```

**URL Parameters:**
- `id` (required): Cart ID

**Success Response (200):**
```json
{
    "status": "success",
    "cart": {
        "id": 5,
        "user_id": 12,
        "store_id": 3,
        "status": "active",
        "user": {
            "id": 12,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "store": {
            "id": 3,
            "name": "Fashion Hub"
        },
        "items": [...],
        "total": 5097.00
    }
}
```

---

## 6. List All Carts (Admin)

**Endpoint:** `GET /api/v1/carts`

**Description:** Get paginated list of all carts (admin only).

**Headers:**
```
Authorization: Bearer {your_token}
Accept: application/json
```

**Success Response (200):**
```json
{
    "data": [
        {
            "id": 5,
            "user_email": "john@example.com",
            "store_name": "Fashion Hub",
            "items_count": 2,
            "total": "5097.00",
            "status": "active",
            "created_at": "21 Jun 2026 00:40"
        }
    ],
    "links": {...},
    "meta": {...}
}
```

---

## Common Response Fields

### Cart Object
- `id`: Cart ID
- `user_id`: User ID who owns the cart
- `store_id`: Store ID (null if no store specified)
- `status`: Cart status (active, completed, abandoned)
- `expires_at`: Cart expiration date
- `items`: Array of cart items
- `total`: Calculated total price of all items

### Cart Item Object
- `id`: Cart item ID
- `cart_id`: Parent cart ID
- `variant_id`: Product variant ID
- `quantity`: Item quantity
- `unit_price`: Price per unit
- `line_total`: Calculated total (unit_price × quantity)
- `variant`: Product variant details
  - `id`: Variant ID
  - `sku`: Stock keeping unit
  - `sale_price`: Current price
  - `product`: Product details
    - `id`: Product ID
    - `name`: Product name
    - `slug`: Product slug
    - `thumbnail`: Product image URL

---

## Error Codes

| Status Code | Description |
|-------------|-------------|
| 200 | Success |
| 401 | Unauthorized (missing or invalid token) |
| 404 | Resource not found |
| 422 | Validation error |
| 500 | Server error |

---

## Testing with cURL

### Add Item to Cart
```bash
curl -X POST http://your-domain.com/api/v1/carts/add-item \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "variant_id": 15,
    "quantity": 2,
    "store_id": 3
  }'
```

### Update Item Quantity
```bash
curl -X PUT http://your-domain.com/api/v1/carts/items/8 \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "quantity": 5
  }'
```

### Remove Item
```bash
curl -X DELETE http://your-domain.com/api/v1/carts/items/8 \
  -H "Authorization: Bearer {your_token}"
```

### Get My Cart
```bash
curl -X GET http://your-domain.com/api/v1/carts/my-cart \
  -H "Authorization: Bearer {your_token}"
```

---

## Testing with Postman/Insomnia

1. Set request type (POST/PUT/GET/DELETE)
2. Set URL: `http://your-domain.com/api/v1/carts/...`
3. Go to Headers tab:
   - Key: `Authorization`, Value: `Bearer {your_token}`
   - Key: `Content-Type`, Value: `application/json` (for POST/PUT)
   - Key: `Accept`, Value: `application/json`
4. For POST/PUT, go to Body tab → raw → JSON and paste request body
5. Send request

---

## Notes

- All endpoints require authentication via Sanctum token
- Cart expires after 7 days of inactivity
- Adding same variant multiple times increases quantity instead of creating duplicate items
- Cart total is automatically calculated based on items
- Soft deletes are used for carts and cart items (can be restored if needed)