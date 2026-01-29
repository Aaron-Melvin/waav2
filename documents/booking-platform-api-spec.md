# Partner Organization Booking Platform Backend Blueprint

This document provides a single source of truth for the backend roadmap, API surface, booking state machine, and access layout. It is intended to guide implementation and serve as the reference for future code changes.

## 1) Architecture Goals

- **Partner org isolation first:** Every request is scoped to a partner organization using partner resolution middleware.
- **API-first:** All core workflows are exposed via versioned JSON APIs for partner and front-end integrations.
- **Separation of concerns:** Admin, partner, and front-end controllers/routes are distinct.
- **Security by default:** Role-based access control and partner scoping are mandatory.

## 2) Route & Controller Segmentation

### Suggested Controller Structure

```
app/Http/Controllers/
  Admin/
    BookingController.php
    ...
  Partner/
    BookingController.php
    ...
  Front/
    BookingController.php
    ...
  Api/
    Admin/BookingController.php
    Partner/BookingController.php
    Front/BookingController.php
```

### Suggested Route Structure

```
routes/
  admin.php
  partner.php
  front.php
  api/admin.php
  api/partner.php
  api/front.php
```

### Route Groups

- **Admin**: `/admin/*`, internal ops across partner orgs.
- **Partner**: `/partner/*`, partner staff operations.
- **Front**: `/front/*`, public booking and availability APIs.

## 3) Middleware & Policy Layout

### Middleware

- **ResolvePartner**
  - Resolves partner by:
    - API client credentials (`api_clients`).
  - Attaches `currentPartner` to request context.

- **RequirePartner**
  - Blocks requests without a resolved partner.

- **ApiClientAuth**
  - Validates `client_id` + signature or token.
  - Loads `api_clients`.

- **Role & Permission** (Spatie)
  - `role:super-admin`, `role:partner-admin`, `role:partner-staff`, `role:front`.

### Policy Rules (examples)

- **BookingPolicy**
  - `view`: partner must match booking partner.
  - `update`: role must be super admin or partner manager.
  - `cancel`: partner manager or super admin only.

- **PaymentPolicy**
  - `view`: partner match + staff role.
  - `refund`: super admin only.

- **ProductPolicy**
  - `manage`: partner staff only.

## 4) API Endpoints (v1)

### 4.1 Partner Management (Admin API)

**List partners**
- `GET /api/v1/admin/partners`

**Create partner**
- `POST /api/v1/admin/partners`
```json
{
  "name": "Acme Adventures",
  "slug": "acme-adventures",
  "billing_email": "billing@acme.test",
  "currency": "EUR",
  "timezone": "Europe/Dublin",
  "status": "active"
}
```

### 4.2 API Clients (Partner API)

**Create API client**
- `POST /api/v1/partner/api-clients`
```json
{
  "client_id": "acme_partner",
  "client_secret": "plain-text-secret",
  "scopes": ["bookings:read", "bookings:write"],
  "status": "active"
}
```

### 4.3 Catalog (Partner API)

**List products**
- `GET /api/v1/partner/products`

**Create product**
- `POST /api/v1/partner/products`
```json
{
  "name": "Sea Kayak Tour",
  "status": "active",
  "type": "event",
  "default_currency": "EUR"
}
```

### 4.4 Availability (Front API)

**Search availability**
- `POST /api/v1/front/availability/search`
```json
{
  "product_id": "uuid",
  "date_range": {
    "from": "2026-02-01",
    "to": "2026-02-14"
  },
  "quantity": 2
}
```

### 4.5 Booking Flow (Front API)

**Create hold**
- `POST /api/v1/front/holds`
```json
{
  "product_id": "uuid",
  "event_id": "uuid",
  "date": "2026-02-10",
  "quantity": 2,
  "expires_in_minutes": 15
}
```

