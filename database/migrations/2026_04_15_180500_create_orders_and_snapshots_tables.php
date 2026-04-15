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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('auth_id')->index();
            $table->text('note')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('weight', 12, 2)->default(0);
            $table->smallInteger('status')->default(0);
            $table->date('finished_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
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

        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('row_id');
            $table->unsignedBigInteger('id')->index();
            $table->unsignedBigInteger('order_id')->index();
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

        Schema::create('order_bracelets', function (Blueprint $table) {
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

        Schema::create('carts_order_carts', function (Blueprint $table) {
            $table->unsignedBigInteger('cart_id')->index();
            $table->unsignedBigInteger('order_cart_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts_order_carts');
        Schema::dropIfExists('order_bracelets');
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('order_carts');
        Schema::dropIfExists('orders');
    }
};
