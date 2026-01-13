<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('become_instructor_contents', function (Blueprint $table) {
            $table->id();

            /* ================= HERO SECTION ================= */
            $table->longText('hero_title')->nullable();          // HTML string (can contain HTML tags)
            $table->longText('hero_description')->nullable();     // TipTap HTML content
            $table->string('hero_button_text')->nullable();
            $table->string('hero_image')->nullable();

            /* ================= BENEFITS SECTION ================= */
            $table->longText('benefits_title')->nullable();      // HTML string
            $table->text('benefits_subtitle')->nullable();       // Simple text
            $table->json('benefits')->nullable();                // Array of benefit objects

            /* ================= CTA SECTION ================= */
            $table->longText('cta_title')->nullable();           // HTML string
            $table->longText('cta_description')->nullable();     // TipTap HTML content
            $table->string('cta_button_text')->nullable();

            /* ================= FORM SECTION ================= */
            $table->text('form_title')->nullable();              // Simple text or HTML string

            /* ================= LEGACY / BACKWARD COMPAT ================= */
            $table->longText('heading')->nullable();
            $table->longText('content')->nullable();
            $table->string('banner')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('become_instructor_contents');
    }
};
