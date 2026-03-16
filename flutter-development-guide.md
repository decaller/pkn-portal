# Flutter Development Guide for PKN Portal App

This comprehensive guide outlines how to build a Flutter application that mirrors the functionality of the "Normal/Public User" portal available in the existing Laravel web application. The Flutter app will communicate with the Laravel backend via a secure REST API.

---

## 🏗️ 1. Architecture & Tech Stack

### Recommended Flutter Stack

- **Framework:** Flutter (latest stable)
- **State Management:** Riverpod (or BLoC/Provider depending on team setup)
- **Networking:** `dio` or standard `http` package for REST API communication
- **Routing:** `go_router` for structured, deep-linkable declarative routing
- **Local Storage:** `shared_preferences` or `flutter_secure_storage` (for auth tokens)
- **JSON Parsing:** `json_serializable` and `json_annotation` for typed data models from the API

---

## 🔌 2. API Backend Requirements (Laravel Side)

Currently, the web application relies on Livewire/Filament for its frontend interface. To support the Flutter application, **you must first implement JSON REST API endpoints** in your Laravel project using Laravel Sanctum.

**Steps for the backend:**

1.  **Issue API Tokens:** Use Laravel Sanctum (`auth:sanctum` middleware). Create an endpoint `/api/login` that returns a Bearer Token upon successful user authentication.
2.  **API Versioning:** Preface all routes with `/api/v1/` to ensure future compatibility.
3.  **Eloquent Resources:** Create Laravel API Resources (`php artisan make:resource`) for Models like `Event`, `Invoice`, `User`, `News` to ensure the API responses are consistently formatted and strip away sensitive backend columns.

---

## 📱 3. Core App Modules & Screens

Below is the required feature set, matched exactly to the web portal's capabilities.

### A. Authentication Module

- **Screens:** Login, Register, Forgot Password.
- **Flow:**
    - User inputs email and password.
    - App calls POST `/api/v1/login`.
    - Backend responds with User data + `access_token`.
    - App saves `access_token` securely and adds it to the `Authorization: Bearer {token}` header of all subsequent API requests.
    - Upon Register, App calls POST `/api/v1/register` to create the user account (which also assigns standard permissions).

### B. Home & Dashboard

- **Screen:** Main Dashboard (Bottom Navigation Tab 1)
- **Features:**
    - **Hero Section:** Highlights latest, highly-anticipated Events.
    - **News Feed:** List of recent `News` articles.
    - **Testimonials Carousel:** Displaying recent feedback.
- **API Needs:**
    - GET `/api/v1/home` (Aggregation endpoint) OR
    - GET `/api/v1/events?featured=true`
    - GET `/api/v1/news?limit=5`

### C. Event Discovery & Details

- **Screens:** Events List, Event Filter/Search, Event Details.
- **Features:**
    - List active events (pagination via API).
    - Filter events by category, dates, or search by name.
    - View Event details (description, quota, schedule, pricing).
- **API Needs:**
    - GET `/api/v1/events` (with query parameters `?search=...&page=1`)
    - GET `/api/v1/events/{id}`

### D. Event Registration Workflow (Crucial)

- **Screens:** Registration Wizard.
- **Flow:**
    - **Step 1:** User selects Event and begins registration.
    - **Step 2:** Define number of participants.
    - **Step 3:** Input details for each `RegistrationParticipant` (Name, Email, Phone).
    - **Step 4:** Select or input overarching `Organization` details.
    - **Step 5:** Final confirmation & submission.
- **API Needs:**
    - POST `/api/v1/events/{id}/register`
    - _Payload shape:_ `{"participants": [...], "organization_id": ...}`
    - _Response:_ Returns the generated `EventRegistration` and `Invoice` references.

### E. Invoices & Payments Management

- **Screens:** Invoice List, Invoice Details.
- **Features:**
    - A tab dedicated to the user's financial transactions.
    - Display list of `Invoice`s with their status (Pending, Paid, Partial, Overpaid).
    - Ability to view breakdown (`InvoiceItem`s).
    - Button to download the Invoice PDF using the existing `InvoicePdfService` logic (adapted for API).
