<?php


class ProductImporter
{
    private $id;
    private $name;
    private $price;
    private $quantity;

    public function __construct($id, $name, $price, $quantity)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function save()
    {
        $product = new WC_Product();

        $product->set_name($this->name);
        $product->set_description($this->get_description());
        $product->set_short_description($this->get_post_excerpt());

        $product->set_catalog_visibility('visible');
        $product->set_virtual('no');
        $product->set_downloadable('no');

        $product->set_sku(SkuImporter::getGeneratedItemSku());
        $product->set_manage_stock(true);
        $product->set_stock_quantity($this->quantity);

        $product->set_stock_status($this->quantity ? 'instock' : 'outofstock');

        $product->set_regular_price($this->price*1.2);
        $product->set_sale_price($this->price);

        $product->set_attributes($this->get_attributes_array($this->id));

        $product->save();
    }

    public function update()
    {
        // Получаем из базы продукты из списка
        $query_args = array(
            'tax_query' => array(
                array(
                    'taxonomy' => 'pa_1c-id',
                    'field'    => 'slug',
                    'terms'    => $this->id
                )
            )
        );

        $products = wc_get_products($query_args);
        $product = array_shift($products);

        $product->set_regular_price($this->price*1.2);
        $product->set_sale_price($this->price);
        $product->set_stock_quantity($this->quantity);

        $product->save();
    }

    private function get_attributes_array($id_1c)
    {
        $attributes = array('brand', 'color', 'frame_size', 'material', 'speed', 'tormoz', 'type_velo', 'wheel_size', '1c-id');
        $attr_array = array();

        foreach ($attributes as $key => $attribute_item) {
            $attribute = new WC_Product_Attribute();
            $attribute->set_id( $key + 1 );
            $attribute->set_name( 'pa_'.$attribute_item );
            $attribute->set_position( $key + 1 );
            $attribute->set_visible( true );
            $attribute->set_variation( false );
            $attribute->set_options($attribute_item == '1c-id' ? [$id_1c] : []);

            $attr_array[] = $attribute;
        }

        return $attr_array;
    }

    private function get_description()
    {
        return
            'Год выпуска - ;'.PHP_EOL.
            'Максимальный вес пользователя - ;'.PHP_EOL.
            'Вес, кг - ;'.PHP_EOL.
            'Страна производства - Россия;'.PHP_EOL.
            'Материал рамы - ;'.PHP_EOL.
            'Амортизация - ;'.PHP_EOL.
            'Складная конструкция - Нет;'.PHP_EOL.
            'Конструкция вилки - ;'.PHP_EOL.
            'Покрышки - ;'.PHP_EOL.
            'Тип переднего тормоза - ;'.PHP_EOL.
            'Тип заднего тормоза - ;'.PHP_EOL.
            'Регулировка седла - Да;'.PHP_EOL.
            'Регулировка руля - Да;'.PHP_EOL.
            'Материал руля - ';
    }

    private function get_post_excerpt()
    {
        return 'Велосипед, предназначенный для детей в возрасте от двух до четырех лет, без переключения передач. Технические особенности: стальная рама Hi-Ten, жесткая стальная вилка, одинарные алюминиевые обода, ножные педальные тормоза, защита цепи, съемные боковые колеса, багажник, длинные крылья, мягкая накладка на руле, звонок. Подходит для обучения и прогулочного катания в городских условиях.';
    }
}