<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->cascadeOnDelete();
            $table->string('provider')->default('midtrans');
            $table->string('order_id')->unique();
            $table->string('status');
            $table->decimal('gross_amount', 12, 2);
            $table->string('currency', 3)->default('IDR');
            $table->string('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_transaction_status')->nullable();
            $table->string('midtrans_payment_type')->nullable();
            $table->string('midtrans_fraud_status')->nullable();
            $table->json('raw_snap_response')->nullable();
            $table->json('raw_notification_payload')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('event_registration_id');
            $table->index('status');
            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
