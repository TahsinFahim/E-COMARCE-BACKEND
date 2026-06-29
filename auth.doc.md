# Authentication API Documentation

## Architecture Overview

This authentication system uses **Laravel Sanctum** with **httpOnly cookies** for maximum security. The auth flow works through **Next.js Server Actions** which act as a proxy between the browser and the Laravel API, because httpOnly cookies cannot be directly accessed from JavaScript in cross-origin scenarios (localhost:3000 → localhost:8000).

## Auth Flow Diagram

```
Browser (JS)          Next.js Server Action          Laravel API (Sanctum)
    │                         │                            │
    │  POST /login            │                            │
    │  (form submit)          │                            │
    │────────────────────────►│                            │
    │                         │  POST /api/v1/login        │
    │                         │  (with browser's cookies)  │
    │                         │───────────────────────────►│
    │                         │                            │
    │                         │  Response: {user, token}   │
    │                         │  + Set-Cookie: auth_token  │
    │                         │◄───────────────────────────│
    │                         │                            │
    │  Response + Set-Cookie  │                            │
    │◄────────────────────────│                            │
    │                         │                            │
    │  Browser stores cookie  │                            │
    │  (httpOnly, inaccess-   │                            │
    │   ible to JS)           │                            │
    │                         │                            │
    │  Future API calls:      │                            │
    │  Server Action          │  Cookie auto-forwarded     │
    │────────────────────────►│───────────────────────────►│
    │                         │                            │
    │  OR client-side fetch   │                            │
    │  with credentials:      │                            │
    │  "include" ────────────►│                            │
```

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
Uses **Laravel Sanctum with httpOnly cookies** named `auth_token`. The cookie is:

- **httpOnly**: Cannot be read by JavaScript (XSS protection)
- **SameSite=Lax**: CSRF protection (use `Strict` in production)
- **Secure**: Only sent over HTTPS (set to `false` for local dev)
- **Expires**: 1 year

---

## How Cookie Auth Works Across Origins

### The Problem
When Next.js runs on `localhost:3000` and Laravel on `localhost:8000`, the browser won't send httpOnly cookies from the Laravel response to the Next.js server on a cross-origin request.

### The Solution: Server Actions as Cookie Proxy

**Step 1 - Login/Register:**
```
Browser form submit → Server Action → Laravel API
```
- The server action calls the Laravel API **server-to-server** (same-origin, no CORS)
- Laravel returns `Set-Cookie: auth_token=<token>`
- The server action reads the `Set-Cookie` header and **forwards it to the browser**
- The browser stores the httpOnly cookie

**Step 2 - Protected API Calls (via Server Actions):**
```
Browser clicks "Logout" → Server Action → Laravel API
```
- The server action reads the browser's cookies via `next/headers`
- Forwards them to the Laravel API as a `Cookie` header
- Laravel authenticates using the cookie
- Laravel responds with new `Set-Cookie` (e.g., to clear cookie on logout)
- Server action forwards `Set-Cookie` back to browser

**Step 3 - Client-side direct API calls:**
```
Browser → fetch(url, { credentials: "include" }) → Laravel API
```
- The browser sends the httpOnly cookie directly if:
  - Frontend and backend are on the **same domain** (e.g., same-domain proxy)
  - OR CORS is configured with `Access-Control-Allow-Credentials: true`
- For local dev, this is handled by the server action proxy

---

## Endpoints

### 1. Register User

**Endpoint:** `POST /api/v1/register`

**Request Body:**
```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Success Response (201):**
```json
{
    "status": "success",
    "message": "Registration successful.",
    "user": { ... },
    "token": "1|abc123..."
}
```
The `token` and `auth_token` cookie are both set. The token is for informational/external use; the cookie handles all subsequent auth.

---

### 2. Login User

**Endpoint:** `POST /api/v1/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "Login successful.",
    "user": { ... },
    "token": "1|abc123..."
}
```

**Set-Cookie:**
```
auth_token=1|abc123...; expires=...; path=/; httponly; samesite=lax
```

---

### 3. Get Authenticated User

**Endpoint:** `GET /api/v1/user`

**Authentication:** Required (via httpOnly cookie)

**Server Action Implementation (Next.js):**
```typescript
// app/actions/auth.ts (server action)
export async function getAuthenticatedUserAction() {
  const cookieStore = await cookies();
  const allCookies = cookieStore.toString();
  
  const response = await fetch("http://localhost:8000/api/v1/user", {
    headers: {
      Accept: "application/json",
      Cookie: allCookies  // Forward browser cookies
    },
    credentials: "include",
  });
  
  return response.json();
}
```

**Client Component Usage:**
```typescript
// Usage in a component
import { getAuthenticatedUserAction } from "@/app/actions/auth";

