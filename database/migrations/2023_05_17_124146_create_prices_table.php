<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_model_id');
            $table->decimal('per_hour_price',10,2)->nullable()->default(NULL);
            $table->decimal('per_week_price',10,2)->nullable()->default(NULL);
            $table->decimal('per_month_price',10,2)->nullable()->default(NULL);
            $table->foreign('car_model_id')->references('id')->on('car_models')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
};
