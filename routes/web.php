<?php

use App\Filament\Admin\Resources\Events\Schemas\EventInfolist;
use App\Http\Controllers\Payments\MidtransWebhookController;
use App\Models\Event;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::view('/', 'home')->middleware(['throttle:60,1', 'cacheResponse'])->name('home');

Route::get('/locale/{locale}', function (string $locale) {
    $supportedLocales = ['en', 'id'];

    if (in_array($locale, $supportedLocales, true)) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->middleware('throttle:30,1')->name('locale.switch');

Route::get(
    '/login',
    fn () => redirect()->route('filament.user.auth.login'),
)->name('login');
Route::get(
    '/register',
    fn () => redirect()->route('filament.user.auth.register'),
)->name('register');

Route::post('/payments/midtrans/notifications', MidtransWebhookController::class)
    ->middleware('doNotCacheResponse')
    ->name('payments.midtrans.notifications');

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

Route::get('/temporary/invoices/{invoice}/download', function (
    Request $request,
    Invoice $invoice,
    InvoicePdfService $service,
) {
    abort_unless($request->integer('user') === $invoice->registration()->value('booker_user_id'), 403);

    return $service->download($invoice);
})->middleware('signed')->name('invoices.temporary-download');

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

Route::middleware('auth')
    ->get('/events/{event}/sessions/download-all', function (Event $event) {
        $files = [];
        $rundown = $event->rundown ?? [];
        foreach ($rundown as $index => $session) {
            $sessionData = $session['data'] ?? [];
            $sessionTitle = $sessionData['title'] ?? ('Session '.($index + 1));
            $sessionFiles = $sessionData['session_files'] ?? [];

            if (is_array($sessionFiles)) {
                foreach ($sessionFiles as $file) {
                    if (filled($file) && ! str_starts_with($file, 'http')) {
                        $files[] = [
                            'source' => storage_path('app/public/'.$file),
                            'zip_path' => str($sessionTitle)->slug().'/'.basename($file),
                        ];
                    }
                }
            }
        }

        if (empty($files) || ! class_exists('ZipArchive')) {
            return redirect()->back()->with('error', __('No files found to download.'));
        }

        $zipFileName = str($event->slug)->slug().'-session-files.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        $zip = new ZipArchive;
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (file_exists($file['source'])) {
                    $zip->addFile($file['source'], $file['zip_path'] ?: basename($file['source']));
                }
            }
            $zip->close();
        }

        return response()->download($tempFile, $zipFileName)->deleteFileAfterSend(true);
    })
    ->name('events.sessions.download-all');