- **API Needs:**
    - GET `/api/v1/invoices`
    - GET `/api/v1/invoices/{id}`
    - GET `/api/v1/invoices/{id}/download` (Return a PDF buffer or temporary signed fast-download URL)

### F. Notifications Hub

- **Screens:** Notifications Page (Accessible via a Bell Icon in the AppBar).
- **Features:**
    - Mirrors the recently built Facebook-style notification bell.
    - Shows alerts for: Event Registration confirmed, Payment Reminders, Payment Approved, Empty Spots available.
    - Clicking a notification routes the user to the relevant screen (e.g., specific invoice).
- **API Needs:**
    - GET `/api/v1/notifications` (Unread & Read)
    - POST `/api/v1/notifications/{id}/mark-as-read`

### G. Profile & Configuration

- **Screens:** Profile settings, linked Organizations, attached Documents.
- **API Needs:**
    - GET `/api/v1/user/profile`
    - PUT `/api/v1/user/profile`

---

## 🛠 4. Standard Implementation Steps

1.  **Prepare the Laravel Backend:**
    Run `php artisan install:api` to scaffold base API routing if not present. Build out the controllers (e.g., `Api\EventController`, `Api\InvoiceController`). Ensure all routes are protected by Sanctum middleware `auth:sanctum` where required.
2.  **Generate Flutter Project:**
    Run `flutter create pkn_portal_app --org id.or.pkn`
3.  **Setup Theming:**
    Use Google Fonts, define an `AppTheme` class mapping the portal's primary colors, button styles, and border radiuses. Keep UI clean, utilizing Card layouts for Events and Invoices.
4.  **Build Core Networking Layer:**
    Implement a Singleton API Client (`Dio` interceptor) that handles auto-injecting the bearer token and gracefully managing `401 Unauthorized` responses by kicking the user back to the Login screen.
5.  **Develop UI iteratively:**
    Build screens roughly following the module list. Use `ListView.builder` for lists like Events and Invoices, ensuring network pagination is handled.

---

## 💡 5. Important Considerations

- **File Uploads / Downloads:** Since the web portal involves file uploads (like documents or payment proofs) and limits sizes to 10MB/1GB, the Flutter app must use `dio` `MultipartFile` and compress images where possible to adhere to server configurations.
- **Webviews:** Some content (like complex HTML Rich Text stored in News articles or long Testimonials) may require `flutter_html` or `webview_flutter` to render appropriately on mobile.
- **State Syncing:** Ensure that when a user registers for an Event, the Dashboard and Event List states are refreshed automatically.

---

## 💾 6. Offline Support & Data Persistence

To ensure a smooth user experience even when internet connectivity is spotty, implement offline caching for both API data and downloaded documents (like Invoices or Event materials).

### A. Preserving API Data

- **Local Database:** Use `sqflite` or `hive` (NoSQL) to cache API responses. `hive` is often faster and easier to set up for simple JSON caching.
- **Repository Pattern:** Wrap your API calls in a Repository class. When fetching data (e.g., the Events List):
    1.  Immediately return the cached data from the local database to display the UI instantly.
    2.  Perform a background fetch to the API.
    3.  If the API call succeeds, update the cache and refresh the UI state.
    4.  If the API call fails (no internet), the user still sees the cached data.

### B. Managing Downloaded Documents

- **Path Provider:** Use the `path_provider` package to access the device's local filesystem (e.g., `getApplicationDocumentsDirectory()`).
- **Downloading:** When a user downloads an Invoice PDF or attachment:
    1.  Check if the file already exists locally in the app's directory. If it does, open it directly using a package like `open_filex`.
    2.  If not, use `dio` to download the file from the API to the local path.
- **Permissions:** You may need to request storage permissions (using `permission_handler`) if you plan to save files directly to the user's generic Downloads folder on Android, though saving to the app's sandboxed document directory is often easier and doesn't require extra permissions.
