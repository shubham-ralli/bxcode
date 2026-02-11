<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'first_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('first_name')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('users', 'last_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('last_name')->nullable()->after('first_name');
            });
        }

        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('subscriber')->after('email');
            });
        }

        if (!Schema::hasColumn('users', 'bio')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('bio')->nullable()->after('password');
            });
        }

        if (!Schema::hasColumn('users', 'profile_image_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('profile_image_id')->nullable()->after('bio');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'role', 'bio', 'profile_image_id']);
        });
    }
};
