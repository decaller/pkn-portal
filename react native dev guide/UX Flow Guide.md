# PKN Portal App - UX Flow Guide

This document defines the mobile user experience for the React Native application, including screen structure, UI design direction, and screen-to-screen interactions for the hybrid native + WebView architecture.

---

## 1. Core UX Strategy: "Read Native, Write Hybrid"

The app prioritizes fast read flows and reuses existing Laravel + Filament logic for complex write flows.

- **READ-HEAVY** surfaces are **Native Screens**:
  - dashboard
  - event list and event detail
  - registration list and registration detail
  - news list and detail
  - notifications
  - invoice detail
  - profile summary
- **WRITE-HEAVY / COMPLEX LOGIC** surfaces stay **Hybrid via Authenticated WebView**:
  - login
  - event registration
  - payment proof upload
  - profile editing
  - organization management

This split keeps the app responsive while avoiding premature rewrites of business-critical portal workflows.

---

## 2. UX Design Principles

### Primary product goals
- Make key information readable within 1-2 taps.
- Make status obvious without opening every detail page.
- Separate "browse" mode from "complete a portal task" mode.
- Keep destructive or important actions explicit and reversible where possible.

### Design language
- Clean, institutional, and trustworthy rather than promotional.
- Emphasize clarity of hierarchy: large titles, compact metadata, strong status chips.
- Use cards for grouped information, sticky action bars for primary decisions, and banners for high-priority alerts.

### Interaction model
- Tabs are for top-level destinations only.
- Details, articles, and drill-down records use stack navigation.
- WebView flows open as modal sessions so users understand they are entering a contained workflow.

---

## 3. Global Navigation Structure

The app uses **Bottom Tab Navigation** for primary areas and **Stack Navigation** for detail screens.

### Top-Level Tabs
- **Dashboard**: Summary of featured events, latest news, registration alerts, and shortcut actions.
- **Events**: Searchable event discovery and detail browsing.
- **Registrations**: Registration history, status tracking, linked invoices, and follow-up actions.
- **Profile**: Personal account summary, organization context, settings, and logout.

### Secondary Stack Screens
- Event Detail
- News Detail
- Invoice Detail
- Notifications
- Registration Detail
- WebView Bridge Modal
- Hybrid Login Modal

### Global Header Rules
- Top-level tabs use a contextual header title and optional right-side action.
- Detail screens use a back button, title, and share/bookmark/download actions where relevant.
- WebView modals always include:
  - left: close button
  - center: flow title
  - right: overflow/help if needed

---

## 4. Screen Inventory and Hierarchy

### A. Hybrid Login Screen (WebView)
**Purpose:** Authenticate without rebuilding the existing portal login flow.

**Core elements:**
- Full-screen WebView container
- Safe-area header with:
  - close button
  - title: `Login`
  - loading indicator during page transitions
- Optional connection error state with:
  - illustration or icon
  - short explanation
  - `Retry` button
- Web login form rendered by portal:
  - phone number field
  - password field
  - submit button
  - validation feedback

**Behavior:**
- App opens `/user/login`.
- App watches for redirect to `/api/v1/auth/token-handoff`.
- On success:
  - extract token
  - persist token in secure storage
  - close modal
  - route user to Dashboard
- On manual close before completion:
  - confirm if login is mid-process
  - return user to previous native state or exit gate

### B. Dashboard Screen
**Purpose:** Give immediate value after login and provide shortcuts into deeper flows.

**Layout order:**
1. Greeting header
2. Connectivity or account banners
3. Quick action row
4. Featured events carousel/list
5. Latest news list
6. Registration summary card
7. Optional testimonials or partner trust block

**Detailed UI elements:**
- Header:
  - user greeting: `Hi, {first_name}`
  - subtext: organization name or membership role
  - notification icon with unread badge
- Banner area:
  - offline banner
  - incomplete profile prompt
  - unpaid invoice warning
- Quick actions:
  - `Browse Events`
  - `My Registrations`
  - `Notifications`
  - `Profile`
- Featured event cards:
  - cover image
  - event title
  - date and city
  - price/package summary
  - status chip: `Open`, `Closing Soon`, `Full`
  - CTA: `View Details`
- Latest news cards:
  - thumbnail
  - article title
  - publish date
  - short excerpt
- Registration summary card:
  - active registration count
  - next required action
  - linked unpaid amount if present
  - CTA: `Open Registration`