**Create booking (draft)**
- `POST /api/v1/front/bookings`
```json
{
  "customer": {
    "name": "Jane Doe",
    "email": "jane@example.com",
    "phone_e164": "+353861234567"
  },
  "items": [
    {
      "item_type": "event",
      "product_id": "uuid",
      "event_id": "uuid",
      "date": "2026-02-10",
      "quantity": 2
    }
  ],
  "coupon_code": "SUMMER10",
  "terms_version": "2026-01"
}
```

**Confirm booking (payment)**
- `POST /api/v1/front/bookings/{booking}/confirm`
```json
{
  "payment_method": "card",
  "payment_token": "gateway_token"
}
```

**Cancel booking**
- `POST /api/v1/front/bookings/{booking}/cancel`
```json
{
  "reason": "Customer request",
  "refund": true
}
```

### 4.6 Partner Booking Management (Partner API)

**List bookings**
- `GET /api/v1/partner/bookings?status=confirmed&from=2026-02-01&to=2026-02-28`

**Update booking status**
- `PATCH /api/v1/partner/bookings/{booking}`
```json
{
  "status": "completed",
  "note": "Tour completed"
}
```

### 4.7 Webhooks (Partner API)

**Booking status webhook**
- `POST /api/v1/partner/webhooks/bookings`
```json
{
  "booking_id": "uuid",
  "status": "confirmed",
  "changed_at": "2026-02-10T12:00:00Z"
}
```

## 5) Booking State Machine

### States

1. **Draft**: booking created but not confirmed.
2. **PendingPayment**: awaiting payment or confirmation from gateway.
3. **Confirmed**: payment successful and inventory allocated.
4. **Completed**: service delivered.
5. **Cancelled**: cancelled by user or admin.
6. **Refunded**: refund issued after cancellation.
7. **Expired**: holds or pending payments timed out.

### Valid Transitions

| From | To | Trigger |
| --- | --- | --- |
| Draft | PendingPayment | Confirm request issued |
| Draft | Cancelled | User cancels before payment |
| PendingPayment | Confirmed | Payment success |
| PendingPayment | Cancelled | Payment failed or user cancelled |
| PendingPayment | Expired | Payment timeout |
| Confirmed | Completed | Service delivered |
| Confirmed | Cancelled | Admin or customer cancels |
| Cancelled | Refunded | Refund processed |
| Draft | Expired | Hold expired |

### Required Side Effects

- **Draft → PendingPayment**
  - Lock inventory via holds or unit locks.
  - Create payment intent.

- **PendingPayment → Confirmed**
  - Persist booking allocations.
  - Finalize invoice.
  - Trigger confirmation notifications.

- **PendingPayment → Cancelled/Expired**
  - Release holds and locks.
  - Cancel payment intent.

- **Confirmed → Cancelled**
  - Apply cancellation policy.
  - Trigger refund if applicable.
  - Notify customer and partner staff.

## 6) Required JSON Resource Shapes (Examples)

### Booking Resource

```json
{
  "id": "uuid",
  "status": "confirmed",
  "type": "event",
  "channel": "direct",
  "currency": "EUR",
  "totals": {
    "gross": "120.00",
    "tax": "20.00",
    "fees": "5.00"
  },
  "customer": {
    "id": "uuid",
    "name": "Jane Doe",
    "email": "jane@example.com"
  },
  "items": [
    {
      "id": 1,
      "product_id": "uuid",
      "event_id": "uuid",
      "date": "2026-02-10",
      "quantity": 2,
      "unit_price": "50.00",
      "total": "100.00"
    }
  ],
  "created_at": "2026-02-01T10:00:00Z"
}
```

## 7) Implementation Notes

- **Partner scoping is mandatory** on every query. Use a global scope or a base repository pattern with partner-aware queries.
- **Idempotency** is required for booking creation and confirmation endpoints.
- **Eager loading** is required for API responses (bookings with customer/items/payments).
- **Queue** notifications and webhook delivery.

## 8) Testing Expectations

- Feature tests for:
  - Availability search and hold expiration.
  - Booking creation, confirmation, cancellation.
  - Authorization boundaries between admin/partner/front.
- Each new endpoint should include at least one happy-path and one failure-path test.

---

This document is intended to remain updated as the platform evolves.
