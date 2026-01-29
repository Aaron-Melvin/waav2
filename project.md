# Wild Atlantic Adventures (WAA) - Project Overview

## What the app is designed to do
This application is a partner-organization booking platform for experiences and accommodations. It serves four primary audiences:

- Super admins (site owner): operate the overall platform, onboard/approve partners, and oversee compliance.
- Partner organizations: companies who list accommodations and events on the platform.
- Partner staff: manage their own organization's catalog, events, availability, and bookings.
- Customers: search availability, place holds, book, and manage bookings.

Core workflows include:

- Partner onboarding and approval (admin-controlled) with API clients.
- Catalog management (products, media, pricing, taxes, fees).
- Event scheduling and availability management.
- Booking flow with holds, confirmations, payments, cancellations, and refunds.
- Notifications (email/SMS) and audit logging.
- Front-facing booking and customer self-service.

Reference documents:
- `documents/booking-platform-api-spec.md` (architecture, APIs, state machine)


## Migrations

- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`
- `2025_09_22_145432_add_two_factor_columns_to_users_table.php`
- `2026_01_14_110155_create_pulse_tables.php`
- `2026_01_14_110739_create_features_table.php`
- `2026_01_14_111940_create_permission_tables.php`
- `2026_01_15_201520_create_platform_users_table.php`
- `2026_01_20_000001_create_partner_tables.php`
- `2026_01_20_000002_update_users_for_partners.php`
- `2026_01_20_000003_create_catalog_tables.php`
- `2026_01_20_000004_create_event_tables.php`
- `2026_01_20_000005_create_policy_tables.php`
- `2026_01_20_000006_create_accommodation_tables.php`
- `2026_01_20_000007_create_pricing_tables.php`
- `2026_01_20_000008_create_availability_tables.php`
- `2026_01_20_000009_create_booking_tables.php`
- `2026_01_20_000010_create_payment_tables.php`
- `2026_01_20_000011_create_notification_tables.php`
- `2026_01_20_000012_create_integration_tables.php`
- `2026_01_20_000013_create_analytics_tables.php`
- `2026_01_20_000014_create_audit_tables.php`
- `2026_01_20_000015_add_notification_opt_ins_to_customers_table.php`
- `2026_01_20_000016_create_staff_invitations_table.php`
- `2026_01_20_000017_create_customer_access_tokens_table.php`
- `2026_01_26_205235_add_booking_reference_to_bookings_table.php`
- `2026_01_26_220925_add_customer_auth_fields_to_customers_table.php`
- `2026_01_26_230736_update_sessions_user_id_for_customers.php`

## Models

- `app/Models/ApiClient.php`
- `app/Models/AuditLog.php`
- `app/Models/Booking.php`
- `app/Models/BookingAllocation.php`
- `app/Models/BookingItem.php`
- `app/Models/BookingStatusHistory.php`
- `app/Models/CancellationPolicy.php`
- `app/Models/Coupon.php`
- `app/Models/Customer.php`
- `app/Models/CustomerAccessToken.php`
- `app/Models/EligibilityRule.php`
- `app/Models/Event.php`
- `app/Models/EventBlackout.php`
- `app/Models/EventOverride.php`
- `app/Models/EventSeries.php`
- `app/Models/Fee.php`
- `app/Models/Hold.php`
- `app/Models/IdempotencyKey.php`
- `app/Models/InventoryLedger.php`
- `app/Models/Invoice.php`
- `app/Models/Location.php`
- `app/Models/NotificationEvent.php`
- `app/Models/NotificationQueue.php`
- `app/Models/NotificationTemplate.php`
- `app/Models/Partner.php`
- `app/Models/Payment.php`
- `app/Models/PlatformUser.php`
- `app/Models/Product.php`
- `app/Models/ProductMedia.php`
- `app/Models/RatePlan.php`
- `app/Models/RatePlanPrice.php`
- `app/Models/Refund.php`
- `app/Models/SmsMessage.php`
- `app/Models/SmsProvider.php`
- `app/Models/StaffInvitation.php`
- `app/Models/Tax.php`
- `app/Models/Unit.php`
- `app/Models/UnitBookingLock.php`
- `app/Models/UnitCalendar.php`
- `app/Models/UnitHoldLock.php`
- `app/Models/User.php`

## Schema summary (table-by-table)

### Core Laravel
- `users`: auth table with partner scoping; includes `partner_id`, `role`, `phone_e164`, `two_factor_enabled`, 2FA secrets, soft deletes, and unique `partner_id` + `email`.
- `password_reset_tokens`: password reset tokens keyed by email.
- `sessions`: session storage; `user_id` stored as UUID (char(36)) to support customer auth.
- `cache`, `cache_locks`: cache storage and locks.
- `jobs`, `job_batches`, `failed_jobs`: queue infrastructure tables.

### Platform auth
- `platform_users`: super admin/support users with `role`, soft deletes, and standard auth columns.

### Partner orgs and access
- `partners`: partner org profile (`slug`, `billing_email`, `currency`, `timezone`, `status`) with soft deletes.
- `api_clients`: API credentials per partner org, `client_id`, `client_secret_hash`, scopes, status.
- `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`: Spatie permission tables (standard schema).

### Catalog
- `locations`: partner location details, address, geo coordinates, timezone.
- `products`: bookable items; `type`, `slug`, `description`, optional `location_id`, `capacity_total`, `status`, `visibility`, lead time/cutoff, `meta`, soft deletes; unique per `partner_id` + `type` + `slug`.
- `product_media`: media assets tied to products (`url`, `kind`, `sort`).

### Events
- `event_series`: recurrence templates for repeating event slots (e.g., daily 11:00 tour), with default start/end times, capacity, and rules used to generate scheduled events.
- `events`: scheduled instances with `starts_at`, `ends_at`, `capacity`, `capacity_reserved`, `traffic_light`, `status`, weather flags, publish state, soft deletes.
- `event_overrides`: field/value overrides for events (JSON values).
- `event_blackouts`: date ranges blocked for product or location with reason.

### Policies
- `cancellation_policies`: policy rules as JSON.
- `eligibility_rules`: rule set (kind + config) optionally scoped to a product.

### Accommodation
- `products` (type = accommodation): represent a property or accommodation listing; store check-in/check-out times, house rules, amenities, and default stay rules in `meta`.
- `units`: rooms or room types within an accommodation product; includes `code`, `name`, `occupancy_adults`, `occupancy_children`, `status`, and `housekeeping_required`.
- `unit_calendars`: per-room availability by date; supports `is_available`, `min_stay_nights`, `max_stay_nights`, and block reasons.
- `rate_plans`: pricing plans for accommodation products with `pricing_model`, currency, and optional cancellation policy.
- `rate_plan_prices`: nightly pricing by date range with `price`, `extra_adult`, `extra_child`, and optional `restrictions`.

### Pricing
- `taxes`: percentage taxes with `applies_to`.
- `fees`: flat fees with `type` and `applies_to`.
- `coupons`: discount codes with value/limits, date ranges, conditions, and status.

### Availability
- `inventory_ledger`: inventory deltas by scope with optional booking/hold references.
- `holds`: temporary holds with scope references, expiry, and status.
- `unit_hold_locks`: holds for unit/date combinations (unique on `unit_id` + `date`).
- `idempotency_keys`: idempotency records with request hash, response snapshot, status, expiry.

### Booking
- `customers`: customer profile with marketing and notification opt-ins; auth fields (`password`, `remember_token`).
- `bookings`: booking records with channel, partner/customer, totals, coupon info, terms version, payment state, `booking_reference`, soft deletes.
- `booking_items`: line items with product/event/unit references, dates/times, pricing breakdown.
- `booking_allocations`: capacity allocations per booking (event or unit).
- `unit_booking_locks`: unit/date locks for confirmed bookings.
- `booking_status_history`: lifecycle audit trail (from/to, reason, meta).
- `customer_access_tokens`: magic link tokens for customer self-service.

### Payments
- `payments`: provider payments, amounts, status, capture time, raw webhook payload.
- `refunds`: refund records tied to payments.
- `invoices`: partner invoice records with number, totals, and optional PDF URL.

### Notifications
- `notification_templates`: per-partner templates by channel and language.
- `notification_events`: event tracking for notifications.
- `notification_queue`: scheduled notifications with polymorphic target model.
- `sms_providers`: SMS provider credentials and status.
- `sms_messages`: outbound SMS delivery logs.

### Integrations
- `webhooks`: outbound webhook registrations.
- `webhook_deliveries`: delivery attempts and retry tracking.
- `ical_feeds`: calendar feeds for products or units.
- `calendar_sync_accounts`: external calendar accounts for sync.
- `calendar_sync_events`: per-event sync state with providers.

### Analytics
- `search_index`: availability/search projections for products/events/units.
- `reports_cache`: cached report payloads with TTL.

### Audit and compliance
- `audit_logs`: partner-scoped audit events with before/after payloads.
- `gdpr_erasure_queue`: customer erasure requests and status.

### Feature flags and observability
- `features`: Laravel Pennant feature values by name/scope.
- `pulse_values`, `pulse_entries`, `pulse_aggregates`: Laravel Pulse metrics storage.

## Model relationships

- `ApiClient`: belongsTo `Partner`.
- `AuditLog`: no explicit relationships defined.
- `Booking`: belongsTo `Partner`, `Customer`, `Coupon`; hasMany `BookingItem`, `BookingAllocation`, `BookingStatusHistory`, `UnitBookingLock`, `Payment`, `Invoice`.
- `BookingAllocation`: belongsTo `Booking`, `Event`, `Unit`.
- `BookingItem`: belongsTo `Booking`, `Product`, `Event`, `Unit`.
- `BookingStatusHistory`: belongsTo `Booking`.
- `CancellationPolicy`: belongsTo `Partner`; hasMany `RatePlan`.
- `Coupon`: belongsTo `Partner`; hasMany `Booking`.
- `Customer`: belongsTo `Partner`; hasMany `Booking`, `CustomerAccessToken`.
- `CustomerAccessToken`: belongsTo `Partner`, `Customer`.
- `EligibilityRule`: belongsTo `Partner`, `Product`.
- `Event`: belongsTo `Partner`, `Product`, `EventSeries`; hasMany `EventOverride`, `BookingItem`, `BookingAllocation`.
- `EventBlackout`: belongsTo `Partner`, `Product`, `Location`.
- `EventOverride`: belongsTo `Event`.
- `EventSeries`: belongsTo `Partner`, `Product`; hasMany `Event`.
- `Fee`: belongsTo `Partner`.
- `Hold`: belongsTo `Partner`; hasMany `UnitHoldLock`.
- `IdempotencyKey`: belongsTo `Partner`.
- `InventoryLedger`: belongsTo `Partner`, `Booking`, `Hold`.
- `Invoice`: belongsTo `Partner`, `Booking`.
- `Location`: belongsTo `Partner`; hasMany `Product`, `EventBlackout`.
- `NotificationEvent`: belongsTo `Partner`.
- `NotificationQueue`: belongsTo `Partner`, `NotificationTemplate`; morphTo `model`.
- `NotificationTemplate`: belongsTo `Partner`.
- `Partner`: hasMany `User`, `ApiClient`, `Location`, `Product`, `EventSeries`, `Event`, `EventBlackout`, `CancellationPolicy`, `EligibilityRule`, `Unit`, `RatePlan`, `Tax`, `Fee`, `Coupon`, `InventoryLedger`, `Hold`, `IdempotencyKey`, `Customer`, `Booking`, `Invoice`.
- `Payment`: belongsTo `Booking`; hasMany `Refund`.
- `Product`: belongsTo `Partner`, `Location`; hasMany `ProductMedia`, `EventSeries`, `Event`, `EventBlackout`, `Unit`, `RatePlan`, `EligibilityRule`.
- `ProductMedia`: belongsTo `Product`.
- `RatePlan`: belongsTo `Partner`, `Product`, `CancellationPolicy`; hasMany `RatePlanPrice`.
- `RatePlanPrice`: belongsTo `RatePlan`.
- `Refund`: belongsTo `Payment`.
- `SmsMessage`: belongsTo `Partner`, `SmsProvider`; morphTo `related`.
- `SmsProvider`: belongsTo `Partner`; hasMany `SmsMessage`.
- `StaffInvitation`: belongsTo `Partner`, `User` (inviter).
- `Tax`: belongsTo `Partner`.
- `Unit`: belongsTo `Partner`, `Product`; hasMany `UnitCalendar`, `UnitHoldLock`, `UnitBookingLock`.
- `UnitBookingLock`: belongsTo `Booking`, `Unit`.
- `UnitCalendar`: belongsTo `Partner`, `Unit`.
- `UnitHoldLock`: belongsTo `Hold`, `Unit`.
- `User`: belongsTo `Partner`.

## Rebuild plan (Laravel 12 + Livewire v4 multi-file components)

This is a full-from-scratch plan to recreate the current system with Livewire v4 multi-file components (MFC).

### Phase 0 - Discovery and scope lock
- Read and freeze the product blueprint in `documents/booking-platform-api-spec.md`.
- Confirm which parts are required for the first release (admin + partner + front APIs).
- Decide on single-partner vs multi-partner initial constraints for early milestones.

### Phase 1 - Foundation and tooling
- Create a new Laravel 12 project.
- Install required packages: Livewire v4, Flux UI, Fortify, Pennant, Pulse, Socialite, Spatie Permission, and Pest.
- Configure environment, queues, mail, cache, and storage.
- Set up Tailwind and Vite for the frontend build.
- Publish Livewire stubs if you want a standard multi-file scaffold.

### Phase 2 - Partner access control
- Implement partner resolution by API client credentials.
- Register partner middleware in `bootstrap/app.php`.
- Add partner-aware global scope or base query patterns.
- Define roles and permissions (super admin, partner admin, partner staff, front).
- Wire policies for partner-scoped access (products, bookings, payments, etc.).

### Phase 3 - Database schema and Eloquent model layer
- Recreate the migrations in domain order (partners, catalog, events, availability, bookings, payments, notifications, analytics, audit).
- Build Eloquent models, relationships, and model factories.
- Enforce data invariants via casts, model events, and guarded attributes.
- Seed core data (partners, staff, sample catalog, events, bookings).

### Phase 4 - API layer (versioned JSON)
- Implement `/api/v1/admin` endpoints for partner management.
- Implement `/api/v1/partner` endpoints for catalog, events, bookings, staff, and API clients.
- Implement `/api/v1/front` endpoints for availability, holds, booking creation, confirmation, and cancellations.
- Add idempotency handling and strict validation requests for all mutation endpoints.

### Phase 5 - Livewire v4 UI (multi-file components)
- Standardize on multi-file components using `php artisan make:livewire <name> --mfc`.
- Use `pages::` namespaces for full-page components, e.g. `php artisan make:livewire pages::partner/products/index --mfc`.
- Structure multi-file components under `resources/views/components/.../<name>/` with:
  - `<name>.php` (component class)
  - `<name>.blade.php` (view)
  - `<name>.js` and `<name>.css` (optional)
- Build the platform admin UI first (partner onboarding, impersonation).
- Build partner staff UI (catalog, events, availability, bookings, staff management).
- Build the front booking flow (search, event selection, hold, checkout, confirmation).
- Front search should present locations grouped by county/area (not partner-provided location names).
- Use Flux UI components for form inputs, modals, tables, and layout scaffolding.

### Phase 6 - Booking lifecycle automation
- Implement booking state transitions with side effects (inventory locks, allocations, invoices, payments).
- Implement holds expiration and auto-release of inventory.
- Implement cancellation and refund workflows driven by cancellation policies.
- Add audit logging for staff actions and state changes.

### Phase 7 - Notifications and messaging
- Create notification templates and queues for email and SMS.
- Implement notification workers for confirmations, cancellations, reminders, and follow-ups.
- Add opt-in/opt-out preferences and channel-aware delivery logic.

### Phase 8 - Testing and quality gates
- Write Pest feature tests per endpoint group (happy path + validation failure).
- Add booking state machine tests (including idempotency).
- Test partner isolation and authorization boundaries.
- Add UI tests for core Livewire flows as needed.
- Add schema-level tests for critical constraints.

### Phase 9 - Deployment readiness
- Add health checks, observability (Pulse), and queues in production.
- Configure backups, queue workers, and monitoring alerts.
- Run full regression and performance checks before launch.

## Additional considerations for a large multi-type booking system

### Booking lifecycle depth
- Modifications: reschedule, add/remove items, partial changes, and partial refunds.
- Booking item replacements and amendment fees.
- Deposits, balance due dates, and payment plans.
- Expired holds and abandoned checkout recovery rules.

### Customer experience and compliance
- Waivers and consent (e-sign, liability, GDPR/marketing).
- Guest vs registered customers; account merge and duplicate detection.
- Accessibility and localization (language, time zones, date formatting).

### Inventory and capacity
- Resource allocation beyond product capacity (guides, vehicles, equipment).
- Waitlists with auto-promotion rules.
- Overbooking policies and safeguards.
- No-show handling and release rules.

### Tickets and onsite ops
- Ticket issuance (QR/barcodes).
- Attendee lists and check-in workflows.
- Staff check-in roles and audit trail.

### Pricing, taxes, and payouts
- Taxes/fees by location and product type; inclusive vs exclusive pricing.
- Channel commissions and partner settlement schedules.
- Multi-currency display vs settlement currency and FX rules.

### API and integration hardening
- API pagination/filtering conventions and error shape standards.
- API key rotation and revocation flows.
- Webhook signing, retries, and idempotency expectations.

### Search and discovery
- Search indexing strategy, geo search, and ranking signals.
- Product bundles, add-ons, and upsells.

### Messaging and notifications
- Reminder schedules, SLA/ops alerts, and exception handling.
- Notification preferences by channel and region.
