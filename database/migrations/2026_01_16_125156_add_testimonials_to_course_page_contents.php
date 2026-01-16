use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('course_page_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('course_page_contents', 'testimonials_heading')) {
                $table->string('testimonials_heading')->nullable();
            }

            if (!Schema::hasColumn('course_page_contents', 'testimonials_subheading')) {
                $table->text('testimonials_subheading')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('course_page_contents', function (Blueprint $table) {
            $table->dropColumn([
                'testimonials_heading',
                'testimonials_subheading',
            ]);
        });
    }
};
