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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('width', 8, 2);
            $table->decimal('weight', 10, 2);
            $table->string('photo')->nullable();
            $table->text('note')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('weight_total', 12, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bracelets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->index();
            $table->unsignedInteger('height_56')->default(0);
            $table->unsignedInteger('height_58')->default(0);
            $table->unsignedInteger('height_60')->default(0);
            $table->unsignedInteger('height_62')->default(0);
            $table->unsignedInteger('height_64')->default(0);
            $table->unsignedInteger('height_66')->default(0);
            $table->unsignedInteger('height_68')->default(0);
            $table->unsignedInteger('height_70')->default(0);
            $table->unsignedInteger('height_72')->default(0);
            $table->unsignedInteger('height_74')->default(0);
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
        Schema::dropIfExists('bracelets');
        Schema::dropIfExists('carts');
    }
};
