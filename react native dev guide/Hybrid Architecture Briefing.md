# PKN Portal Hybrid Architecture Briefing

## Goal

Ship a mobile app quickly without rewriting the most complex Filament workflows.

The web app already contains business-critical logic around:

- registration package selection
- organization membership
- participant management
- invoice generation
- payment proof handling

Those parts should be reused, not reimplemented immediately.

## Architecture split

### Native surfaces

Build these as first-class React Native screens:

- home dashboard
- event list
- event detail
- news list and detail
- notifications
- invoice list and invoice detail
- profile summary

### WebView surfaces

Keep these web-backed in v1:

- event registration create flow
- payment proof upload
- participant edits if they remain Filament-heavy
- advanced organization management

## Authentication model

Use API auth for the app, then bridge into cookie auth only when entering WebView flows.

### Native auth flow

1. App shows a native login screen.
2. User enters `phone_number` and `password`.
3. App calls `POST /api/v1/auth/login`.
4. Laravel returns a bearer token plus current user payload.
5. App stores the token in secure storage.

### Why this is the preferred v1 path

- it matches the existing Laravel login field better
- it aligns with the repository's API auth test direction
- it avoids building a browser login redirect flow before the API exists

## Magic-link WebView bridge

The API token used by the app is not enough for Filament pages, because Filament expects a web session.

Use a magic-link bridge:

1. Native app requests `GET /api/v1/webview/magic-link?redirect=...`
2. Laravel validates the bearer token
3. Laravel returns a one-time signed URL
4. React Native opens that URL in a WebView
5. Laravel creates the web session and redirects to the actual Filament page

## WebView exit strategy

A WebView flow must return the user to native UI when the flow is complete.

Recommended pattern:

1. Mobile app includes `source=mobile`
2. Laravel detects mobile context
3. Successful form submission redirects to `pknportal://action-success?...`
4. The WebView intercepts that deep link and closes itself
5. Native app refreshes affected stores such as registrations, invoices, and notifications

## Backend responsibilities

Backend work required for this architecture:

1. Create versioned API routes under `/api/v1`
2. Add native login and logout endpoints using Sanctum
3. Add a magic-link endpoint for WebView entry
4. Add mobile-safe redirects for successful Filament flows
5. Return absolute asset URLs from API resources
6. Add BFF endpoints where multiple queries would otherwise be needed

## Frontend responsibilities

Frontend work required for this architecture:

1. Build native auth, dashboard, events, invoices, notifications, and profile screens
2. Create a reusable authenticated WebView wrapper
3. Handle token persistence and `401` recovery centrally
4. Refresh state after any WebView-based mutation
5. Cache list data for fast reopen and poor connectivity

## Phase recommendation

Recommended delivery order:

1. Native auth
2. Native dashboard and events
3. Native invoices and notifications
4. WebView registration bridge
5. WebView payment proof flow
6. Optional later migration of some web flows to native
