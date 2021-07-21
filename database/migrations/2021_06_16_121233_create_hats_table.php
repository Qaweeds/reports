<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */



    public function up()
    {
        Schema::create('hats', function (Blueprint $table) {
            $table->id();
            $table->string('store');
            $table->date('date');
            $table->decimal('cashbox');  // Касса
            $table->decimal('cashbox_ret'); // Касса (розница)
            $table->decimal('income');  // Доход (Касса – Себестоимость)
            $table->decimal('profit');  //Прибыль ( Доход – Расходы)
            $table->decimal('income_piece');  //Доля дохода (Валовая прибыль / Себестоимость)
            $table->decimal('rent');  //Аренда (Виртуальная)
            $table->decimal('salary'); // Зарплата (Виртуальная)
            $table->decimal('other_costs'); // дополнительные затраты
            $table->decimal('distributed_costs'); // Распределительные затраты
            $table->decimal('discounts'); // Общая сумма скидок проданного товара
            $table->decimal('items_sold'); // Кол-во продаж (ед.)
            $table->decimal('items_sold_ret'); // Кол-во продаж в розницу (ед.)
            $table->decimal('items_returned'); // Кол-во возвратов (ед.)
            $table->decimal('unique_sku'); // Кол-во уникальных [SKU] на магазине
            $table->decimal('area'); // Площадь магазина
            $table->decimal('dollar_rate'); // Площадь магазина
            $table->decimal('SUPERINCOME');  // Даже описывать не буду
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hats');
    }
}
