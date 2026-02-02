<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('deal_follow_ups', function (Blueprint $table) {
        $table->id();
        $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
        $table->timestamp('next_send_at')->nullable();
        $table->boolean('active')->default(true);
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_follow_ups');
    }
};
