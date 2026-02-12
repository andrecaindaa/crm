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
        Schema::table('deal_activities', function (Blueprint $table) {
            $table->text('description')->nullable()->after('label');
            $table->timestamp('due_at')->nullable()->after('meta');
            $table->timestamp('completed_at')->nullable()->after('due_at');
        });
    }

    public function down(): void
    {
        Schema::table('deal_activities', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'due_at',
                'completed_at',
            ]);
        });
    }

};
