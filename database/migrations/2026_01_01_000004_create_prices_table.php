<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * The "posted price": what one stall charges for one commodity on one day.
 * The unique key means a stall has only one price per commodity per day;
 * posting again on the same day updates the row instead of duplicating it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stall_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commodity_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->date('price_date');
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['stall_id', 'commodity_id', 'price_date']);
            $table->index('price_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
