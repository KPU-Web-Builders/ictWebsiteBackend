<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->string('feature_name', 200);
            $table->boolean('is_included');
            $table->string('feature_value', 100)->nullable();
            $table->text('tooltip')->nullable();
            $table->integer('sort_order')->default(0);

            $table->foreign('plan_id')->references('id')->on('hosting_plans')->onDelete('cascade');
            $table->index(['plan_id', 'is_included']);
            $table->index(['plan_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};