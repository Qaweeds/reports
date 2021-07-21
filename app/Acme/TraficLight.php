<?php

namespace App\Acme;

use Carbon\Carbon;

class TraficLight
{
    public function self_price($product, $planned_profitability_coeff, $dollar_rate)
    {
        $gross_profit = $product['Валовая прибыль'];
        $total_arrival_goods_count = $product['суммарный приход товара в шт'];
        $arrival_date = $product['дата прихода партии'];
        $self_price_of_goods_piece = $product['себестоимость штуки товара в грн'];

        $total_days_in_sale = Carbon::now()->subDay()->diffInDays($arrival_date);

        $self_price_of_consignment = ($total_arrival_goods_count * $self_price_of_goods_piece) / $dollar_rate;

        $total_profitability_of_consignment = $gross_profit / $self_price_of_consignment;

        $factual_coeff_of_profitability = $total_profitability_of_consignment / $total_days_in_sale;

        $planned_profit = (
                ($factual_coeff_of_profitability * $self_price_of_consignment) -
                ($planned_profitability_coeff * $self_price_of_consignment)
            ) * $total_days_in_sale;

        $a = $planned_profitability_coeff * 0.95;
        $b = $planned_profitability_coeff * 1.05;

        if ($factual_coeff_of_profitability < $a) $color = 'red';
        elseif ($factual_coeff_of_profitability > $a and $factual_coeff_of_profitability < $b) $color = 'yellow';
        elseif ($factual_coeff_of_profitability > $b) $color = 'green';
        else dd('смотри в формулу расчета себестоимости');

        return [
            'Фактический коэффициент рентабельности' => $factual_coeff_of_profitability,
            'планновая прибыль' => $planned_profit,
            'color' => $color
        ];
    }

}
