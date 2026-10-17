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
    Schema::create('product_variants', function (Blueprint $table) {
      $table->id();
      $table->foreignId('product_id')->constrained()->cascadeOnDelete();

      // Option values. size is locale-independent (handoff §4); an empty
      // string means "not applicable", avoiding NULL-uniqueness holes below.
      $table->string('size')->default('');
      $table->string('color_en')->default('');
      $table->string('color_ar')->default('');
      $table->string('color_hex')->nullable();

      $table->string('sku')->unique();
      // Null price falls back to the parent product's price.
      $table->decimal('price', 10, 2)->nullable();
      $table->unsignedInteger('quantity')->default(0);
      $table->boolean('is_active')->default(true);
      $table->unsignedSmallInteger('sort_order')->default(0);

      // One row per size/color combination of a product.
      $table->unique(['product_id', 'size', 'color_en']);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_variants');
  }
};
