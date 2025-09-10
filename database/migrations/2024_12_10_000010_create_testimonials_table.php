<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name', 100);
            $table->string('company', 100)->nullable();
            $table->string('position', 100)->nullable();
            $table->text('testimonial');
            $table->tinyInteger('rating')->unsigned()->check('rating >= 1 AND rating <= 5');
            $table->string('photo_url')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
            $table->index(['is_approved', 'is_featured']);
            $table->index(['service_id', 'is_approved']);
            $table->index(['rating', 'is_approved']);
            $table->index(['sort_order', 'is_approved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};