<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100);
            $table->string('phone', 20)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('subject', 200)->nullable();
            $table->longText('message');
            $table->string('service_interest', 100)->nullable();
            $table->string('budget_range', 50)->nullable();
            $table->enum('preferred_contact', ['email', 'phone', 'both'])->default('email');
            $table->enum('status', ['new', 'read', 'replied', 'closed'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('replied_at')->nullable();

            $table->index(['status', 'created_at']);
            $table->index(['service_interest', 'status']);
            $table->index(['budget_range', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};