**Primary interactions:**
- Tap notification icon -> Notifications screen
- Tap featured event -> Event Detail
- Tap news item -> News Detail
- Tap registration summary -> Registrations tab or Registration Detail
- Pull to refresh -> reload dashboard payload

### C. Events List Screen
**Purpose:** Help users find, scan, and filter events quickly.

**Detailed UI elements:**
- Search bar:
  - placeholder: `Search events`
  - clear action
- Filter row:
  - category chip
  - date/status chip
  - city/location chip if supported later
- Scrollable event cards (rendered via `<FlashList>` for high performance):
  - thumbnail/banner
  - title
  - date range
  - venue or city
  - short summary
  - registration availability chip
  - optional price/package starting point
- Empty state:
  - icon/illustration
  - message: no events match filters
  - `Reset Filters` action

**Primary interactions:**
- Search submit -> refresh event list
- Tap filter chip -> bottom sheet or inline picker
- Tap event card -> Event Detail
- Pull to refresh -> refetch list
- Reach end of list -> paginate

### D. Event Detail Screen
**Purpose:** Convert interest into registration while preserving trust and clarity.

**Layout order:**
1. Hero image
2. Event title and status
3. Key metadata block
4. Registration summary
5. Description/content
6. Package preview
7. Related or similar events
8. Sticky bottom CTA

**Detailed UI elements:**
- Hero section:
  - cover image
  - back button overlay
  - share action
- Identity block:
  - event title
  - organizer name if relevant
  - badge: `Open`, `Registered`, `Closed`, `Full`
- Metadata tiles:
  - date
  - time
  - location
  - remaining slots
- Registration card:
  - registration period
  - starting package price
  - summary text
  - note if registration requires organization data
- Description:
  - rich text content
  - agenda highlights
  - included benefits
- Package preview:
  - package name
  - price
  - participant limit summary
- Similar events section:
  - horizontal cards

**Sticky CTA states:**
- `Register Now` when registration is open
- `Continue Registration` if draft registration exists
- `View Registration` if already registered
- Disabled CTA with explanation when closed or offline

**Primary interactions:**
- Tap `Register Now` -> request magic-link -> open WebView Registration Flow
- Tap `Continue Registration` -> WebView Registration Flow
- Tap `View Registration` -> Registration Detail
- Tap similar event -> Event Detail for selected item

### E. Registration Detail Screen
**Purpose:** Let users review what they submitted after returning from a WebView flow.

**Detailed UI elements:**
- Status header card:
  - registration number
  - event title
  - badge: `Draft`, `Submitted`, `Confirmed`, `Cancelled`
- Participant list:
  - names
  - role/category
  - completeness indicator
- Package summary card:
  - selected package
  - quantity
  - total cost
- Linked invoice block:
  - invoice number
  - payment status
  - CTA: `Open Invoice`
- Timeline:
  - created
  - awaiting payment
  - confirmed

**Primary interactions:**
- Tap linked invoice -> Invoice Detail
- Tap edit/manage if still web-backed -> open WebView with magic-link

### F. Registrations List Screen
**Purpose:** Give users a dedicated top-level place to track all event registrations and their next required actions.

**Detailed UI elements:**
- Summary strip:
  - total active registrations
  - unpaid or pending count
  - nearest upcoming event date
- Filter chips:
  - `All`
  - `Draft`
  - `Submitted`
  - `Awaiting Payment`
  - `Confirmed`
- Search bar:
  - search by event title or registration number
- Registration cards:
  - event title
  - registration number
  - event date
  - status badge
  - invoice/payment summary
  - CTA label based on state: `Continue`, `View`, `Pay`
- Empty state:
  - no registrations yet
  - CTA: `Browse Events`

**Primary interactions:**
- Tap registration card -> Registration Detail
- Tap `Browse Events` -> Events tab
- Pull to refresh -> refetch registrations

### G. News List Screen
**Purpose:** Make portal news scannable and easy to revisit.

**Detailed UI elements:**
- Search field or simple list header
- Featured article card at top
- Standard article rows:
  - thumbnail
  - title
  - published date
  - excerpt
- Pull-to-refresh state

**Primary interactions:**
- Tap article -> News Detail

### H. News Detail Screen
**Purpose:** Provide readable long-form content.

**Detailed UI elements:**
- Hero image
- Article title
- Publish date and author/source line
- Rich content body
- Related article cards
- Share action in header

