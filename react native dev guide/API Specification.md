# PKN Portal Mobile API Specification v1

Base URL: `https://your-domain.com/api/v1`

Authentication:

- Laravel Sanctum bearer tokens for the mobile app
- cookie session only inside WebView flows after magic-link handoff

Response rules:

- JSON only
- use Laravel API Resources
- use absolute URLs for images and files
- return `null` for missing optional values

## 1. Authentication

### `POST /auth/login`

Purpose:

- authenticate a mobile user using the same credential style as the current portal

Request:

```json
{
  "phone_number": "08123456789",
  "password": "secret-password"
}
```

Response:

```json
{
  "success": true,
  "message": "Login success.",
  "token": "plain-text-token",
  "user": {
    "id": 1,
    "name": "User Name",
    "phone_number": "08123456789",
    "email": "phone-08123456789@local.pkn"
  }
}
```

### `POST /auth/logout`

Auth required: yes

Purpose:

- revoke the current token

### `GET /auth/me`

Auth required: yes

Purpose:

- return the currently authenticated mobile user

## 2. WebView bridge

### `GET /webview/magic-link`

Auth required: yes

Query params:

- `redirect`: target Filament path, for example `/user/event-registrations/create?event_id=5`
- `source`: should be `mobile`

Response:

```json
{
  "url": "https://your-domain.com/mobile/webview-login?...signed..."
}
```

Purpose:

- convert bearer-authenticated app state into a one-time web session entry point for Filament pages

## 3. Home dashboard

### `GET /mobile-dashboard`

Auth required: no

Purpose:

- aggregate content needed for the app home screen

Suggested payload:

```json
{
  "featured_events": [],
  "latest_news": [],
  "testimonials": []
}
```

## 4. Events

### `GET /events`

Auth required: no

Query params:

- `search`
- `page`
- `category`
- `status`

Purpose:

- return paginated published events suitable for mobile list rendering

### `GET /events/{id}`

Auth required: no

Purpose:

- return event details including content needed for the native detail screen

Recommended fields:

- title
- slug
- summary
- description
- event date
- location
- available spots
- registration availability
- registration package summary
- image URLs

### `GET /events/{id}/similar`

Auth required: no

Optional helper endpoint for detail screens.

## 5. News

### `GET /news`

Auth required: no

### `GET /news/{id}`

Auth required: no

Return article content plus absolute banner URLs.

## 6. Registrations

### `GET /my-registrations`

Auth required: yes

Purpose:

- list the authenticated user's registrations created through the portal

### `GET /my-registrations/{id}`

Auth required: yes

Purpose:

- show registration detail, participants, invoice references, and current status

Note:

- creation remains WebView-based in v1

## 7. Invoices

### `GET /invoices`

Auth required: yes

Purpose:

- list invoices for registrations visible to the current user

### `GET /invoices/{id}`

Auth required: yes

Purpose:

- show invoice line items, amounts, due date, and status

### `GET /invoices/{id}/download`

Auth required: yes

Recommended response:

```json
{
  "download_url": "https://your-domain.com/temporary/invoice.pdf"
}
```

Alternative:

- stream the PDF directly if you prefer

## 8. Notifications

### `GET /notifications`

Auth required: yes

Purpose:

- list database notifications for the current user

### `POST /notifications/{id}/mark-read`

Auth required: yes

### `POST /notifications/mark-all-read`

Auth required: yes

## 9. Profile

### `GET /user/profile`

Auth required: yes

Purpose:

- return profile summary, linked organizations, and simple account fields

### `PUT /user/profile`

Auth required: yes

Purpose:

- support lightweight native profile edits if needed

If profile editing remains complex, keep updates in WebView for v1 and expose this endpoint later.

## 10. Formatting and safety rules

1. Never return raw Eloquent models directly.
2. Strip sensitive or admin-only fields.
3. Keep enums predictable and stable.
4. Use pagination for mobile lists.
5. Make file and image URLs absolute.
6. Keep mobile endpoints under `routes/api.php` with `auth:sanctum` where required.

## 11. Compatibility note

This spec intentionally standardizes on `/api/v1/...`.

If the repository currently contains exploratory tests for `/api/auth/...`, either:

1. update those tests to `/api/v1/auth/...`, or
2. provide temporary aliases during migration

Do not let the documentation and the actual route map diverge.
