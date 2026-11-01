<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('pages', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();

      // Localized copy, following the project's {field}_{locale} convention.
      $table->string('title_en')->nullable();
      $table->string('title_ar')->nullable();
      $table->text('intro_en')->nullable();
      $table->text('intro_ar')->nullable();

      // Lets a page exist as a draft without being exposed by the API.
      $table->boolean('is_published')->default(true);
      $table->unsignedSmallInteger('sort_order')->default(0);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pages');
  }
};
