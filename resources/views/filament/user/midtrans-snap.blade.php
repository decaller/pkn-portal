@php
    $snapUrl = config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<script src="{{ $snapUrl }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    document.addEventListener('livewire:init', () => {
        const notify = (title, status = 'info') => {
            if (! window.FilamentNotification) {
                return;
            }

            new window.FilamentNotification().title(title)[status]().send();
        };

        Livewire.on('open-midtrans-snap', ({ token }) => {
            if (! token || ! window.snap) {
                notify(@js(__('Unable to start payment')), 'danger');
                return;
            }

            window.snap.pay(token, {
                onSuccess: () => {
                    notify(@js(__('Payment completed')), 'success');
                    window.location.reload();
                },
                onPending: () => {
                    notify(@js(__('Waiting for Midtrans confirmation')), 'info');
                    window.location.reload();
                },
                onError: () => {
                    notify(@js(__('Unable to start payment')), 'danger');
                    window.location.reload();
                },
                onClose: () => {
                    notify(@js(__('Payment window closed')), 'warning');
                },
            });
        });
    });
</script>
