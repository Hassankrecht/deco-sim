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
    if (!Schema::hasTable('home_settings')) {
        return;
    }

    Schema::table('home_settings', function (Blueprint $table) {
        $columns = [
            'theme_primary',
            'theme_dark',
            'theme_text',
            'theme_bg',
            'headings_color',
            'body_text_color',
            'link_color',
            'btn_global_primary_color',
            'btn_global_primary_style',
            'btn_global_secondary_color',
            'btn_global_secondary_style',
        ];

        foreach ($columns as $column) {
            if (!Schema::hasColumn('home_settings', $column)) {
                $table->string($column)->nullable();
            }
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_settings', function (Blueprint $table) {
            //
        });
    }
};
