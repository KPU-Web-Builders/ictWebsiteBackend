<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('role', 100);
            $table->text('bio')->nullable();
            $table->string('photo_url')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->json('skills')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('joined_date')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['is_active', 'sort_order']);
            $table->index(['is_verified', 'is_active']);
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};