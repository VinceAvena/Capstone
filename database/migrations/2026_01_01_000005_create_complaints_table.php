<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * An overpricing complaint. The posted price is copied into the complaint
 * at the moment it is filed, so later price changes by the vendor cannot
 * alter what the administrator is verifying.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no', 30)->unique();
            $table->foreignId('shopper_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('stall_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commodity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('price_id')->nullable()->constrained('prices')->nullOnDelete();
            $table->decimal('posted_price', 10, 2);   // snapshot of the stall's posted price
            $table->date('posted_price_date');        // the day that posted price was entered
            $table->decimal('reported_price', 10, 2); // what the shopper says was charged
            $table->text('description')->nullable();
            $table->string('photo_path');             // photo evidence (private storage)
            $table->string('status', 20)->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable(); // set when verified as overpricing
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
