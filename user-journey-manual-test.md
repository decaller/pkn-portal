# PKN Portal - User Journey Manual Test Plan

This document outlines the manual testing steps to verify the core user journeys of the PKN Portal application. It covers usual paths (happy paths) and edge cases across the Public, User, and Admin panels.

---

## 1. Public Journey (Unauthenticated)

**Goal:** Ensure general visitors can access public information appropriately.

- **1.1 Accessing the Landing Page**
    - **Usual:** Navigate to the main URL. Verify the page loads correctly with all expected elements (header, footer, hero section).
    - **Edge:** Access on a mobile device to ensure the layout is responsive.
- **1.2 Browsing Events & News**
    - **Usual:** View the list of available events and news articles. Click on an event/news item to read the full details.
    - **Edge:** Trigger pagination or filtering (if applicable) and verify the correct items are displayed. Verify behavior when there are no active events or news.
- **1.3 Attempting Restricted Actions**
    - **Usual:** Click "Register" on an event from the public page.
    - **Expected Outcome:** The user should be redirected to the Login or Registration page.

---

## 2. User Journey (Registrant / Participant)

**Goal:** Verify a user can register, manage their organization, and successfully register for events.

### 2.1 Registration & Authentication

- **Usual:**
    - Navigate to the registration page.
    - Fill in valid details (Name, Email, Password, etc.).
    - Submit the form. User is successfully registered and logged in (or sent an email verification if applicable).
- **Edge Cases:**
    - **Duplicate Email:** Attempt to register with an email that already exists. Should show a clear validation error.
    - **Weak Password:** Enter a password that doesn't meet requirements. Should show an error.
    - **Missing Fields:** Submit the form with required fields left blank. Should show validation errors.

### 2.2 Organization Management

- **Usual:**
    - Navigate to the User Dashboard.
    - Create a new organization with valid details.
    - Join an existing organization by searching or using a code.
- **Edge Cases:**
    - **Duplicate Organization:** Attempt to create an organization with a name that is already taken.
    - **Invalid Join Attempt:** Attempt to join a non-existent organization.

### 2.3 Event Registration Flow

- **Usual:**
    - Select an active event from the dashboard or public page.
    - Click to register.
    - Read the payment instructions (rendered correctly via rich text).
    - Complete the registration form.
    - Add the required number of participants.
    - Upload valid proof of payment (image/PDF).
    - Submit the registration. Verify it appears on the dashboard with a "Pending" or "Awaiting Approval" status.
- **Edge Cases:**
    - **Full Event:** Attempt to register for an event where `participants_count` has reached the maximum allowed limit. Should be blocked or show a "Full" message.
    - **Past Event:** Attempt to register for an event whose registration deadline has passed.
    - **Double Registration:** Attempt to register for the same event twice with the same account. Should ideally prevent duplicate main registrations.
    - **Exceeding Participants Limit:** Attempt to add more individual participants than the event allows per registration.
    - **Invalid Payment File:** Upload a file format that is not allowed (e.g., `.exe` or a file that is too large).

### 2.4 User Dashboard & Widgets

- **Usual:**
    - Log in and land on the dashboard.
    - Verify the "Welcome Widget" displays the latest event registration status correctly.
    - Use the widget buttons to quickly add participants or upload payments if the registration is still incomplete.
    - Verify the button transitions correctly (e.g., changes to an "Event Page" link once payment is verified and participants are fully added).
    - Once an admin approves the payment, verify the status updates to "Approved" on the dashboard.

---

## 3. Admin Journey

**Goal:** Ensure administrators can manage the platform's content and approve registrations.

### 3.1 Admin Authentication

- **Usual:** Log into the `/admin` panel using valid administration credentials. Verify access is granted and the admin dashboard is visible.
- **Edge:** Normal users attempting to access `/admin` should be blocked (403 or redirected).

### 3.2 Event Management

- **Usual:**
    - Navigate to the Events resource.
    - Create a new event, filling out all fields including titles, dates, max participants, and rich text `payment_instructions`.
    - Edit an existing event.
    - Delete or archive an event.
- **Edge Cases:**
    - **Invalid Dates:** Set an event end date that occurs _before_ the start date. Form should fail validation.
    - **Modifying Active Events:** Edit an event that already has users registered for it. Ensure existing registrations remain intact.

### 3.3 Registration & Payment Approval

- **Usual:**
    - Navigate to Event Registrations.
    - View a pending registration submitted by a user.
    - Inspect the proof of payment file.
    - Approve the registration. The user's status should update.
    - Reject a registration (e.g., invalid payment).
- **Edge Cases:**
    - Approve a registration that pushes the total accepted participants over the event's maximum limit (admin should ideally be warned or blocked depending on requirements).

### 3.4 User & Organization Management

- **Usual:**
    - View the list of registered users.
    - Edit user details or roles if necessary.
    - View and edit organizations.

### 3.5 News / Content Management

- **Usual:**
    - Create, edit, and delete news articles.
    - Verify the content updates correctly on the Public panel.
