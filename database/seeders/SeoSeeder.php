<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seo;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // Main Pages
            [
                'id' => 1,
                'slug' => 'home',
                'page_name' => 'Home Page',
                'meta_title' => 'SkillVedika | Best IT Training Institute for SAP',
                'meta_description' => 'SkillVedika offers expert-led IT training in SAP, AWS DevOps...',
                'meta_keywords' => 'SAP training, AWS, Data Science, SkillVedika',
            ],
            [
                'id' => 2,
                'slug' => 'courses',
                'page_name' => 'Course Listing',
                'meta_title' => 'Top Online & Offline Courses to Learn Any Skill | SkillVedika',
                'meta_description' => 'Browse the best online and offline skill-based courses...',
                'meta_keywords' => 'Courses, SkillVedika',
            ],
            [
                'id' => 3,
                'slug' => 'corporate-training',
                'page_name' => 'Corporate Training',
                'meta_title' => 'Corporate Training',
                'meta_description' => 'Learn about our corporate training solutions...',
                'meta_keywords' => 'Corporate Training',
            ],
            [
                'id' => 4,
                'slug' => 'on-job-support',
                'page_name' => 'On Job Support',
                'meta_title' => 'On Job Support',
                'meta_description' => 'Get expert on-job support from SkillVedika...',
                'meta_keywords' => 'Job Support',
            ],
            [
                'id' => 5,
                'slug' => 'about-us',
                'page_name' => 'About Us',
                'meta_title' => 'About SkillVedika | Empowering Skill-Based Learning',
                'meta_description' => 'Learn more about SkillVedika...',
                'meta_keywords' => 'About SkillVedika',
            ],
            [
                'id' => 6,
                'slug' => 'blog',
                'page_name' => 'Blog Listing',
                'meta_title' => 'Best Skill Learning Tips & Career Guides | SkillVedika Blog',
                'meta_description' => 'SkillVedika Blog helps you grow faster...',
                'meta_keywords' => 'Blog, Skill Tips',
            ],
            [
                'id' => 7,
                'slug' => 'contact-us',
                'page_name' => 'Contact Us',
                'meta_title' => 'Contact Us | Get in Touch with SkillVedika',
                'meta_description' => 'Have questions or need help? Contact us...',
                'meta_keywords' => 'Contact, Support',
            ],
            // Services & Programs
            [
                'id' => 18,
                'slug' => 'become-instructor',
                'page_name' => 'Become an Instructor',
                'meta_title' => 'Become an Instructor | SkillVedika – Teach & Share Your Expertise',
                'meta_description' => 'Join SkillVedika as an instructor and share your expertise with learners worldwide. Teach online courses, build your reputation, and earn while making a difference.',
                'meta_keywords' => 'Instructor, Trainer, Teach Online, SkillVedika Instructor',
            ],
            [
                'id' => 22,
                'slug' => 'interview-questions',
                'page_name' => 'Interview Questions',
                'meta_title' => 'Interview Questions by Skill | SkillVedika',
                'meta_description' => 'Browse interview questions for top skills like Python, Salesforce, Java, AI, and more. Prepare for technical interviews with comprehensive Q&A.',
                'meta_keywords' => 'Interview Questions, Technical Interview, Python Interview Questions, Java Interview Questions, JavaScript Interview Questions, Salesforce Interview Questions, Coding Interview, Interview Preparation',
            ],
            // Legal Pages
            [
                'id' => 19,
                'slug' => 'privacy-policy',
                'page_name' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy | SkillVedika',
                'meta_description' => "Read SkillVedika's privacy policy to understand how we collect, use, and protect your personal information.",
                'meta_keywords' => 'Privacy Policy, Data Protection, Privacy, SkillVedika Privacy, User Privacy',
            ],
            [
                'id' => 20,
                'slug' => 'terms-and-conditions',
                'page_name' => 'Terms & Conditions (Student)',
                'meta_title' => 'Student Terms & Conditions | SkillVedika',
                'meta_description' => "Read SkillVedika's student terms and conditions, policies, and legal information for our platform.",
                'meta_keywords' => 'Terms and Conditions, Student Terms, Policy, SkillVedika Terms, Legal Information',
            ],
            [
                'id' => 21,
                'slug' => 'terms-and-conditions-instructor',
                'page_name' => 'Terms & Conditions (Instructor)',
                'meta_title' => 'Instructor Terms & Conditions | SkillVedika',
                'meta_description' => "Read SkillVedika's instructor terms and conditions, policies, and legal information for instructors.",
                'meta_keywords' => 'Instructor Terms, Terms and Conditions, Policy, SkillVedika Instructor Terms',
            ],
        ];

        foreach ($rows as $row) {
            // Use slug as the unique identifier, but keep id for backward compatibility during migration
            Seo::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
