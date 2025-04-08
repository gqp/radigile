<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifyMeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notify_me', function (Blueprint $table) {
            $table->id();                           // Primary Key
            $table->string('name');                 // Name of the user
            $table->string('email');                // Email of the user
            $table->string('company')->nullable();  // Company (Optional)
            $table->timestamps();                   // Created at and Updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notify_me');
    }
}
