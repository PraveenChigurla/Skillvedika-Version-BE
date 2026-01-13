<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seo;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // -----------------------------
            // MAIN PAGES
            // -----------------------------
            [
                'slug' => 'home',
                'page_name' => 'Home Page',
                'meta_title' => 'SkillVedika | Best IT Training Institute for SAP',
                'meta_description' => 'SkillVedika offers expert-led IT training in SAP, AWS DevOps...',
                'meta_keywords' => 'SAP training, AWS, Data Science, SkillVedika',
            ],
            [
                'slug' => 'courses',
                'page_name' => 'Course Listing',
                'meta_title' => 'Top Online & Offline Courses to Learn Any Skill | SkillVedika',
                'meta_description' => 'Browse the best online and offline skill-based courses...',
                'meta_keywords' => 'Courses, SkillVedika',
            ],
            [
                'slug' => 'corporate-training',
                'page_name' => 'Corporate Training',
                'meta_title' => 'Corporate Training',
                'meta_description' => 'Learn about our corporate training solutions...',
                'meta_keywords' => 'Corporate Training',
            ],
            [
                'slug' => 'on-job-support',
                'page_name' => 'On Job Support',
                'meta_title' => 'On Job Support',
                'meta_description' => 'Get expert on-job support from SkillVedika...',
                'meta_keywords' => 'Job Support',
            ],
            [
                'slug' => 'about-us',
                'page_name' => 'About Us',
                'meta_title' => 'About SkillVedika | Empowering Skill-Based Learning',
                'meta_description' => 'Learn more about SkillVedika...',
                'meta_keywords' => 'About SkillVedika',
            ],
            [
                'slug' => 'blog',
                'page_name' => 'Blog Listing',
                'meta_title' => 'Best Skill Learning Tips & Career Guides | SkillVedika Blog',
                'meta_description' => 'SkillVedika Blog helps you grow faster...',
                'meta_keywords' => 'Blog, Skill Tips',
            ],
            [
                'slug' => 'contact-us',
                'page_name' => 'Contact Us',
                'meta_title' => 'Contact Us | Get in Touch with SkillVedika',
                'meta_description' => 'Have questions or need help? Contact us...',
                'meta_keywords' => 'Contact, Support',
            ],

            // -----------------------------
            // SERVICES & PROGRAMS
            // -----------------------------
            [
                'slug' => 'become-instructor',
                'page_name' => 'Become an Instructor',
                'meta_title' => 'Become an Instructor | SkillVedika – Teach & Share Your Expertise',
                'meta_description' => 'Join SkillVedika as an instructor and share your expertise with learners worldwide.',
                'meta_keywords' => 'Instructor, Trainer, Teach Online, SkillVedika Instructor',
            ],
            [
                'slug' => 'interview-questions',
                'page_name' => 'Interview Questions',
                'meta_title' => 'Interview Questions by Skill | SkillVedika',
                'meta_description' => 'Browse interview questions for top skills like Python, Salesforce, Java, AI, and more.',
                'meta_keywords' => 'Interview Questions, Technical Interview, Python Interview Questions, Java Interview Questions, Salesforce Interview Questions',
            ],

            // -----------------------------
            // LEGAL PAGES
            // -----------------------------
            [
                'slug' => 'privacy-policy',
                'page_name' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy | SkillVedika',
                'meta_description' => "Read SkillVedika's privacy policy to understand how we collect, use, and protect your personal information.",
                'meta_keywords' => 'Privacy Policy, Data Protection, Privacy, SkillVedika Privacy',
            ],
            [
                'slug' => 'terms-and-conditions',
                'page_name' => 'Terms & Conditions (Student)',
                'meta_title' => 'Student Terms & Conditions | SkillVedika',
                'meta_description' => "Read SkillVedika's student terms and conditions, policies, and legal information.",
                'meta_keywords' => 'Terms and Conditions, Student Terms, SkillVedika Terms',
            ],
            [
                'slug' => 'terms-and-conditions-instructor',
                'page_name' => 'Terms & Conditions (Instructor)',
                'meta_title' => 'Instructor Terms & Conditions | SkillVedika',
                'meta_description' => "Read SkillVedika's instructor terms and conditions, policies, and legal information.",
                'meta_keywords' => 'Instructor Terms, SkillVedika Instructor Terms',
            ],
        ];

        foreach ($rows as $row) {
            Seo::updateOrCreate(
                ['slug' => $row['slug']],   // ✅ lookup only
                [
                    'page_name'        => $row['page_name'],
                    'meta_title'       => $row['meta_title'],
                    'meta_description' => $row['meta_description'],
                    'meta_keywords'    => $row['meta_keywords'],
                ]
            );
        }
    }
}
