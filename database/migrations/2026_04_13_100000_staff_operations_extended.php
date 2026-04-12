<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('internal_notes')->nullable();
            $table->timestamp('status_changed_at')->nullable();
        });

        DB::table('orders')->whereNull('status_changed_at')->update([
            'status_changed_at' => DB::raw('COALESCE(updated_at, created_at)'),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('staff_can_orders')->default(true);
            $table->boolean('staff_can_reviews')->default(true);
            $table->boolean('staff_can_inventory')->default(true);
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false);
            $table->timestamp('hidden_at')->nullable();
            $table->foreignId('hidden_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('hidden_reason')->nullable();
        });

        Schema::create('staff_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action', 120);
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index('created_at');
        });

        Schema::create('product_review_moderations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 32);
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });

        Schema::create('review_rejection_templates', function (Blueprint $table) {
            $table->id();
            $table->string('label', 120);
            $table->text('body');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_rejection_templates');
        Schema::dropIfExists('product_review_moderations');
        Schema::dropIfExists('staff_activities');

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hidden_by');
            $table->dropColumn(['is_hidden', 'hidden_at', 'hidden_reason']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['staff_can_orders', 'staff_can_reviews', 'staff_can_inventory']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['internal_notes', 'status_changed_at']);
        });
    }
};
