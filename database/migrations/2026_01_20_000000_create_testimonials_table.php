<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates testimonials table for student reviews/testimonials
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('student_name');
            $table->string('student_role')->nullable();
            $table->string('student_company')->nullable();
            $table->string('course_category'); // e.g., Salesforce, Data Science, Full Stack
            $table->integer('rating')->default(5); // 1-5
            $table->text('testimonial_text');
            $table->string('student_image')->nullable(); // URL to profile image
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0); // For manual ordering
            $table->timestamps();
            
            // Indexes for performance
            $table->index('is_active');
            $table->index('display_order');
            $table->index('course_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};

