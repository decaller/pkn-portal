<?php

use App\Filament\Admin\Resources\Events\Schemas\EventInfolist;
use App\Models\Event;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/locale/{locale}', function (string $locale) {
    $supportedLocales = ['en', 'id'];

    if (in_array($locale, $supportedLocales, true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

Route::get(
    '/login',
    fn () => redirect()->route('filament.user.auth.login'),
)->name('login');
Route::get(
    '/register',
    fn () => redirect()->route('filament.user.auth.register'),
)->name('register');

Route::middleware('auth')
    ->get('/invoices/{invoice}/download', function (
        Invoice $invoice,
        InvoicePdfService $service,
    ) {
        $invoice->loadMissing('registration');
        Gate::authorize('view', $invoice->registration);

        return $service->download($invoice);
    })
    ->name('invoices.download');

Route::middleware('auth')
    ->get('/admin/events/{event}/participants/download', function (Event $event) {
        abort_unless((bool) auth()->user()?->isMainAdmin(), 403);

        $rows = EventInfolist::participantsTableRows($event);
        $filename = "event-{$event->id}-participants.csv";

        return response()->streamDownload(function () use ($rows): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, [
                'registration_id',
                'participant_name',
                'participant_email',
                'participant_phone',
                'organization_name',
                'booker_name',
            ]);

            foreach ($rows as $row) {
                fputcsv($stream, [
                    $row['registration_id'] ?? '',
                    $row['participant_name'] ?? '',
                    $row['participant_email'] ?? '',
                    $row['participant_phone'] ?? '',
                    $row['organization_name'] ?? '',
                    $row['booker_name'] ?? '',
                ]);
            }

            fclose($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })
    ->name('admin.events.participants.download');
