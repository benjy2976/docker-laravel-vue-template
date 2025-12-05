<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->boolean('is_menu')->default(false)->after('guard_name');
            $table->string('menu_label')->nullable()->after('is_menu');
            $table->string('menu_path')->nullable()->after('menu_label');
            $table->string('icon')->nullable()->after('menu_path');
            $table->unsignedBigInteger('parent_id')->nullable()->after('icon');
            $table->integer('sort_order')->default(0)->after('parent_id');

            $table->foreign('parent_id')
                ->references('id')
                ->on('permissions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['description', 'is_menu', 'menu_label', 'menu_path', 'icon', 'parent_id', 'sort_order']);
        });
    }
};
