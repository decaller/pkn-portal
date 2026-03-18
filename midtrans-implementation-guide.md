# Midtrans Snap Implementation Guide

## Purpose

This file is the continuation artifact for the Midtrans Snap migration in `pkn-portal`. It tracks the target architecture, implementation batches, completed work, working behavior, and the next step for the next session.

## Source References

- Midtrans PHP SDK README: `vendor/midtrans/midtrans-php/README.md`
- Midtrans SDK package installed through Composer: `midtrans/midtrans-php:^2.6`
- Webhook signature contract used in app code: `sha512(order_id + status_code + gross_amount + server_key)`

## Target Architecture

- New payments are initiated from invoice screens, not proof-upload actions.
- Midtrans Snap token creation is handled by `App\Services\Payments\MidtransSnapGateway`.
- Payment attempt lifecycle is stored in `invoice_payments`.
- Midtrans webhook notifications are the only authority for final payment state.
- `event_registrations.payment_status` remains the user-facing compatibility field, but transitions are now gateway-driven.
- Historical `payment_proof_path` data remains read-only legacy data.

## Setup Instructions

1. Set `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`, and `MIDTRANS_PAYMENT_EXPIRY_MINUTES`.
2. Configure the Midtrans dashboard notification URL to `/payments/midtrans/notifications`.
3. Run migrations through Sail.
4. Use Sail for composer and test commands.

## Status Mapping

- Midtrans `pending` => `invoice_payments.status = pending`, registration `payment_status = submitted`, registration `status = pending_payment`
- Midtrans `capture` with `fraud_status = challenge` => pending
- Midtrans `capture` with `fraud_status = accept` => paid
- Midtrans `settlement` => paid
- Midtrans `deny`, `cancel`, `expire`, `failure` => failed

## Implementation Checklist

- [x] Batch 1. Config and guide bootstrap
- [x] Batch 2. Persistence layer
- [x] Batch 3. Services and domain methods
- [x] Batch 4. Observer safety
- [x] Batch 5. Webhook
- [x] Batch 6. User Filament UX
- [x] Batch 7. Remove manual flow
- [x] Batch 8. Docs and translations
- [x] Batch 9. Pest coverage

## Current Progress

The Midtrans Snap migration is implemented for the active payment flow:
- Midtrans SDK dependency added.
- Snap sessions are created and reused from user invoice screens.
- Webhooks drive the registration paid/pending/failed lifecycle.
- Legacy proof upload and admin verification actions are removed from active UI.
- Invoice regeneration is protected from payment-only updates.
- README, translations, and regression tests are updated.

## Progress Log

### Batch 1. Dependency/config batch

- Completed: Installed `midtrans/midtrans-php`, added Midtrans service config, added env placeholders, and created this guide.
- Files changed: `composer.json`, `composer.lock`, `config/services.php`, `.env.example`, `.env.production.example`, `midtrans-implementation-guide.md`
- Behavior now working: Midtrans credentials and expiry can be configured through app config.
- Open issues: UI and webhook consumers are not wired into the user/admin panels yet.
- Next step: Add durable payment attempts and the service layer.

### Batch 2. Persistence/model batch

- Completed: Added `invoice_payments` table, `InvoicePayment` model, and invoice/registration relationships.
- Files changed: `database/migrations/2026_03_18_000001_create_invoice_payments_table.php`, `app/Models/InvoicePayment.php`, `app/Models/Invoice.php`, `app/Models/EventRegistration.php`
- Behavior now working: The app has a durable payment-attempt store with reusable pending-state semantics.
- Open issues: No UI path creates attempts yet.
- Next step: Add Midtrans gateway and payment orchestration services.

### Batch 3. Service/domain batch

- Completed: Added `MidtransSnapGateway`, `InvoicePaymentService`, and explicit gateway-driven registration transition methods.
- Files changed: `app/Services/Payments/MidtransSnapGateway.php`, `app/Services/Payments/InvoicePaymentService.php`, `app/Models/EventRegistration.php`
- Behavior now working: Snap transactions can be created or reused and webhook payloads can drive paid/pending/failed transitions.
- Open issues: Frontend invoice actions still need to invoke the service.
- Next step: Protect invoice regeneration from payment-only updates.

### Batch 4. Observer safety batch

- Completed: Invoice regeneration now runs only when invoice-affecting registration fields change.
- Files changed: `app/Observers/EventRegistrationObserver.php`
- Behavior now working: Payment-status updates no longer force invoice void/regenerate cycles.
- Open issues: Filament edit flows still need to hide payment-proof/manual verify affordances.
- Next step: Add the webhook route/controller wiring.

### Batch 5. Webhook batch

- Completed: Added webhook route, CSRF exemption, signature verification, and idempotent service entrypoint.
- Files changed: `bootstrap/app.php`, `routes/web.php`, `app/Http/Controllers/Payments/MidtransWebhookController.php`
- Behavior now working: Midtrans can POST notifications to a dedicated public endpoint without CSRF failures.
- Open issues: UI integration, legacy flow removal, and test coverage remain.
- Next step: Wire the user invoice UI to create/reuse Snap sessions.

### Batch 6. Filament UI batch

