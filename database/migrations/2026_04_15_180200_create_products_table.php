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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('bracelets');
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('ctg_id')->nullable()->index();
            $table->decimal('width', 8, 2);
            $table->decimal('weight', 10, 2);
            $table->decimal('between', 10, 2)->default(0);
            $table->boolean('empty')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
