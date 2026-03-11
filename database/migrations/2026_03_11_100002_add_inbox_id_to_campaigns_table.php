<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->unsignedInteger('inbox_id')
                  ->nullable()
                  ->after('created_by')
                  ->index()
                  ->comment('ID de l\'inbox Chatwoot utilisé pour l\'envoi');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('inbox_id');
        });
    }
};
