# Wild Atlantic Adventures API Usage

## Base URL
- `/api/v1`

## Upcoming change: split events vs accommodations
- We are decoupling the two product types into separate resources and endpoints to reduce conditional logic and mixed schemas.
- New namespaces to introduce:
  - Admin: `/api/v1/admin/events/*` and `/api/v1/admin/accommodations/*`
  - Partner: `/api/v1/partner/events/*` and `/api/v1/partner/accommodations/*`
  - Front: `/api/v1/front/events/*` (time-slot availability) and `/api/v1/front/accommodations/*` (date-range availability)
- Feature flag: `partner-event-accommodation-split` (per-partner) gates new namespaces during rollout.
- Bookings and holds will keep a shared envelope but use distinct item payloads:
  - Event item: `{ "item_type": "event", "product_id": "uuid", "event_id": "uuid", "quantity": 2 }`
  - Accommodation item: `{ "item_type": "accommodation", "product_id": "uuid", "unit_id": "uuid", "starts_on": "2026-02-10", "ends_on": "2026-02-12", "quantity": 1 }`
- Availability search will branch by type:
  - Events: `/front/events/availability` (date + time window; capacity based)
  - Accommodations: `/front/accommodations/availability` (date-range; inventory/overbooking rules)
- Partner catalog create/update flows will be split to avoid `type` switches in a single product endpoint. Existing `type` field will be deprecated once the split endpoints are live. Please keep backward compatibility during migration.

## Authentication

### Partner + Front APIs (API key)
Use the API client credentials in request headers:
- `X-Client-Id`: the API client ID
- `X-Client-Secret`: the API client secret

Secrets are only returned once when the API client is created. Store them securely and rotate if exposed.
API key access is only available for partners with `status = active`.

### Idempotency (all mutation endpoints)
For POST/PATCH/PUT/DELETE endpoints on admin, partner, and front APIs, pass:
- `Idempotency-Key`: unique key per request payload

If a key is reused with the same payload, the previous response is returned. Reusing a key with a different payload returns `409`.

## Development seed data

When running `php artisan db:seed` in local or testing environments, the demo seeder creates:
- Super admin: `admin@waa.test` / `password`
- Partner admins: `partner1@waa.test` ... `partner6@waa.test` / `password`
- Partner API clients: `client_id = {partner_slug}`, `client_secret = demo-secret-{partner_slug}`

These credentials are intended for local testing only.

Additional demo data seeded for local testing includes pricing (policies, rate plans, taxes/fees, coupons), payments/refunds, notifications/SMS, webhooks, calendar sync feeds, search index rows, audit logs, and GDPR erasure requests.

### Admin APIs (session auth)
Admin APIs currently require a signed-in user session and role-based access. UI endpoints will be added in later phases.

## Admin API

### List partners
`GET /api/v1/admin/partners`

Query params:
- `status` (optional): `pending`, `active`, or `inactive`
- `search` (optional): matches name, slug, or billing email
- `per_page` (optional): 1-100 (default 50)

