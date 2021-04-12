<?php


class SkuImport
{
    public static function getProductNotSku()
    {
        $products = wc_get_products(array(
            'sku' => ''
        ));

        print_r(count($products));
    }
}