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
        Schema::table('paragraphs', function (Blueprint $table) {
            $table->integer('word_count')->default(0)->after('content');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->integer('word_count')->default(0)->after('title');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->integer('word_count')->default(0)->after('description');
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->integer('word_count')->default(0)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('paragraphs', function (Blueprint $table) {
            $table->dropColumn('word_count');
        });

        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('word_count');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn('word_count');
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn('word_count');
        });
    }
};
