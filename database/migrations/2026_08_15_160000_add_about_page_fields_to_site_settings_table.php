<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('about_page_title')->nullable()->after('about_text');
            $table->string('about_page_subtitle')->nullable()->after('about_page_title');
            $table->longText('about_page_body')->nullable()->after('about_page_subtitle');
        });

        $defaultBody = <<<'TXT'
Established in the year 2016, we "R.P. Trading Company" are a leading *Wholesaler* of a wide range of *Disposable Plate, Plastic Box, Disposable Bowl, Disposable Tray, Pasta Tray,* and much more.

Under the valuable guidance of our mentor, *Mr. Rahul Aggarwal*, we are continuously moving towards success in this field.
TXT;

        DB::table('site_settings')->update([
            'about_page_title' => 'About Us',
            'about_page_subtitle' => null,
            'about_page_body' => $defaultBody,
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_page_title',
                'about_page_subtitle',
                'about_page_body',
            ]);
        });
    }
};