**Primary interactions:**
- Tap related article -> News Detail for selected article
- Back -> return to previous list position

### I. Invoice Detail Screen
**Purpose:** Help users understand what they owe and what action is required.

**Layout order:**
1. Invoice status card
2. Amount summary
3. Line items
4. Payment instructions / proof state
5. Action buttons

**Detailed UI elements:**
- Status card:
  - invoice number
  - badge: `Unpaid`, `Pending Verification`, `Paid`, `Expired`
  - due date
- Amount block:
  - subtotal
  - fees if any
  - total payable
- Line items list:
  - package name
  - quantity
  - amount
- Payment proof block:
  - uploaded/not uploaded state
  - last submitted timestamp
  - verification note if available
- Action area:
  - `Upload Payment Proof`
  - `Download Invoice`
  - `Contact Support` if needed

**Primary interactions:**
- Tap `Upload Payment Proof` -> request magic-link -> open Payment WebView
- Tap `Download Invoice` -> open temp file URL or native browser/file handler
- Tap linked registration reference -> Registration Detail if implemented

### J. Notifications Screen
**Purpose:** Give users a single place to review system updates and reminders.

**Detailed UI elements:**
- Header actions:
  - `Mark all as read`
- Notification list items:
  - unread indicator dot
  - icon by type: payment, registration, event, general
  - title
  - body preview
  - relative timestamp
- Empty state:
  - no notifications yet

**Primary interactions:**
- Tap notification -> mark as read -> deep link to relevant screen
- Tap `Mark all as read` -> optimistic update with rollback on failure

### K. Profile Screen
**Purpose:** Provide identity, organization context, and account controls.

**Detailed UI elements:**
- Profile header:
  - avatar/initials
  - full name
  - phone number
  - membership or role badge
- Organization card:
  - organization name
  - member role
  - status
- Menu sections:
  - `Edit Profile`
  - `Manage Organization`
  - `My Registrations`
  - `Payment History / Invoices`
  - `Notifications`
  - `Help`
  - `Logout`
- App info footer:
  - version
  - environment if non-production

**Primary interactions:**
- Tap `Edit Profile` -> open WebView
- Tap `Manage Organization` -> open WebView
- Tap `My Registrations` -> Registrations tab or Registration Detail
- Tap `Logout` -> confirm -> revoke token -> return to login gate

### L. Reusable WebView Bridge Modal
**Purpose:** Host all authenticated write flows in a consistent wrapper.

**Detailed UI elements:**
- Safe-area modal shell
- Header:
  - close button
  - screen title derived from flow
  - loading progress bar/spinner
- WebView body
- Optional bottom helper bar:
  - `Having trouble?`
  - support/contact shortcut

**Behavior rules:**
- Show blocking loader while magic-link is being requested.
- Detect deep link success: `pknportal://action-success?...`
- Detect close/cancel and ask confirmation if form progress may be lost.
- Emit refresh event for affected native stores after success.

---

## 5. Screen-to-Screen Interaction Flows

### A. App Launch and Authentication Flow
1. User opens app.
2. Splash/loading gate checks secure token and cached user state.
3. If token exists and is valid:
   - route to Dashboard tab
4. If token is missing or invalid:
   - open Hybrid Login modal
5. User completes login in WebView.
6. App receives token handoff.
7. Modal closes.
8. Dashboard loads with native content.

### B. Dashboard Entry Flow
1. User lands on Dashboard.
2. App shows cached dashboard data or skeletons.
3. Dashboard fetches fresh content.
4. User can branch to:
   - Notifications
   - Event Detail
   - News Detail
   - Registrations tab / Registration Detail

### C. Event Registration Flow
1. User opens Events tab.
2. User searches or scrolls event list.
3. User taps event card.
4. Event Detail opens.
5. User taps `Register Now`.
6. App requests `/webview/magic-link`.
7. App opens Registration WebView modal.
8. User completes Filament registration steps.
9. Portal redirects to `pknportal://action-success?type=registration`.
10. App closes WebView.
11. Event detail and related registration/invoice stores refresh.
12. User sees updated state:
   - `Registered`
   - `Continue Registration`
   - or linked registration summary

### D. Invoice Payment Proof Flow
1. User opens Registrations tab.
2. User taps a registration with unpaid or pending payment status.
3. Registration Detail opens.
4. User taps the linked invoice block.
5. Invoice Detail opens.
6. User taps `Upload Payment Proof`.
7. App requests magic-link.
8. Payment WebView modal opens.
9. User uploads proof in portal flow.
10. Portal redirects to `pknportal://action-success?type=payment`.
11. App closes modal and refreshes registrations and invoices.
12. Invoice Detail updates to `Pending Verification`.

