<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('deal_follow_ups', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('next_send_at');
        });
    }

    public function down(): void
    {
        Schema::table('deal_follow_ups', function (Blueprint $table) {
            $table->dropColumn('sent_at');
        });
    }
};
