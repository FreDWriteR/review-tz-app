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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->comment('UUID товара');
            $table->string('category')->comment('Категория товара');
            $table->tinyInteger('is_active')->default(1)->comment('Флаг активности');
            $table->string('name')->default('')->comment('Тип услуги');
            $table->text('description')->nullable()->comment('Описание товара');
            $table->string('thumbnail')->nullable()->comment('Ссылка на картинку');
            $table->float('price')->comment('Цена');
            $table->index('is_active', 'is_active_idx');
            $table->comment('Товары');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
