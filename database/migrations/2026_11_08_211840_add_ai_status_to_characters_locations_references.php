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
        Schema::table('characters', function (Blueprint $table) {
            $table->enum('status', ['confirmed', 'ai_created', 'rejected'])->default('confirmed')->after('image');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->enum('status', ['confirmed', 'ai_created', 'rejected'])->default('confirmed')->after('image');
        });

        Schema::table('references', function (Blueprint $table) {
            $table->enum('status', ['confirmed', 'ai_created', 'rejected'])->default('confirmed')->after('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('references', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
