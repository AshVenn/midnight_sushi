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
            $table->morphs('reviewable');
            $table->tinyInteger('rating'); 
            $table->text('comment')->nullable();
            $table->timestamps();

      
            $table->unique(['user_id', 'reviewable_id', 'reviewable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
