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
    Schema::create('page_sections', function (Blueprint $table) {
      $table->id();
      $table->foreignId('page_id')->constrained()->cascadeOnDelete();

      // Block kind: text | stats | contact | social. AboutResource groups rows
      // by type into the matching JSON array.
      $table->string('type')->default('text');

      // Localized label/body, same {field}_{locale} convention as the rest.
      $table->string('heading_en')->nullable();
      $table->string('heading_ar')->nullable();
      $table->text('body_en')->nullable();
      $table->text('body_ar')->nullable();

      // Locale-independent payload: stat value, contact value, or social URL.
      $table->string('value')->nullable();

      $table->unsignedSmallInteger('sort_order')->default(0);
      $table->timestamps();

      $table->index(['page_id', 'type', 'sort_order']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('page_sections');
  }
};
