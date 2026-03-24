# Agent Documentation - Access Control

This document outlines the access control rules for the PKN Portal application.

## User Roles & Access

- **Super Admin**: 
  - URL Access: `/admin`
  - Capabilities: Full system management, global resource access.
- **Admin (Organization Admin)**:
  - URL Access: `/user`
  - Capabilities: Manage users and resources within their owned organization. Restricted from `/admin`.
- **User (Normal User)**:
  - URL Access: `/user`
  - Capabilities: Participate in events, join or create new organizations. Restricted from `/admin`.

## Redirection Logic

- **Protected Routes**: Any unauthorized access to protected `/admin` routes (non-super admins) will be automatically redirected to `/user`.
- **Public Routes**: Administrative authentication routes (e.g., `/admin/login`, `/admin/logout`) remain publicly accessible to allow Super Admins to authenticate.

## API Documentation

- **MANDATORY**: Whenever you modify API routes, controllers, or resources, you must regenerate the API documentation using Scribe:
  ```bash
  vendor/bin/sail artisan scribe:generate
  ```

## Hybrid Event Registration Flow (Mobile)

The mobile app employs a hybrid architecture for event registrations to reduce friction while keeping maintenance low:
- **Native Context (Mobile App):** The user browses events and creates the initial registration native-side (`POST /api/v1/registrations`). The app also lists past and current registrations (`GET /api/v1/registrations`).
- **WebView Context (Web Portal):** For complex fulfillment actions like filling in participant details, downloading PDF invoices, and making payments (Midtrans Snap), the mobile app redirects the user via a secure WebView to the corresponding web portal pages. Native APIs for participant CRUD or Midtrans tokens are deprecated or removed in favor of this web wrapper approach.
