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
            $table->double('cashbox');  // Касса
            $table->double('cashbox_ret'); // Касса (розница)
            $table->double('income');  // Доход (Касса – Себестоимость)
            $table->double('profit');  //Прибыль ( Доход – Расходы)
            $table->double('income_piece');  //Доля дохода (Валовая прибыль / Себестоимость)
            $table->double('rent');  //Аренда (Виртуальная)
            $table->double('salary'); // Зарплата (Виртуальная)
            $table->double('other_costs'); // Любые другие расходы и затраты
            $table->double('discounts'); // Общая сумма скидок проданного товара
            $table->double('items_sold'); // Кол-во продаж (ед.)
            $table->double('items_sold_ret'); // Кол-во продаж в розницу (ед.)
            $table->double('items_returned'); // Кол-во возвратов (ед.)
            $table->double('unique_sku'); // Кол-во уникальных [SKU] на магазине
            $table->double('area'); // Площадь магазина
            $table->double('dollar_rate'); // Площадь магазина
            $table->double('SUPERINCOME');  // Даже описывать не буду
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
