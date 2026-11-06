<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * One row per market stall. A stall belongs to a vendor (a user) and holds
 * the repeat-violation flag that the administrator monitors.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stalls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // the vendor
            $table->string('stall_number', 20)->unique();
            $table->string('name');
            $table->string('section');
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending'); // pending | active | inactive
            $table->boolean('is_flagged')->default(false);
            $table->timestamp('flagged_at')->nullable();
            $table->timestamp('flag_cleared_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stalls');
    }
};
