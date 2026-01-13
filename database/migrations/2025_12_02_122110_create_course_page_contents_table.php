<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 1. Create course_page_contents table (with testimonials fields)
     * 2. Create testimonials table
     */
    public function up(): void
    {
        /**
         * Create course_page_contents table
         */
        Schema::create('course_page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('heading');
            $table->string('subheading');
            $table->string('sidebar_heading');

            // Testimonials section fields
            $table->string('testimonials_heading')->nullable();
            $table->text('testimonials_subheading')->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('course_page_contents');
    }
};
