<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates sample testimonials for testing
     */
    public function run(): void
    {
        $testimonials = [
            [
                'student_name' => 'Priya Sharma',
                'student_role' => 'Senior Software Developer',
                'student_company' => 'Tech Corp',
                'course_category' => 'Full Stack Development',
                'rating' => 5,
                'testimonial_text' => 'The comprehensive curriculum and hands-on projects helped me land my dream job. The instructors are industry experts who provide real-world insights.',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'student_name' => 'Rajesh Kumar',
                'student_role' => 'DevOps Engineer',
                'student_company' => 'Cloud Solutions Inc.',
                'course_category' => 'AWS Cloud Certification',
                'rating' => 5,
                'testimonial_text' => 'Practical training with real-world scenarios. The placement assistance was exceptional - I got multiple job offers within weeks of completion.',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'student_name' => 'Anjali Patel',
                'student_role' => 'Data Analyst',
                'student_company' => 'Analytics Pro',
                'course_category' => 'Data Science & Machine Learning',
                'rating' => 5,
                'testimonial_text' => 'Best investment in my career! The course content is up-to-date with industry standards. The support team is always available to help.',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'student_name' => 'Amit Singh',
                'student_role' => 'Salesforce Developer',
                'student_company' => 'CRM Solutions',
                'course_category' => 'Salesforce Administration',
                'rating' => 5,
                'testimonial_text' => 'Excellent training program! The live sessions and practical assignments prepared me well for certification exams. Highly recommended!',
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'student_name' => 'Sneha Reddy',
                'student_role' => 'UI/UX Designer',
                'student_company' => 'Design Studio',
                'course_category' => 'Web Development',
                'rating' => 5,
                'testimonial_text' => 'The course structure is well-organized and the instructors explain complex concepts clearly. I gained confidence to work on real projects.',
                'is_active' => true,
                'display_order' => 5,
            ],
            [
                'student_name' => 'Vikram Mehta',
                'student_role' => 'Backend Developer',
                'student_company' => 'StartupXYZ',
                'course_category' => 'Node.js & Express',
                'rating' => 5,
                'testimonial_text' => 'Practical approach with industry-relevant projects. The mentorship program helped me understand best practices and career guidance.',
                'is_active' => true,
                'display_order' => 6,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}

