<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating'); // Rating out of 5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']); // One review per user per product
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
