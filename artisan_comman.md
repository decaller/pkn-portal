# Artisan Commands Reference

Bismillah, here is the list of important and custom Artisan commands available in this project.

## Custom Commands

These commands are specific to the **pkn-portal** application logic.

| Command | Description |
|:---|:---|
| `php artisan api:update-results` | Update `react native dev guide/api_result` folder with updated API result JSON files from the current database state. |
| `php artisan documents:sync-missing-session-files` | Queue document extraction (Tika) for session files that are missing from the `documents` table. |
| `php artisan documents:queue-missing-covers` | Queue cover generation for documents that are missing cover images. |
| `php artisan documents:backfill-covers` | Queue cover generation for **all** existing documents (use `--force` to regenerate). |
| `php artisan events:sync-past` | Clean up completed events, append them to the User `past_events` history array, and mark registrations as `Closed`. |
| `php artisan notifications:send-registration-reminders` | Send weekly reminders to users who have not registered for open events. |
| `php artisan notifications:send-participant-slot-reminders` | Send reminders every 3 days to users whose registration has empty participant slots. |
| `php artisan notifications:send-payment-reminders` | **Legacy**: Replaced by Midtrans Snap. Retained for compatibility but only displays an info message. |

---

## Important Framework & Package Commands

These are standard or package-provided commands frequently used in development and maintenance.

### Database & Testing
* `php artisan migrate` - Run the database migrations.
* `php artisan db:seed` - Seed the database with records (default: `DatabaseSeeder`).
* `php artisan test` - Run the Pest test suite. Use `--compact` for cleaner output.

### Filament (Admin Panel)
* `php artisan filament:upgrade` - Upgrade Filament assets and configuration after an update.
* `php artisan filament:optimize` - Cache Filament components for better performance.
* `php artisan filament:clear-cached-components` - Clear cached Filament components.

### API Documentation (Scribe)
* `php artisan scribe:generate` - Generate/Update the API documentation based on controller annotations and routes.

### Queues & Performance
* `php artisan horizon` - Start the Horizon dashboard and queue workers.
* `php artisan optimize:clear` - Remove the configuration, route, and compiled view cache files.
* `php artisan responsecache:clear` - Clear the Spatie response cache.

### Maintenance
* `php artisan activitylog:clean` - Clean up old records from the activity log.
* `php artisan pail` - Stream your application logs directly to the console.

---

> [!TIP]
> Always prefix these commands with `vendor/bin/sail` if you are running the application using Laravel Sail:
> `vendor/bin/sail artisan ...`
