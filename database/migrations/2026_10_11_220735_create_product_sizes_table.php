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
    Schema::create('product_sizes', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(\App\Models\Product::class)->constrained()->onDelete('cascade');
      $table->string('size');
      $table->unsignedInteger('quantity')->default(0);

      $table->unique(['product_id', 'size']);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('product_sizes');
  }
};
