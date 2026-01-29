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
        Schema::create('deal_proposals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
        $table->string('file_path');
        $table->string('original_name');
        $table->timestamp('sent_at')->nullable();
        $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_proposals');
    }
};
