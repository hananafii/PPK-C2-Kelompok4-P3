<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projects') && ! Schema::hasColumn('projects', 'owner_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->foreignId('owner_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }

        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (! Schema::hasColumn('tasks', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
                if (! Schema::hasColumn('tasks', 'priority')) {
                    $table->string('priority')->default('medium')->after('description');
                }
                if (! Schema::hasColumn('tasks', 'deadline')) {
                    $table->date('deadline')->nullable()->after('priority');
                }
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('email');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['description', 'priority', 'deadline']);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
