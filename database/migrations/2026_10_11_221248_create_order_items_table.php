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
    Schema::create('order_items', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor('order_id')->constrained()->onDelete('cascade');
      $table->foreignIdFor('product_id')->constrained()->onDelete('cascade');
      // size
      $table->string('size');
      // qty
      $table->integer('quantity');
      // unit price
      $table->decimal('unit_price', 10, 2);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('order_items');
  }
};
