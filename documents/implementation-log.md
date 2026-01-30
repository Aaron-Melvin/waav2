# Implementation Log

## Phase 1 - Foundation and tooling
- Status: Completed
- Date: 2026-01-28
- Notes:
  - Installed laravel/pennant, laravel/pulse, laravel/socialite, spatie/laravel-permission.
  - Published configs and migrations for Pennant, Pulse, and Spatie Permission.

## Phase 2 - Partner access control
- Status: Completed
- Date: 2026-01-28
- Notes:
  - Added partner + API client models, migrations, factories, and seeders.
  - Added partner resolution and API client authentication middleware.
  - Scaffolded admin, partner, and front API route files.
  - Added initial partner/admin endpoints and API key usage docs.
  - Switched partner API authentication to API keys for easier partner site integration.
  - Added partner self-signup endpoint and admin bootstrap API key issuance.
  - Added admin endpoint to activate partner status.
  - Added admin endpoint to list pending partner signups.
  - Added admin API filters and a Livewire approval UI for partner status review.
  - Added super admin seeder for local login.

## Phase 3 - Database schema and Eloquent model layer
- Status: Completed
- Date: 2026-01-28
- Notes:
  - Added catalog schema/models and demo seeding for partners, locations, products, and media.
  - Added event series/events schema, models, and demo seeding with overrides and blackouts.
  - Added accommodation units, availability holds, inventory ledger, idempotency keys, and demo seeding.
  - Added booking/customer schema and demo booking data with items, allocations, status history, and unit locks.
  - Added policy/pricing schema (cancellation policies, eligibility rules, rate plans/prices, taxes, fees, coupons).
  - Added payment schema (payments, refunds, invoices) and demo data.
  - Added notification schema (templates, queue, events, SMS providers/messages) and demo data.
  - Added integration schema (webhooks, iCal feeds, calendar sync) and demo data.
  - Added analytics/audit schema (search index, reports cache, audit logs, GDPR erasure queue) and demo data.
  - Added staff invitations, customer access tokens, and platform users for realistic test data.
  - Updated sessions user_id type for customer auth compatibility.

## Phase 4 - API layer (versioned JSON)
- Status: In progress
- Date: 2026-01-28
- Notes:
  - Added partner endpoints for payments, invoices, notification templates, webhooks, iCal feeds, and calendar sync accounts.
  - Added webhook delivery listing for partner integrations.
  - Added admin refund endpoint for payments.
  - Added API resources and form requests for the new endpoints.
  - Added front availability search, hold creation, and booking create/confirm/cancel endpoints.
  - Added partner booking list and status update endpoints.
  - Added partner product list/create endpoints and API resources.
  - Added partner catalog endpoints for locations, events, blackouts, overrides, units, unit calendars, rate plans/prices, staff invitations.
  - Added partner pricing policy endpoints for taxes, fees, cancellation policies.
  - Added partner event series endpoints plus event generation from recurrence rules.
  - Added partner eligibility rule + coupon endpoints.
  - Added admin endpoints for taxes, fees, and cancellation policies (list + create + update).
  - Added idempotency middleware for admin/partner/front mutation endpoints.
  - Added admin Livewire pages for bookings, payments, locations, and products.
  - Added Livewire test cache override to keep tests isolated from runtime cache permissions.
  - Added customer search UI (Livewire + Flux) for browsing availability.
  - Added customer booking flow UI (hold creation, booking details, payment confirmation).
  - Added partner UI forms for creating products, locations, and events from catalog/availability pages.
  - Added partner accommodation tooling (unit creation plus taxes, fees, and cancellation policy management).
  - Added unit metadata, bulk calendar range updates, and edit/delete flows for accommodation policies.

## Checkpoint - Phase 4 (current state)
- Status: Active development
- Date: 2026-01-28
- What’s done:
  - Partner API: products list/create/show/update; locations; event series + generation; events + overrides; blackouts; units + calendars; rate plans + prices; eligibility rules; coupons; bookings list/update; payments; invoices; notification templates; webhooks + deliveries; iCal feeds; calendar sync accounts; staff invitations; taxes/fees/cancellation policies.
  - Front API: availability search; holds; booking create/confirm/cancel with idempotency.
  - Customer UI: availability search page using Livewire + Flux.
  - Admin API: partners + refunds; taxes/fees/cancellation policies listing; idempotency for mutations.
  - Admin UI: Livewire pages for partners, bookings, payments, locations, products (Flux tables + filters).
  - Tests added for partner products API and admin pages; test cache isolation for Livewire + Blade.