Response (200):
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Acme Adventures",
      "slug": "acme-adventures",
      "billing_email": "billing@acme.test",
      "currency": "EUR",
      "timezone": "Europe/Dublin",
      "status": "active",
      "created_at": "2026-01-28T11:00:00Z",
      "updated_at": "2026-01-28T11:00:00Z"
    }
  ]
}
```

### List pending partners
`GET /api/v1/admin/partners/pending`

Response (200):
```json
{
  "data": [
    {
      "id": "uuid",
      "name": "Pending Partner",
      "slug": "pending-partner",
      "billing_email": "billing@pending.test",
      "currency": "EUR",
      "timezone": "Europe/Dublin",
      "status": "pending",
      "created_at": "2026-01-28T11:00:00Z",
      "updated_at": "2026-01-28T11:00:00Z"
    }
  ]
}
```

### Create partner
`POST /api/v1/admin/partners`

Request:
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

Response (201):
```json
{
  "data": {
    "id": "uuid",
    "name": "Acme Adventures",
    "slug": "acme-adventures",
    "billing_email": "billing@acme.test",
    "currency": "EUR",
    "timezone": "Europe/Dublin",
    "status": "active",
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  }
}
```

### Create partner API client (bootstrap key)
`POST /api/v1/admin/partners/{partner}/api-clients`

Request:
```json
{
  "client_id": "acme_partner",
  "client_secret": "optional-plain-text-secret",
  "scopes": ["bookings:read", "bookings:write"],
  "status": "active"
}
```

Response (201):
```json
{
  "data": {
    "id": "uuid",
    "partner_id": "uuid",
    "client_id": "acme_partner",
    "scopes": ["bookings:read", "bookings:write"],
    "status": "active",
    "last_used_at": null,
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  },
  "client_secret": "plain-text-secret-returned-once"
}
```

Use this endpoint to issue the initial API key after a partner signs up.

### Update partner status
`PATCH /api/v1/admin/partners/{partner}/status`

Request:
```json
{
  "status": "active"
}
```

Response (200):
```json
{
  "data": {
    "id": "uuid",
    "name": "Acme Adventures",
    "slug": "acme-adventures",
    "billing_email": "billing@acme.test",
    "currency": "EUR",
    "timezone": "Europe/Dublin",
    "status": "active",
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  }
}
```

### Create refund (admin only)
`POST /api/v1/admin/payments/{payment}/refunds`

Request:
```json
{
  "amount": 50,
  "reason": "Customer request",
  "status": "pending"
}
```

### List taxes (admin only)
`GET /api/v1/admin/taxes`

Query params:
- `status` (optional): `active` or `inactive`
- `partner_id` (optional): partner UUID
- `per_page` (optional): 1-100 (default 50)

### Create tax (admin only)
`POST /api/v1/admin/taxes`

Request:
```json
{
  "partner_id": "uuid",
  "name": "VAT",
  "rate": 0.2,
  "applies_to": "booking",
  "is_inclusive": false,
  "status": "active"
}
```

### Update tax (admin only)
`PATCH /api/v1/admin/taxes/{tax}`

Request:
```json
{
  "name": "VAT Updated",
  "rate": 0.25,
  "applies_to": "booking",
  "is_inclusive": false,
  "status": "active"
}
```

### List fees (admin only)
`GET /api/v1/admin/fees`

Query params:
- `status` (optional): `active` or `inactive`
- `partner_id` (optional): partner UUID
- `per_page` (optional): 1-100 (default 50)

### Create fee (admin only)
`POST /api/v1/admin/fees`

Request:
```json
{
  "partner_id": "uuid",
  "name": "Service Fee",
  "type": "flat",
  "amount": 5,
  "applies_to": "booking",
  "status": "active"
}
```

### Update fee (admin only)
`PATCH /api/v1/admin/fees/{fee}`

Request:
```json
{
  "name": "Service Fee Updated",
  "type": "flat",
  "amount": 6,
  "applies_to": "booking",
  "status": "active"
}
```

### List cancellation policies (admin only)
`GET /api/v1/admin/cancellation-policies`

Query params:
- `status` (optional): `active` or `inactive`
- `partner_id` (optional): partner UUID
- `per_page` (optional): 1-100 (default 50)

### Create cancellation policy (admin only)
`POST /api/v1/admin/cancellation-policies`

Request:
```json
{
  "partner_id": "uuid",
  "name": "Flexible",
  "rules": [
    { "window_hours": 24, "fee_percent": 10 }
  ],
  "status": "active"
}
```

### Update cancellation policy (admin only)
`PATCH /api/v1/admin/cancellation-policies/{cancellationPolicy}`

Request:
```json
{
  "name": "Flexible Updated",
  "rules": [
    { "window_hours": 12, "fee_percent": 20 }
  ],
  "status": "active"
}
```

Response (201):
```json
{
  "data": {
    "id": "uuid",
    "payment_id": "uuid",
    "amount": "50.00",
    "currency": "EUR",
    "status": "pending",
    "reason": "Customer request",
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  }
}
```

## Partner API (API key)

### Show current partner
`GET /api/v1/partner/partner`

Response (200):
```json
{
  "data": {
    "id": "uuid",
    "name": "Acme Adventures",
    "slug": "acme-adventures",
    "billing_email": "billing@acme.test",
    "currency": "EUR",
    "timezone": "Europe/Dublin",
    "status": "active",
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  }
}
```

### Create API client
`POST /api/v1/partner/api-clients`

Request:
```json
{
  "client_id": "acme_partner",
  "client_secret": "optional-plain-text-secret",
  "scopes": ["bookings:read", "bookings:write"],
  "status": "active"
}
```

Response (201):
```json
{
  "data": {
    "id": "uuid",
    "partner_id": "uuid",
    "client_id": "acme_partner",
    "scopes": ["bookings:read", "bookings:write"],
    "status": "active",
    "last_used_at": null,
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  },
  "client_secret": "plain-text-secret-returned-once"
}
```

If `client_secret` is omitted, the system generates a secure secret and returns it once in the response.

Use an existing API client key to create or rotate additional keys.

## Partner UI (non-API)
These pages are accessed via session auth and are not part of the JSON API.

### Accommodation setup wizard
`GET /partner/catalog/accommodations/create`

Creates an accommodation with a starter unit, rate plan, price window, and optional availability range in one flow.

### List products
`GET /api/v1/partner/products`

Query params:
- `status` (optional): `active` or `inactive`
- `type` (optional): `event` or `accommodation`
- `visibility` (optional): `public`, `unlisted`, or `private`
- `search` (optional): matches name or slug
- `per_page` (optional): 1-100 (default 50)

Response (200):
```json
{
  "data": [
    {
      "id": "uuid",
      "partner_id": "uuid",
      "location_id": "uuid",
      "name": "Sea Kayak Tour",
      "type": "event",
      "slug": "sea-kayak-tour",
      "description": "Guided sea kayaking adventure.",
      "capacity_total": 20,
      "default_currency": "EUR",
      "status": "active",
      "visibility": "public",
      "lead_time_minutes": 120,
      "cutoff_minutes": 60,
      "meta": {
        "duration_minutes": 120
      },
      "location": {
        "id": "uuid",
        "name": "Dingle Marina",
        "city": "Dingle"
      },
      "created_at": "2026-01-28T11:00:00Z",
      "updated_at": "2026-01-28T11:00:00Z"
    }
  ]
}
```

### Create product
`POST /api/v1/partner/products`

Request:
```json
{
  "name": "Sea Kayak Tour",
  "type": "event",
  "status": "active",
  "visibility": "public",
  "default_currency": "EUR",
  "location_id": "uuid",
  "capacity_total": 20,
  "lead_time_minutes": 120,
  "cutoff_minutes": 60,
  "meta": {
    "duration_minutes": 120,
    "meeting_point": "Dingle Marina"
  }
}
```

Response (201):
```json
{
  "data": {
    "id": "uuid",
    "partner_id": "uuid",
    "location_id": "uuid",
    "name": "Sea Kayak Tour",
    "type": "event",
    "slug": "sea-kayak-tour",
    "status": "active",
    "visibility": "public",
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  }
}
```

If `slug` is omitted, the API generates a unique slug per partner + product type.

### Manage event series
`GET /api/v1/partner/event-series`

`POST /api/v1/partner/event-series`

Request:
```json
{
  "product_id": "uuid",
  "name": "Morning Tour",
  "starts_at": "09:00",
  "ends_at": "11:00",
  "timezone": "Europe/Dublin",
  "recurrence_rule": {
    "frequency": "weekly",
    "interval": 1,
    "byweekday": ["MO", "WE", "FR"]
  }
}
```

### Generate events from a series
`POST /api/v1/partner/event-series/{eventSeries}/generate`

Request:
```json
{
  "date_range": {
    "from": "2026-02-01",
    "to": "2026-02-28"
  }
}
```

### Manage eligibility rules
`GET /api/v1/partner/eligibility-rules`

`POST /api/v1/partner/eligibility-rules`

Request:
```json
{
  "name": "Adults only",
  "kind": "age_gate",
  "config": {
    "min_age": 18
  },
  "product_id": "uuid"
}
```

### Manage coupons
`GET /api/v1/partner/coupons`

`POST /api/v1/partner/coupons`

Request:
```json
{
  "code": "WELCOME10",
  "discount_type": "percent",
  "discount_value": 10,
  "status": "active"
}
```

### Manage pricing policies
`GET /api/v1/partner/taxes`

`GET /api/v1/partner/fees`

`GET /api/v1/partner/cancellation-policies`

### List bookings
`GET /api/v1/partner/bookings`

Query params:
- `status` (optional): `draft`, `pending_payment`, `confirmed`, `completed`, `cancelled`
- `from` (optional): ISO date filter on created_at
- `to` (optional): ISO date filter on created_at
- `per_page` (optional): 1-100 (default 50)

### Update booking status
`PATCH /api/v1/partner/bookings/{booking}`

Request:
```json
{
  "status": "completed",
  "note": "Tour completed"
}
```

### List payments
`GET /api/v1/partner/payments`

Query params:
- `status` (optional): e.g. `captured`, `pending`, `failed`
- `provider` (optional): e.g. `stripe`
- `booking_id` (optional): booking UUID
- `per_page` (optional): 1-100 (default 50)

### Show payment (includes refunds)
`GET /api/v1/partner/payments/{payment}`

### List invoices
`GET /api/v1/partner/invoices`

Query params:
- `status` (optional): `issued`, `paid`, `void`
- `booking_id` (optional): booking UUID
- `per_page` (optional): 1-100 (default 50)

### Show invoice
`GET /api/v1/partner/invoices/{invoice}`

### List notification templates
`GET /api/v1/partner/notification-templates`

Query params:
- `channel` (optional): `email` or `sms`
- `status` (optional): `active` or `inactive`
- `per_page` (optional): 1-100 (default 50)

### Create notification template
`POST /api/v1/partner/notification-templates`

Request:
```json
{
  "name": "Booking Confirmation",
  "channel": "email",
  "subject": "Your booking is confirmed",
  "body": "Thanks for booking with us.",
  "status": "active"
}
```

### Update notification template
`PATCH /api/v1/partner/notification-templates/{template}`

### List webhooks
`GET /api/v1/partner/webhooks`

### Create webhook
`POST /api/v1/partner/webhooks`

Request:
```json
{
  "name": "Booking Updates",
  "url": "https://example.test/hooks",
  "events": ["booking.confirmed", "booking.cancelled"],
  "status": "active"
}
```

### Update webhook
`PATCH /api/v1/partner/webhooks/{webhook}`

### List webhook deliveries
`GET /api/v1/partner/webhooks/{webhook}/deliveries`

Query params:
- `status` (optional): `pending`, `delivered`, `failed`
- `per_page` (optional): 1-100 (default 50)

### List iCal feeds
`GET /api/v1/partner/ical-feeds`

### Create iCal feed
`POST /api/v1/partner/ical-feeds`

Request:
```json
{
  "name": "Event Feed",
  "product_id": "uuid"
}
```

### List calendar sync accounts
`GET /api/v1/partner/calendar-sync-accounts`

### Create calendar sync account
`POST /api/v1/partner/calendar-sync-accounts`

Request:
```json
{
  "provider": "google",
  "email": "sync@example.test"
}
```

## Front API (API key)

### Partner signup (public)
`POST /api/v1/front/partners/signup`

Request:
```json
{
  "name": "Acme Adventures",
  "slug": "acme-adventures",
  "billing_email": "billing@acme.test",
  "currency": "EUR",
  "timezone": "Europe/Dublin"
}
```

Response (201):
```json
{
  "data": {
    "id": "uuid",
    "name": "Acme Adventures",
    "slug": "acme-adventures",
    "billing_email": "billing@acme.test",
    "currency": "EUR",
    "timezone": "Europe/Dublin",
    "status": "pending",
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  }
}
```

After signup, an admin must issue the initial API key before the partner can call partner APIs.

### Show current partner context
`GET /api/v1/front/partner`

Headers:
- `X-Client-Id`: `acme_partner`
- `X-Client-Secret`: `plain-text-secret-returned-once`

Response (200):
```json
{
  "data": {
    "id": "uuid",
    "name": "Acme Adventures",
    "slug": "acme-adventures",
    "billing_email": "billing@acme.test",
    "currency": "EUR",
    "timezone": "Europe/Dublin",
    "status": "active",
    "created_at": "2026-01-28T11:00:00Z",
    "updated_at": "2026-01-28T11:00:00Z"
  }
}
```

### Search availability
`POST /api/v1/front/availability/search`

Request:
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

### Create hold
`POST /api/v1/front/holds`

Request (event hold):
```json
{
  "product_id": "uuid",
  "event_id": "uuid",
  "quantity": 2,
  "expires_in_minutes": 15
}
```

Request (unit hold):
```json
{
  "product_id": "uuid",
  "unit_id": "uuid",
  "starts_on": "2026-02-10",
  "ends_on": "2026-02-12",
  "quantity": 1
}
```

### Create booking (draft)
`POST /api/v1/front/bookings`

Request:
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
      "quantity": 2
    }
  ],
  "coupon_code": "SUMMER10",
  "terms_version": "2026-01"
}
```

### Confirm booking (payment)
`POST /api/v1/front/bookings/{booking}/confirm`

Request:
```json
{
  "payment_method": "manual",
  "payment_token": "gateway_token"
}
```

### Cancel booking
`POST /api/v1/front/bookings/{booking}/cancel`

Request:
```json
{
  "reason": "Customer request",
  "refund": true
}
```

## Customer UI (Livewire)

### Search availability page
`GET /front`

Livewire + Flux UI page for customer availability search with partner/product/date filters.

### Booking details page
`GET /front/booking/{hold}`

Customer booking details form built with Livewire + Flux.

### Booking confirmation page
`GET /front/booking/{booking}/confirm`

Payment confirmation step for customer bookings.