- Completed: Added `Pay Now` / `Continue Payment` actions, injected `snap.js` through `UserPanelProvider`, and exposed Midtrans attempt data in invoice/registration infolists.
- Files changed: `app/Filament/Shared/Tables/InvoicesTable.php`, `app/Filament/User/Resources/Invoices/Pages/ViewInvoice.php`, `app/Filament/Shared/Schemas/InvoiceInfolist.php`, `app/Filament/User/Resources/EventRegistrations/Schemas/EventRegistrationInfolist.php`, `app/Filament/Admin/Resources/EventRegistrations/Schemas/EventRegistrationInfolist.php`, `app/Providers/Filament/UserPanelProvider.php`, `resources/views/filament/user/midtrans-snap.blade.php`
- Behavior now working: Users can open Snap from invoice pages, and both user/admin panels show the latest payment attempt details.
- Open issues: Legacy manual payment actions still need to be removed from the remaining screens and docs.
- Next step: Remove the old proof-upload/manual verification flow.

### Batch 7. Legacy removal batch

- Completed: Removed active manual upload/verify actions, retired the reminder scheduler, and locked participant edits once payment has started.
- Files changed: `app/Filament/User/Resources/EventRegistrations/Pages/ViewEventRegistration.php`, `app/Filament/Admin/Resources/EventRegistrations/Pages/ViewEventRegistration.php`, `app/Filament/Admin/Resources/EventRegistrations/Pages/EditEventRegistration.php`, `app/Filament/Admin/Resources/Invoices/Tables/InvoicesTable.php`, `app/Policies/EventRegistrationPolicy.php`, `routes/console.php`, `app/Console/Commands/SendPaymentReminderNotificationsCommand.php`
- Behavior now working: New payments no longer follow the manual upload/admin verification path.
- Open issues: User-facing copy and documentation still need to be aligned.
- Next step: Update translations and README.

### Batch 8. Translation/docs batch

- Completed: Updated payment copy for Midtrans, refreshed README payment setup, and aligned registration/auth helper text.
- Files changed: `README.md`, `lang/en.json`, `lang/id.json`, `app/Filament/User/Auth/Register.php`, `app/Notifications/PaymentUploadReminderNotification.php`
- Behavior now working: The UI and docs describe the Midtrans Snap flow instead of the retired proof-upload flow.
- Open issues: Final verification still needed through Pest.
- Next step: Run the targeted Midtrans and regression test suite.

### Batch 9. Test completion batch

- Completed: Added Midtrans webhook/payment flow tests and updated notification/admin regression coverage.
- Files changed: `tests/Feature/MidtransWebhookTest.php`, `tests/Feature/MidtransInvoicePaymentFlowTest.php`, `tests/Feature/NotificationTest.php`, `tests/Feature/Filament/AdminFilamentTestingTest.php`
- Behavior now working: The suite covers webhook signature handling, settlement/pending/failure transitions, duplicate notification idempotency, attempt reuse, invoice stability, payment action visibility, and legacy manual-flow removal.
- Open issues: No browser-level Snap test is included in v1.
- Next step: Manual dashboard testing with real Midtrans sandbox credentials.

## Files Changed So Far

- `composer.json`
- `composer.lock`
- `config/services.php`
- `.env.example`
- `.env.production.example`
- `bootstrap/app.php`
- `routes/web.php`
- `routes/console.php`
- `app/Enums/PaymentStatus.php`
- `app/Enums/RegistrationStatus.php`
- `app/Models/Invoice.php`
- `app/Models/EventRegistration.php`
- `app/Models/InvoicePayment.php`
- `app/Observers/EventRegistrationObserver.php`
- `app/Console/Commands/SendPaymentReminderNotificationsCommand.php`
- `app/Policies/EventRegistrationPolicy.php`
- `app/Filament/User/Auth/Register.php`
- `app/Filament/User/Resources/Invoices/InvoiceResource.php`
- `app/Filament/Admin/Resources/Invoices/InvoiceResource.php`
- `app/Filament/Shared/Tables/InvoicesTable.php`
- `app/Filament/User/Resources/Invoices/Pages/ViewInvoice.php`
- `app/Filament/Shared/Schemas/InvoiceInfolist.php`
- `app/Filament/User/Resources/EventRegistrations/EventRegistrationResource.php`
- `app/Filament/Admin/Resources/EventRegistrations/EventRegistrationResource.php`
- `app/Filament/User/Resources/EventRegistrations/Pages/ViewEventRegistration.php`
- `app/Filament/Admin/Resources/EventRegistrations/Pages/ViewEventRegistration.php`
- `app/Filament/Admin/Resources/EventRegistrations/Pages/EditEventRegistration.php`
- `app/Filament/User/Resources/EventRegistrations/Schemas/EventRegistrationInfolist.php`
- `app/Filament/Admin/Resources/EventRegistrations/Schemas/EventRegistrationInfolist.php`
- `app/Filament/Admin/Resources/Invoices/Tables/InvoicesTable.php`
- `app/Providers/Filament/UserPanelProvider.php`
- `resources/views/filament/user/midtrans-snap.blade.php`
- `app/Services/Payments/MidtransSnapGateway.php`
- `app/Services/Payments/InvoicePaymentService.php`
- `app/Http/Controllers/Payments/MidtransWebhookController.php`
- `database/migrations/2026_03_18_000001_create_invoice_payments_table.php`
- `tests/Feature/MidtransWebhookTest.php`
- `tests/Feature/MidtransInvoicePaymentFlowTest.php`
- `tests/Feature/NotificationTest.php`
- `tests/Feature/Filament/AdminFilamentTestingTest.php`
- `midtrans-implementation-guide.md`

## Next Step

Use real Midtrans sandbox credentials and dashboard webhook configuration to manually verify the end-to-end Snap modal flow against the deployed callback URL.

## Known Gaps / Follow-ups

- Refunds, chargebacks, and post-settlement reconciliation remain manual v1 handling.
- No browser automation is included for the external Snap modal.
- Historical `payment_proof_path` data is still stored for read-only legacy visibility.
