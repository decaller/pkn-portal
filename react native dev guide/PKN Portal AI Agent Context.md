# PKN Portal App - AI Agent Context and Build Rules

## Project overview

This mobile app is the React Native companion for the existing Laravel 12 + Filament 5 portal.

The backend already has strong web-side domain logic for:

- phone-number login
- organization-aware membership
- event registration with package breakdowns
- invoices and payment proof flows
- notifications

The mobile app should not try to rebuild all of that on day one.

## Recommended product architecture

Use a hybrid model:

- Native screens for content and account surfaces:
  - home dashboard
  - event list and event detail
  - news
  - notifications
  - invoices
  - profile summary
- WebView only for complex Filament-driven flows:
  - event registration
  - payment proof upload
  - profile or organization editing if needed

The key distinction is simple:

- read-heavy experiences should be native
- mutation-heavy flows that already exist in Filament can stay web-backed

## Build stack

- Framework: Expo + React Native
- Language: TypeScript
- Routing: Expo Router
- Networking: Axios
- State: Zustand with persist
- Storage:
  - auth token in `expo-secure-store`
  - cached app state in `@react-native-async-storage/async-storage`
- Styling: React Native `StyleSheet`

## Environment constraints

The current development setup is Linux-first and should stay Expo-friendly.

Rules:

1. Do not require native Android or iOS project edits unless explicitly approved.
2. Prefer packages that work with standard Expo workflows.
3. Do not use `react-navigation` directly; use Expo Router patterns.

## Authentication rule

Use native API authentication, not browser-based login, for v1.

Why:

- the Laravel app already authenticates users with `phone_number`
- the repository already contains an API auth test direction
- this is simpler than implementing a browser redirect login loop first

Expected mobile auth input:

- `phone_number`
- `password`

After successful login:

- save the Sanctum bearer token securely
- attach it to all API requests
- use a dedicated WebView magic-link endpoint when a cookie-backed Filament session is needed

## WebView bridge rule

Do not open protected Filament pages directly inside a WebView.

Instead:

1. Native app calls `GET /api/v1/webview/magic-link?redirect=...`
2. Laravel returns a temporary signed URL
3. React Native opens the returned URL in a WebView
4. Laravel establishes the web session and redirects to the intended Filament page
5. On successful completion, Laravel redirects to a custom deep link such as `pknportal://action-success`

This keeps API auth and web session auth separate.

## Data loading rule

Use stale-while-revalidate behavior everywhere practical.

Pattern:

1. Render cached state immediately
2. Fetch fresh data in the background
3. Replace cache and UI when the request succeeds
4. Show a non-blocking offline state when it fails

Prefer BFF-style endpoints over many small calls.

## Suggested app structure

```text
pkn-portal-app/
├── app/
│   ├── _layout.tsx
│   ├── +not-found.tsx
│   ├── auth/
│   │   ├── login.tsx
│   │   └── register.tsx
│   ├── (tabs)/
│   │   ├── _layout.tsx
│   │   ├── index.tsx
│   │   ├── events.tsx
│   │   ├── invoices.tsx
│   │   └── profile.tsx
│   ├── events/
│   │   └── [id].tsx
│   ├── news/
│   │   └── [id].tsx
│   ├── notifications/
│   │   └── index.tsx
│   └── webview/
│       └── hybrid-flow.tsx
├── src/
│   ├── components/
│   ├── features/
│   ├── lib/
│   ├── services/
│   ├── store/
│   └── types/
└── assets/
```

## Coding directives

When generating code for this app:

1. Use TypeScript interfaces for all API contracts.
2. Keep API logic in dedicated service modules, not inline in screens.
3. Include loading, empty, error, and offline states.
4. Model auth around `phone_number`, not email-first assumptions.
5. Treat WebView flows as isolated wrappers, not mixed into normal screen logic.
6. Keep tap targets mobile-friendly and layouts simple.
