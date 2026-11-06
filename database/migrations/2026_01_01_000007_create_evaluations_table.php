<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * One ISO/IEC 25010 questionnaire response per user. The ratings are kept
 * as JSON, for example {"usability.0": 5, "usability.1": 4, ...}.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('respondent_type', 30);  // it_expert | admin_personnel | inspector | vendor | shopper
            $table->string('respondent_group', 20); // it_expert | end_user
            $table->json('answers');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
