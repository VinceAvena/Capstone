<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * The history of a complaint. Each status change and each message between
 * the market administration and the vendor is one row, which is what makes
 * "resolution tracking" possible.
 *
 *   type     = status | message | system
 *   audience = all    (shopper, vendor and staff can see it)
 *              vendor (only the vendor and staff can see it)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('status');
            $table->string('audience', 20)->default('all');
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20)->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_logs');
    }
};