- How to view UI:
  - Admin locations: `/admin/locations`
  - Admin products: `/admin/products`
  - Admin bookings: `/admin/bookings`
  - Admin payments: `/admin/payments`
  - Customer search: `/front`
  - Customer booking details: `/front/booking/{hold}`
  - Customer booking confirmation: `/front/booking/{booking}/confirm`
- Tests last run:
  - `php artisan test --compact tests/Feature/Api/Partner/CatalogAvailabilityApiTest.php tests/Feature/Api/Partner/PricingPoliciesApiTest.php tests/Feature/Api/Partner/EligibilityCouponsApiTest.php tests/Feature/Api/Partner/ProductsApiTest.php tests/Feature/Api/Partner/BookingsApiTest.php tests/Feature/Api/Partner/PaymentsNotificationsIntegrationsTest.php tests/Feature/Api/PartnerApiTest.php tests/Feature/Api/Admin/PricingPoliciesApiTest.php tests/Feature/Api/Front/BookingFlowTest.php`
  - `php artisan test --compact tests/Feature/Front/SearchPageTest.php`
  - `php artisan test --compact tests/Feature/PartnerPagesTest.php`
  - `php artisan test --compact tests/Feature/PartnerPagesTest.php`
  - `php artisan test --compact tests/Feature/PartnerPagesTest.php`
  - `php artisan test --compact tests/Feature/Front/SearchPageTest.php tests/Feature/Front/BookingFlowUiTest.php`
  - `php artisan test --compact tests/Feature/Front/SearchPageTest.php`
  - `php artisan test --compact tests/Feature/Front/SearchPageTest.php`
  - `php artisan test --compact tests/Feature/Front/SearchPageTest.php`
  - `php artisan test --compact tests/Feature/Front/SearchPageTest.php`
  - `php artisan test --compact tests/Feature/Front/SearchPageTest.php`

## Next plan (recommended order)
- Phase 4 hardening:
  - Add missing API resources for accommodation setup (single endpoint to create product + unit + rate plan + price + availability).
  - Standardize API responses with resource/collection shapes and pagination conventions.
  - Add validation + authorization tests for partner and front APIs.
- Phase 5 start (UI):
  - Build partner UI for full accommodation management (rate plans, pricing, unit calendars, policies).
  - Expand front booking UI with detail pages, availability, and checkout UX.
  - Align front search grouping by county/area (per project.md requirement).

## 2026-01-29
- Added accommodation setup wizard for partners to create an accommodation with a starter unit, rate plan, pricing window, and optional availability range.
- Linked the accommodation setup page from the catalog products screen.
- Added PartnerPagesTest coverage for the new accommodation setup flow.

## 2026-01-29 - Plan: Separate events vs accommodations
- Goal: eliminate mixed product logic by splitting models, routes, Livewire pages, and availability/booking flows per domain.
- Backend: introduce distinct event and accommodation product models (or dedicated subclasses) with discrete migrations, policies, and form requests; split partner/admin/front route files to `/events` and `/accommodations`; add mappers to keep legacy `type`-based endpoints working during migration.
- Availability/holds/bookings: keep shared booking envelope but use typed item DTOs; separate availability services (time-slot vs date-range) and validation rules; migrate idempotency + ledgers accordingly.
- Livewire + Flux UI: create parallel pages/forms/tables for each domain; reuse shared atoms (pricing, policies, locations) via shared components; ensure Tailwind v4 utility usage and Flux Pro form/table components.
- Data migration: backfill existing mixed `products` records into dedicated tables and establish feature flags to roll out per partner; add tests to cover both pathways until legacy endpoints are removed.

## 2026-01-29 - Migration scaffolding for split
- Added new tables for `event_products`, `accommodation_products`, typed booking items, and typed holds (migrations only).
- Registered per-partner Pennant feature flag `partner-event-accommodation-split`.
- Added placeholder route groups under admin/partner/front APIs guarded by the feature flag to stage new namespaces without breaking existing flows.