### E. Notification Deep-Link Flow
1. User opens Notifications from Dashboard or Profile.
2. User taps a notification.
3. App marks it as read.
4. App opens the relevant destination based on payload:
   - event -> Event Detail
   - invoice -> Invoice Detail
   - registration -> Registration Detail
   - article -> News Detail
   - generic -> web or native fallback

### F. Profile Management Flow
1. User opens Profile tab.
2. User taps `Edit Profile` or `Manage Organization`.
3. App requests magic-link.
4. App opens WebView modal.
5. On successful save, portal redirects to success deep link.
6. App closes WebView and refreshes profile-related data.

### G. Logout Flow
1. User taps `Logout`.
2. Confirm dialog appears.
3. App calls logout endpoint and clears token/store.
4. User returns to unauthenticated gate.
5. Hybrid Login modal is shown again when needed.

---

## 6. Detailed Interaction Patterns

### Navigation transitions
- **Tab to tab**: instant switch, preserve scroll position where practical.
- **List to detail**: standard push transition.
- **Native to WebView**: full-screen modal slide-up.
- **WebView to native**: modal dismiss after success, cancel, or failure recovery.

### Touch targets and CTA rules
- Primary CTA should remain visible without requiring users to hunt for it.
- Use one primary CTA per screen section to reduce decision conflict.
- Secondary actions such as `Share`, `Download`, and `Help` should not visually compete with payment or registration actions.

### Confirmation rules
- Confirm before closing an in-progress WebView flow.
- Confirm logout.
- Avoid confirmation modals for simple navigation and safe read actions.

### Refresh rules
- Use optimistic refresh for notification read states.
- Use server-confirmed refresh after registration, payment proof, profile edit, and organization changes.

---

## 7. Component and State Guidelines

### Loading states
- First load with no cache:
  - use skeleton blocks for cards, metadata rows, and lists
- Revisit with cache:
  - show cached data immediately
  - refresh silently in background
- WebView launch:
  - show a blocking loader until signed URL is ready

### Empty states
- Must include:
  - simple icon or illustration
  - one-line explanation
  - one clear next action where appropriate

Recommended examples:
- No events: `Browse later or adjust your filters.`
- No registrations: `Your event registrations will appear here after you register.`
- No notifications: `Updates will appear here when something changes.`

### Error states
- Inline error for non-blocking list failures
- Full-page error when no usable content exists
- Toast/snackbar for transient action failures
- Retry button for dashboard, list, and magic-link request failures

### Offline states
- Read-only surfaces remain accessible using cached content.
- Mutation CTAs are disabled when connection is unavailable.
- Show reason text under disabled CTA:
  - `Internet connection is required for this action.`

---

## 8. Status Indicators and Visual Semantics

### Badge system
- **Success / Green**:
  - Paid
  - Registered
  - Active
  - Confirmed
- **Warning / Yellow or Amber**:
  - Unpaid
  - Pending Verification
  - Closing Soon
- **Danger / Red**:
  - Cancelled
  - Expired
  - Overdue
- **Neutral / Gray or Blue-gray**:
  - Draft
  - Informational
  - Archived

### List emphasis rules
- Unread notifications use a stronger title weight and indicator dot.
- Overdue invoices use stronger contrast than normal unpaid invoices.
- Registrations awaiting payment should show the next action prominently on the card.
- Registration-closed events should still be readable, but their CTA becomes secondary or disabled.

---

## 9. Accessibility and Usability Requirements

- Maintain strong contrast for badges, buttons, and banners.
- Ensure all icon-only controls have labels for accessibility services.
- Respect dynamic text scaling for titles, body copy, and metadata rows.
- Keep key CTAs reachable near the thumb zone on long screens through sticky bottom bars where needed.
- Avoid hiding critical status only by color; pair color with text labels.

---

## 10. Recommended Screen Build Order

1. Hybrid Login
2. Dashboard
3. Events List
4. Event Detail
5. Registrations List
6. Registration Detail
7. Invoice Detail
8. Notifications
9. Profile
10. Reusable WebView Bridge

This order matches the hybrid architecture and gives users a usable read experience early while preserving the portal's existing write flows.