const result = await getAuthenticatedUserAction();
// result = { success: true, user: { ... } }
```

---

### 4. Logout

**Endpoint:** `POST /api/v1/logout`

**Authentication:** Required (via httpOnly cookie)

Revokes the current Sanctum token and clears the `auth_token` cookie.

**Server Action Implementation:**
```typescript
export async function logoutUserAction() {
  const { setCookieHeader } = await serverFetch("/logout", { method: "POST" });
  await forwardCookies(setCookieHeader);  // Forwards Set-Cookie to clear browser cookie
  return { success: true };
}
```

---

### 5. Logout from All Devices

**Endpoint:** `POST /api/v1/logout-all`

**Authentication:** Required (via httpOnly cookie)

---

### 6. Forgot Password

**Endpoint:** `POST /api/v1/forgot-password`

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

---

### 7. Reset Password

**Endpoint:** `POST /api/v1/reset-password`

**Request Body:**
```json
{
    "email": "john@example.com",
    "token": "reset-token-from-email",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

---

### 8. Change Password

**Endpoint:** `POST /api/v1/change-password`

**Authentication:** Required (via httpOnly cookie)

**Request Body:**
```json
{
    "current_password": "oldpassword123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

---

### 9. Refresh Token

**Endpoint:** `POST /api/v1/refresh`

**Authentication:** Required (via httpOnly cookie)

Creates a new Sanctum token and updates the `auth_token` cookie.

---

## Key Files & Their Roles

### Backend (Laravel)

| File | Role |
|------|------|
| `Modules/Identity/Http/Controllers/AuthController.php` | Auth logic: register, login, logout, refresh |
| `App/Http/Middleware/ConvertAuthTokenCookieToBearerHeader.php` | Converts `auth_token` cookie to `Authorization: Bearer` header |
| `config/cors.php` | CORS: allows frontend origin, supports credentials |
| `config/sanctum.php` | Sanctum: stateful domains, guards, expiration |

### Frontend (Next.js)

| File | Role |
|------|------|
| `src/app/actions/auth.ts` | **Server Actions**: cookie proxy between browser and Laravel |
| `src/services/auth.service.ts` | Client-side fetch wrapper with `credentials: "include"` |
| `src/lib/api.ts` | Client-side fetch wrapper for all API calls |
| `src/lib/features/auth/authSlice.ts` | Redux state: user, isAuthenticated |
| `src/middleware.ts` | Route protection: checks `auth_token` cookie |
| `src/components/layout/MainHeader.tsx` | Auth-aware header UI |

---

## Cookie Configuration

### `.env` Settings
```env
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false          # true in production (HTTPS)
SESSION_SAME_SITE=lax                # "strict" in production
SESSION_DOMAIN=                      # ".your-domain.com" for shared cookies
```

### `config/cors.php` Settings
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:3000')],
'supports_credentials' => true,
'exposed_headers' => ['Set-Cookie'],
```

---

## Route Protection

### Next.js Middleware
The `middleware.ts` checks for the `auth_token` cookie to protect routes:

```typescript
// middleware.ts
export function middleware(request: NextRequest) {
  const authToken = request.cookies.get("auth_token")?.value;
  
  // Protected routes: /profile, /orders, /checkout, /wishlist, /change-password
  if (isProtectedRoute && !authToken) {
    return NextResponse.redirect(new URL("/login", request.url));
  }
  
  // Guest routes: /login, /register
  if (isGuestRoute && authToken) {
    return NextResponse.redirect(new URL("/", request.url));
  }
}
```

---

## Troubleshooting

### "Stuck in auto-logout loop"
**Root cause:** The `logoutUserAction` was broken (had `"use server"` inside the function instead of top-of-file), causing the logout action to fail silently but still clear state.

**Fix:** Ensure the server action properly calls the backend API and forwards the `Set-Cookie` header:
```typescript
export async function logoutUserAction() {
  const { setCookieHeader } = await serverFetch("/logout", { method: "POST" });
  await forwardCookies(setCookieHeader);
  return { success: true };
}
```

### Cookie not being set
1. Check `SESSION_DOMAIN` is blank or `null` for local dev
2. Check `SESSION_SAME_SITE` is `lax` (not `none` without Secure)
3. Check CORS `supports_credentials` is `true`
4. Check `allowed_origins` includes your frontend URL

### 401 on protected routes after login
1. Check the `auth_token` cookie exists in browser DevTools → Application → Cookies
2. Ensure `convert.auth.cookie` middleware is in the API route group
3. Check `SANCTUM_STATEFUL_DOMAINS` includes your frontend URL

---

## User Object Structure

```typescript
interface User {
    id: number;
    public_id: string;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    status: 'active' | 'inactive' | 'suspended';
    email_verified_at: string | null;
    phone_verified_at: string | null;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
    roles: Role[];
}

interface Role {
    id: number;
    name: string;
    permissions: Permission[];
}

interface Permission {
    id: number;
    name: string;
    module: string;
}
```

---

## Testing with cURL

### Register
```bash
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }' -c cookies.txt
```

### Login
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }' -c cookies.txt
```

### Get User (uses cookie)
```bash
curl -X GET http://localhost:8000/api/v1/user \
  -b cookies.txt
```

### Logout
```bash
curl -X POST http://localhost:8000/api/v1/logout \
  -b cookies.txt
```

---

## Key Implementation Details

1. **No localStorage tokens**: The Sanctum Bearer token is stored in an httpOnly cookie, not localStorage. This prevents XSS attacks from stealing the token.

2. **Server Action Proxy**: All auth operations go through Next.js server actions, which forward browser cookies server-to-server to avoid CORS issues.

3. **Cookie Forwarding**: The `forwardCookies()` helper in server actions reads the `Set-Cookie` header from Laravel responses and sets it on the Next.js response to the browser.

4. **Route Protection**: Next.js middleware checks the `auth_token` cookie for protected/guest routes on server-side navigation.

5. **Client-side Auth**: The Redux `authSlice` manages auth state on the client. On page load, `fetchCurrentUser` is dispatched to verify the cookie and populate the user state.

6. **Logout**: On logout, the token is revoked on the backend, the cookie is cleared (via `Set-Cookie` with past expiry), and Redux state is reset.