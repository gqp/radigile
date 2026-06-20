<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {

        Schema::create('question_categories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Category name
            $table->text('description')->nullable(); // Optional description for the category
            $table->string('team_domain')->nullable()->change(); //Team Domain
            $table->string('type')->nullable()->change(); // Team Type
            $table->timestamps(); // Created_at and updated_at timestamps
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('text'); // Question text
            $table->unsignedBigInteger('category_id'); // Question belongs to category
            $table->foreign('category_id')->references('id')->on('question_categories')->onDelete('cascade');

            // Tips for levels on a 0–4 scale
            $table->text('tip_0')->nullable(); // Tip for score 0
            $table->text('tip_1')->nullable(); // Tip for score 1
            $table->text('tip_2')->nullable(); // Tip for score 2
            $table->text('tip_3')->nullable(); // Tip for score 3
            $table->text('tip_4')->nullable(); // Tip for score 4

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_categories');

    }
}
