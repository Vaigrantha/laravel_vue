<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->foreignId('author_id')->nullable()->after('author')->constrained('users')->nullOnDelete();
            $table->string('title_search')->nullable()->after('title');
            $table->string('author_search')->nullable()->after('author');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('author_id');
            $table->dropColumn(['title_search', 'author_search']);
        });
    }
};
